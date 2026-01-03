// estilosJava/calculatePrice.js

function calculatePrice() {
    const precioUnitario = parseFloat(document.getElementById('prod_precio_un').value);
    const iva = parseFloat(document.getElementById('prod_iva').value) / 100; // Asegúrate de que el IVA se convierte en decimal

    if (isNaN(iva) || iva < 0 || iva > 1) {
        alert('El IVA debe estar entre 0% y 100%.');
        document.getElementById('prod_precio_co').value = '';
        return;
    }

    if (!isNaN(precioUnitario)) {
        const precioConIva = precioUnitario * (1 + iva);
        document.getElementById('prod_precio_co').value = precioConIva.toFixed(2);
    } else {
        document.getElementById('prod_precio_co').value = '';
    }
}

// Asegúrate de que la función calculatePrice se ejecute cada vez que cambies el precio unitario o el IVA
document.getElementById('prod_precio_un').addEventListener('input', calculatePrice);
document.getElementById('prod_iva').addEventListener('input', calculatePrice);
