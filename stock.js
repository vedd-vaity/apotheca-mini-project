document.getElementById("welcome").textContent = "Medicine Inventory Dashboard";

let medicineName = "Paracetamol";
let unitCost = 30;
let inStock = true;

document.getElementById("medicineInfo").textContent =
  medicineName + " | ₹" + unitCost + " | Available: " + inStock;

let quantity = 40;
let stockValue = unitCost * quantity;

document.getElementById("stockValue").textContent =
  "Stock Value: ₹" + stockValue;

let reorderMessage;

if (quantity < 20) {
  reorderMessage = "Reorder Required";
} else {
  reorderMessage = "Stock Level OK";
}

document.getElementById("reorderStatus").textContent = reorderMessage;

let category = "tablet";
let categoryText = "";

switch (category) {
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

let medicines = ["Paracetamol", "Ibuprofen", "Amoxicillin", "Cough Syrup"];
let list = document.getElementById("medicineList");

for (let i = 0; i < medicines.length; i++) {
  let li = document.createElement("li");
  li.textContent = medicines[i];
  list.appendChild(li);
}

let selectedMedicines = [];

function selectMedicine(name) {
  selectedMedicines.push(name);
  document.getElementById("selectedCount").textContent =
    "Selected Medicines: " + selectedMedicines.length;
}

let searchName = "JavaScript";
let found = false;

for (let i = 0; i < medicines.length; i++) {
  if (medicines[i] === searchName) {
    found = true;
    break;
  }
}

document.getElementById("availability").textContent = found
  ? "Medicine Available"
  : "Out of Stock";

let batchPrices = [450, 300, 250];

function calculateTotalValue() {
  let sum = 0;
  for (let i = 0; i < batchPrices.length; i++) {
    sum += batchPrices[i];
  }
  document.getElementById("totalValue").textContent =
    "Total Inventory Value: ₹" + sum;
}

calculateTotalValue();

document.getElementById("selectBtn").addEventListener("click", function () {
  selectMedicine("Paracetamol");
  document.getElementById("message").textContent = "Medicine marked for review";
});
document.getElementById("searchBox").addEventListener("input", function () {
  let value = this.value.toLowerCase();
  let items = list.getElementsByTagName("li");

  for (let i = 0; i < items.length; i++) {
    let text = items[i].textContent.toLowerCase();
    items[i].style.display = text.includes(value) ? "list-item" : "none";
  }
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
