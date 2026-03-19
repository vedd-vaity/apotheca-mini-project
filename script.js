document.addEventListener("DOMContentLoaded", function () {

    // 1. Dashboard Logic
    const dashContainer = document.querySelector('.stats-grid');
    if (dashContainer) {
        fetch('fetch.php')
            .then(res => res.json())
            .then(data => {
                const stats = data.stats;
                document.getElementById('totalMedicines').textContent = stats.total;
                document.getElementById('lowStock').textContent = stats.low;
                document.getElementById('expired').textContent = stats.expired;

                const expSoonEl = document.getElementById('expiringSoon');
                if (expSoonEl) expSoonEl.textContent = stats.expiring;

                // Recent Changes logic
                const recentList = document.getElementById('recentChangesList');
                if (recentList && data.recent) {
                    recentList.innerHTML = '';
                    if (data.recent.length === 0) {
                        recentList.innerHTML = '<li class="stock-item loading">No recent records.</li>';
                    } else {
                        data.recent.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'stock-item';

                            let badgeClass = 'badge-neutral';
                            if (item.status === 'New Medicine') badgeClass = 'badge-info';
                            else if (item.status === 'Expired') badgeClass = 'badge-danger';
                            else if (item.status === 'Low Stock' || item.status === 'Expiring Soon') badgeClass = 'badge-warning';

                            li.innerHTML = `
                                <div style="flex:1;">
                                    <span class="med-name" style="display:block;">${item.name}</span>
                                    <small style="color:var(--text-muted); font-size: 0.8em;">Batch: ${item.batch_no || '--'}</small>
                                </div>
                                <span class="badge ${badgeClass}">${item.status}</span>
                                <span class="med-price">Exp: ${item.expiry_date}</span>
                                <span class="badge badge-neutral">Qty: ${item.qty}</span>
                            `;
                            recentList.appendChild(li);
                        });
                    }
                }
            })
            .catch(err => {
                console.error("Failed to load dashboard data", err);
                dashContainer.innerHTML = "<p class='text-danger'>Error loading dashboard data.</p>";
            });
    }

    // 2. Stock Listing & Search Logic
    const stockList = document.getElementById('stockList');
    const searchInput = document.getElementById('searchInput');
    const tagFilter = document.getElementById('tagFilter');
    const supplierFilter = document.getElementById('supplierFilter');
    const categoryFilter = document.getElementById('categoryFilter');

    if (stockList) {
        // Initial load
        loadStock();

        function wrapLoadStock() {
            let s = searchInput ? searchInput.value : '';
            let t = tagFilter ? tagFilter.value : '';
            let sup = supplierFilter ? supplierFilter.value : '';
            let cat = categoryFilter ? categoryFilter.value : '';
            loadStock(s, t, sup, cat);
        }

        if (searchInput) searchInput.addEventListener('input', wrapLoadStock);
        if (tagFilter) tagFilter.addEventListener('change', wrapLoadStock);
        if (supplierFilter) supplierFilter.addEventListener('change', wrapLoadStock);
        if (categoryFilter) categoryFilter.addEventListener('change', wrapLoadStock);
    }

    function loadStock(query = "", tag = "", supplier = "", category = "") {
        let url = `search.php?query=${encodeURIComponent(query)}&tag=${encodeURIComponent(tag)}&supplier=${encodeURIComponent(supplier)}&category=${encodeURIComponent(category)}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                stockList.innerHTML = '';

                if (data.length === 0) {
                    stockList.innerHTML = '<li class="stock-item loading">No medicines found.</li>';
                    return;
                }

                const today = new Date().toISOString().split('T')[0];

                data.forEach(med => {
                    const li = document.createElement('li');
                    li.className = 'stock-item';

                    // Determine Badges
                    let reorderLevel = parseInt(med.reorder_level) || 20;
                    let quantity = parseInt(med.quantity) || 0;

                    let qtyBadge = quantity < reorderLevel
                        ? `<span class="badge badge-warning">Qty: ${quantity} (Low)</span>`
                        : `<span class="badge badge-success">Qty: ${quantity}</span>`;

                    let expiryBadge = "";
                    let diffDays = null;
                    if (med.expiry_date) {
                        const expDate = new Date(med.expiry_date);
                        const currDate = new Date(today);
                        const diffTime = expDate - currDate;
                        diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                        if (diffDays <= 0) {
                            expiryBadge = `<span class="badge badge-danger">Expired</span>`;
                        } else if (diffDays <= 7) {
                            let dayText = diffDays === 1 ? "1 day" : `${diffDays} days`;
                            expiryBadge = `<span class="badge badge-warning">Expiring in ${dayText}</span>`;
                        } else {
                            expiryBadge = `<span class="badge badge-neutral">Exp: ${med.expiry_date}</span>`;
                        }
                    }

                    let supplierBadge = med.supplier_name
                        ? `<span class="badge badge-neutral">Supplier: ${med.supplier_name}</span>`
                        : '';

                    let batchBadge = med.batch_no
                        ? `<span class="badge badge-neutral">Batch: ${med.batch_no}</span>`
                        : '';

                    li.innerHTML = `
                        <span class="med-name">${med.name}</span>
                        <span class="badge badge-info">${med.category}</span>
                        ${batchBadge}
                        ${qtyBadge}
                        ${expiryBadge}
                        ${supplierBadge}
                    `;
                    stockList.appendChild(li);
                });
            })
            .catch(err => {
                console.error("Failed to load stock list", err);
                stockList.innerHTML = '<li class="stock-item loading text-danger">Error loading inventory.</li>';
            });
    }

    // 3. Admin Table Search Logic
    const adminSearchInput = document.getElementById('adminSearchInput');
    const adminTableBody = document.getElementById('adminTableBody');

    if (adminSearchInput && adminTableBody) {
        adminSearchInput.addEventListener('input', function (e) {
            let url = 'search.php?query=' + encodeURIComponent(e.target.value);
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    adminTableBody.innerHTML = '';
                    if (data.length === 0) {
                        adminTableBody.innerHTML = '<tr><td colspan="9" class="text-center">No medicines found.</td></tr>';
                        return;
                    }

                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                    data.forEach(med => {
                        let cat = med.category ? med.category.charAt(0).toUpperCase() + med.category.slice(1) : '';

                        let exp = '--';
                        if (med.expiry_date) {
                            let parts = med.expiry_date.split('-');
                            if (parts.length === 3) exp = `${months[parseInt(parts[1]) - 1]} ${parts[2]}, ${parts[0]}`;
                        }

                        let row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${med.id}</td>
                            <td>${med.name}</td>
                            <td>${med.batch_no || '--'}</td>
                            <td><span class="badge badge-info">${cat}</span></td>
                            <td>${med.quantity}</td>
                            <td>${med.reorder_level || 20}</td>
                            <td>${exp}</td>
                            <td>${med.supplier_name || '--'}</td>
                            <td class="action-cell">
                                <form action="admin.php" method="POST" onsubmit="return confirm('Delete this medicine?');" style="margin:0;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="${med.id}">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        `;
                        adminTableBody.appendChild(row);
                    });
                })
                .catch(err => {
                    console.error("Admin search failed", err);
                    adminTableBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error loading inventory.</td></tr>';
                });
        });
    }

});
