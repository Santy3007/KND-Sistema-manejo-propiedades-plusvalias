
let deleteId = null;

function openModal(id) {
    deleteId = id;
    document.getElementById("deleteModal").style.display = "block";
}

function closeModal() {
    document.getElementById("deleteModal").style.display = "none";
}

document.getElementById("confirmDelete").onclick = function() {
    window.location.href = 'index.php?controller=bodega&action=delete&id=' + deleteId;
}
