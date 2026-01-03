let deleteMpId = null;

function openModal(id) {
    deleteMpId = id;
    document.getElementById("deleteModal").style.display = "block";
}

function closeModal() {
    document.getElementById("deleteModal").style.display = "none";
}

document.getElementById("confirmDelete").onclick = function() {
    if (deleteMpId !== null) {
        window.location.href = 'index.php?controller=metodopago&action=delete&id=' + deleteMpId;
    }
};
