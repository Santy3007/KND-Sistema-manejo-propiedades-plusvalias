document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('sucursalesChart').getContext('2d');
    const sucursalesData = {
        labels: sucursales.map(sucursal => sucursal.nombre), // Nombres de las sucursales
        datasets: [{
            label: 'Cantidad de Sucursales',
            data: Array(sucursales.length).fill(1), // Muestra una barra por sucursal
            backgroundColor: 'rgba(255, 99, 132, 0.2)', // Cambia el color de las barras a rojo (más claro)
            borderColor: 'rgba(255, 99, 132, 1)', // Borde rojo más oscuro
            borderWidth: 1
        }]
    };

    

    const sucursalesChart = new Chart(ctx, {
        type: 'bar',
        data: sucursalesData,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
