document.getElementById("welcome").textContent = "Medicine Inventory Dashboard";

let list = document.getElementById("medicineList");
let selectedMedicines = [];

fetch("fetch.php")
  .then((response) => response.json())
  .then((data) => {
    if (data.length === 0) {
      document.getElementById("medicineInfo").textContent =
        "No medicines found";
      return;
    }

    let first = data[0];

    document.getElementById("medicineInfo").textContent =
      first.name +
      " | ₹" +
      first.unit_cost +
      " | Available: " +
      (first.in_stock ? "Yes" : "No");

    let stockValue = first.unit_cost * first.quantity;
    document.getElementById("stockValue").textContent =
      "Stock Value: ₹" + stockValue;

    document.getElementById("reorderStatus").textContent =
      first.quantity < 20 ? "Reorder Required" : "Stock Level OK";

    let categoryText = "";
    switch (first.category) {
      case "tablet":
        categoryText = "Tablet Section";
        break;
      case "syrup":
        categoryText = "Syrup Section";
        break;
      case "injection":
        categoryText = "Injection Section";
        break;
      default:
        categoryText = "General Medicines";
    }
    document.getElementById("categoryDisplay").textContent = categoryText;

    list.innerHTML = "";
    let totalValue = 0;

    for (let i = 0; i < data.length; i++) {
      let li = document.createElement("li");
      li.textContent =
        data[i].name +
        " | ₹" +
        data[i].unit_cost +
        " | " +
        data[i].category +
        " | Qty: " +
        data[i].quantity;
      list.appendChild(li);
      totalValue += data[i].unit_cost * data[i].quantity;
    }

    document.getElementById("totalValue").textContent =
      "Total Inventory Value: ₹" + totalValue;
    document.getElementById("availability").textContent =
      "Medicines loaded successfully";
  });

document.getElementById("selectBtn").addEventListener("click", function () {
  selectedMedicines.push("Paracetamol");
  document.getElementById("selectedCount").textContent =
    "Selected Medicines: " + selectedMedicines.length;
  document.getElementById("message").textContent = "Medicine marked for review";
});

document.getElementById("searchBox").addEventListener("input", function () {
  let value = this.value;

  fetch("search.php?query=" + encodeURIComponent(value))
    .then((response) => response.json())
    .then((data) => {
      list.innerHTML = "";

      for (let i = 0; i < data.length; i++) {
        let li = document.createElement("li");
        li.textContent =
          data[i].name +
          " | ₹" +
          data[i].unit_cost +
          " | " +
          data[i].category +
          " | Qty: " +
          data[i].quantity;
        list.appendChild(li);
      }
    });
});

let cards = document.getElementsByClassName("medicine-card");

for (let i = 0; i < cards.length; i++) {
  cards[i].addEventListener("mouseover", function () {
    this.style.backgroundColor = "#e3f2fd";
  });

  cards[i].addEventListener("mouseout", function () {
    this.style.backgroundColor = "transparent";
  });
}
