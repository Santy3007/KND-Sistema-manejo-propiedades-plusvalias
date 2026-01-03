function calculateComboPrice() {
    let totalPrice = 0;

    document.querySelectorAll('input[name="prod_ids[]"]:checked').forEach((checkbox) => {
        const parent = checkbox.closest('div');
        const quantityInput = parent.querySelector('input[name="prod_quantities[]"]');
        const quantity = parseInt(quantityInput.value);
        const price = parseFloat(checkbox.getAttribute('data-precio'));
        totalPrice += price * quantity;
    });

    const descuento = parseFloat(document.getElementById('com_descuento').value);
    if (!isNaN(descuento) && descuento > 0 && descuento <= 100) {
        totalPrice = totalPrice * (1 - descuento / 100);
    }

    document.getElementById('com_precio').value = totalPrice.toFixed(2);
}

function toggleQuantity(checkbox) {
    const parent = checkbox.closest('div');
    const quantityInput = parent.querySelector('input[name="prod_quantities[]"]');
    quantityInput.disabled = !checkbox.checked;
    if (!checkbox.checked) {
        quantityInput.value = 1;
    }
    calculateComboPrice();
}
