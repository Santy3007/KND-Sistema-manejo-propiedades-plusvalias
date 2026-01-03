// Mostrar la ventana emergente para cambiar la contraseña
function openPasswordModal() {
    document.getElementById("passwordModal").style.display = "block";
}

// Cerrar la ventana emergente
function closePasswordModal() {
    document.getElementById("passwordModal").style.display = "none";
}

// Validar y cambiar la contraseña
document.getElementById("btnConfirmPassword").onclick = function() {
    const newPassword = document.getElementById("new_password").value;
    if (newPassword.length >= 6) {
        document.getElementById("passwordForm").submit();
    } else {
        alert("La contraseña debe tener al menos 6 caracteres.");
    }
};
