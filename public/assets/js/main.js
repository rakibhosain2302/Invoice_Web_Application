let itemIndex = 1;

//** Calculate single item total
function calculateItemTotal($row) {
    const unitPrice = parseFloat($row.find(".unit-price").val()) || 0;
    const quantity = parseInt($row.find(".quantity").val()) || 0;
    const total = Math.round(unitPrice * quantity);
    $row.find(".total").val(total);
    calculateGrandTotal();
}

//** Calculate grand total
function calculateGrandTotal() {
    let grandTotal = 0;
    $(".total").each(function () {
        grandTotal += parseInt($(this).val()) || 0;
    });
    $("#grand-total").val(grandTotal);
    calculateDue();
}

// **Calculate due
function calculateDue() {
    const grandTotal = parseInt($("#grand-total").val()) || 0;
    const paid = parseInt($("#paid-amount").val()) || 0;
    const due = grandTotal - paid;
    $("#due-amount").val(due >= 0 ? due : 0);
}

//** Calculate Re-Payment Due
function rePaymentCul() {
    const total = parseFloat($("#re-paygrand-total").val()) || 0;
    const alreadyPaid = parseFloat($("#already-paid").val()) || 0;
    const rePaid = parseFloat($("#paid-amount").val()) || 0;

    const repaymentDue = total - (alreadyPaid + rePaid);
    const due = repaymentDue >= 0 ? repaymentDue : "00";

    $("#due-amount-visible").val(due);
    $("#due-amount").val(due);

    const $dueInput = $("#due-amount-visible");
    if (repaymentDue > 0) {
        $dueInput.removeClass("text-success").addClass("text-danger");
    } else {
        $dueInput.removeClass("text-danger").addClass("text-success");
    }
}

$(document).ready(function () {
    $("#paid-amount").on("input", rePaymentCul);

    $(document).on("input", ".unit-price, .quantity", function () {
        const $row = $(this).closest(".invoice-item");
        calculateItemTotal($row);
    });

    $(document).on("input", "#paid-amount", function () {
        calculateDue();
    });

    // ** Select Option
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

// ** Add Items
    $("#add-item").on("click", function () {
        const $container = $("#items-container");
        const $placeholders = $("#placeholders");

        let options = `<option value="">${$placeholders.data("select")}</option>`;
        window.products.forEach((product) => {
            options += `<option value="${product.id}" data-name="${product.name}" data-price="${product.price}">${product.name}</option>`;
        });

        const $row = $(`
            <div class="row g-3 invoice-item mb-3">
                <div class="col-md-4">
                    <select name="items[${itemIndex}][product_id]" class="form-control form-control-sm select2-no-search product-select">
                        ${options}
                    </select>
                    <input type="hidden" name="items[${itemIndex}][product_name]" class="product-name-hidden" placeholder="${$placeholders.data("product-name")}">
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" name="items[${itemIndex}][unit_price]" class="form-control form-control-sm unit-price" placeholder="${$placeholders.data("unit-price")}" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="items[${itemIndex}][quantity]" min="1" max="10" class="form-control form-control-sm quantity" placeholder="${$placeholders.data( "quantity")}" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="items[${itemIndex}][sub_total]" class="form-control form-control-sm total" placeholder="${$placeholders.data( "subtotal" )}" readonly>
                </div>
                <div class="col-md-2 d-grid align-items-end">
                    <button type="button" class="btn btn-sm btn-danger remove-item"><i class="bi bi-trash3"></i> ${$placeholders.data(
                        "remove-btn"
                    )}</button>
                </div>
            </div>
        `);

        $container.append($row);
        itemIndex++;

        $row.find(".select2-no-search").select2({
            minimumResultsForSearch: Infinity,
        });
    });
// ** Remove Items
    $(document).on("click", ".remove-item", function () {
        const $row = $(this).closest(".invoice-item");
        const $itemIdInput = $row.find('input[name*="[id]"]');
        if ($itemIdInput.length > 0) {
            const itemId = $itemIdInput.val();
            const $hiddenInput = $(
                `<input type="hidden" name="deleted_items[]" value="${itemId}">`
            );
            $("#deleted-items-container").append($hiddenInput);
        }
        $row.remove();
        calculateGrandTotal();
    });

    $("#paid_at").val(new Date().toISOString().split("T")[0]);

});
