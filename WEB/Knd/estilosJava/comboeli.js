// comboeli.js

let deleteComboId = null;

function openComboModal(id) {
    deleteComboId = id;
    document.getElementById("comboDeleteModal").style.display = "block"; // Asegúrate de que coincida con la ID del modal
}

function closeComboModal() {
    document.getElementById("comboDeleteModal").style.display = "none"; // Asegúrate de que coincida con la ID del modal
}

document.getElementById("confirmComboDelete").onclick = function() { // Asegúrate de que coincida con la ID del botón
    window.location.href = 'index.php?controller=combo&action=delete&id=' + deleteComboId;
};
