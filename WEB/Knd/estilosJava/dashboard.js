// Función para alternar la visibilidad del sidebar
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const toggleButton = document.getElementById('toggleButton');

    if (sidebar.classList.contains('collapsed')) {
        sidebar.classList.remove('collapsed');
        mainContent.style.marginLeft = '250px';
        toggleButton.innerHTML = '&lsaquo;';
    } else {
        sidebar.classList.add('collapsed');
        mainContent.style.marginLeft = '60px';
        toggleButton.innerHTML = '&rsaquo;';
    }
}

// Cargar la página cuando se hace clic en un enlace del menú
function loadPage(url) {
    document.getElementById('content-frame').src = url;
    localStorage.setItem('currentPage', url); // Guardar la URL en localStorage
}

// Cargar la última página visitada al recargar
window.onload = function() {
    const currentPage = localStorage.getItem('currentPage');
    if (currentPage) {
        document.getElementById('content-frame').src = currentPage;
    }
};

function showSubmenu() {
    const submenu = document.getElementById('submenu');
    submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
}
// estilosJava/dashboard.js

// Cargar la página cuando se hace clic en un enlace del menú
function loadPage(url) {
    document.getElementById('content-frame').src = url;
    localStorage.setItem('currentPage', url); // Guardar la URL en localStorage
}

