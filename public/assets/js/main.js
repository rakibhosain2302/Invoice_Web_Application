let itemIndex = 1;

//** Calculate single item total
function calculateItemTotal(row) {
    const unitPrice = parseFloat(row.querySelector(".unit-price").value) || 0;
    const quantity = parseInt(row.querySelector(".quantity").value) || 0;
    const total = Math.round(unitPrice * quantity);
    row.querySelector(".total").value = total;
    calculateGrandTotal();
}

//** Calculate grand total
function calculateGrandTotal() {
    const allTotals = document.querySelectorAll(".total");
    let grandTotal = 0;
    allTotals.forEach((input) => {
        grandTotal += parseInt(input.value) || 0;
    });
    document.getElementById("grand-total").value = grandTotal;
    calculateDue(); // update due also
}

// **Calculate due
function calculateDue() {
    const grandTotal =
        parseInt(document.getElementById("grand-total").value) || 0;
    const paid = parseInt(document.getElementById("paid-amount").value) || 0;
    const due = grandTotal - paid;
    document.getElementById("due-amount").value = due >= 0 ? due : 0;
}

//** Calculate Re-Payment Due

function rePaymentCul() {
    const total =
        parseFloat(document.getElementById("re-paygrand-total").value) || 0;
    const alreadyPaid =
        parseFloat(document.getElementById("already-paid").value) || 0;
    const rePaid =
        parseFloat(document.getElementById("paid-amount").value) || 0;

    const repaymentDue = total - (alreadyPaid + rePaid);
    const due = repaymentDue >= 0 ? repaymentDue : "00";

    document.getElementById("due-amount-visible").value = due;
    document.getElementById("due-amount").value = due;

    const dueInput = document.getElementById("due-amount-visible");
    if (repaymentDue > 0) {
        dueInput.classList.remove("text-success");
        dueInput.classList.add("text-danger");
    } else {
        dueInput.classList.remove("text-danger");
        dueInput.classList.add("text-success");
    }
}

document.addEventListener("DOMContentLoaded", function () {
    document
        .getElementById("paid-amount")
        .addEventListener("input", rePaymentCul);
});

// ** Events
document.addEventListener("input", function (e) {
    if (
        e.target.classList.contains("unit-price") ||
        e.target.classList.contains("quantity")
    ) {
        const row = e.target.closest(".invoice-item");
        calculateItemTotal(row);
    }

    if (e.target.id === "paid-amount") {
        calculateDue();
    }
});

$(document).ready(function () {
    $(".select2-no-search").select2({
        minimumResultsForSearch: Infinity,
    });

    $(document).on("change", ".product-select", function () {
        let $row = $(this).closest(".invoice-item");
        let selected = $(this).find("option:selected");
        let price = selected.data("price") || 0;
        let name = selected.data("name") || "";

        $row.find(".unit-price").val(price);
        $row.find(".product-name-hidden").val(name);
    });
});

//** Add item

function generateProductOptions() {
    let options = `<option value="">${placeholders.getAttribute("data-select")}</option>`;
    window.products.forEach((product) => {
        options += `<option value="${product.id}" data-name="${product.name}" data-price="${product.price}">${product.name}</option>`;
    });
    return options;
}

document.getElementById("add-item").addEventListener("click", function () {
    const container = document.getElementById("items-container");
    const placeholders = document.getElementById("placeholders");

    const row = document.createElement("div");
    row.className = "row g-3 invoice-item mb-3";
    row.innerHTML = `
        <div class="col-md-4">
            <select name="items[${
                window.itemIndex
            }][product_id]" class="form-control form-control-sm select2-no-search product-select" data-index="0">
                ${generateProductOptions()}
            </select>
            <input type="hidden" name="items[${
                window.itemIndex
            }][product_name]" class="product-name-hidden" placeholder="${placeholders.getAttribute(
        "data-product-name"
    )}">
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" name="items[${
                window.itemIndex
            }][unit_price]" class="form-control form-control-sm unit-price" placeholder="${placeholders.getAttribute(
        "data-unit-price"
    )}" required>
        </div>
        <div class="col-md-2">
            <input type="number" name="items[${
                window.itemIndex
            }][quantity]" min="1" max="10" class="form-control form-control-sm quantity" placeholder="${placeholders.getAttribute(
        "data-quantity"
    )}" required>
        </div>
        <div class="col-md-2">
            <input type="number" name="items[${
                window.itemIndex
            }][sub_total]" class="form-control form-control-sm total" placeholder="${placeholders.getAttribute(
        "data-subtotal"
    )}" readonly>
        </div>
        <div class="col-md-2 d-grid align-items-end">
            <button type="button" class="btn btn-sm btn-danger remove-item"><i class="bi bi-trash3"></i> ${placeholders.getAttribute(
                "data-remove-btn"
            )}</button>
        </div>
    `;

    container.appendChild(row);
    window.itemIndex++;

    $(row).find(".select2-no-search").select2({
        minimumResultsForSearch: Infinity,
    });
});

//!! Remove item
document.addEventListener("click", function (e) {
    if (e.target.classList.contains("remove-item")) {
        var row = e.target.closest(".invoice-item");
        var itemIdInput = row.querySelector('input[name*="[id]"]');

        if (itemIdInput) {
            var itemId = itemIdInput.value;

            var hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "deleted_items[]";
            hiddenInput.value = itemId;

            document
                .getElementById("deleted-items-container")
                .appendChild(hiddenInput);
        }

        row.remove();
        calculateGrandTotal(); // যদি থাকে
    }
});

document.getElementById("paid_at").value = new Date()
    .toISOString()
    .split("T")[0];

let productIndex = 1;

document.getElementById("add-product").addEventListener("click", function () {
    const container = document.getElementById("product-container");
    const placeholders = document.getElementById("placeholders");

    const product = document.createElement("div");
    product.className = "row g-3 product-item mb-3";

    product.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="products[${productIndex}][product_name]" class="form-control form-control-sm"
                placeholder="${placeholders.getAttribute(
                    "data-product-name"
                )}" required>
        </div>
        <div class="col-md-5">
            <input type="number" step="0.01" name="products[${productIndex}][unit_price]"
                class="form-control form-control-sm unit-price"
                placeholder="${placeholders.getAttribute(
                    "data-unit-price"
                )}" required>
        </div>
        <div class="col-md-2 d-grid align-items-end">
            <button type="button" class="btn btn-sm btn-danger remove-product">
                <i class="bi bi-trash3"></i> ${placeholders.getAttribute(
                    "data-remove-btn"
                )}
            </button>
        </div>
    `;

    container.appendChild(product);
    productIndex++;
});
