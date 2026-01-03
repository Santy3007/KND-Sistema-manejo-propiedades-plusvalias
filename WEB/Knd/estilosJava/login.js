// estilosJava/login.js

function validateForm() {
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    if (email === "" || password === "") {
        alert("Por favor, completa todos los campos.");
        return false;
    }

    return true;
}
function showErrorModal(message) {
    const errorModal = document.getElementById('errorModal');
    const errorMessage = document.getElementById('errorMessage');
    errorMessage.innerText = message;
    errorModal.style.display = 'block';
}

function closeErrorModal() {
    document.getElementById('errorModal').style.display = 'none';
}

function showLockoutModal(remainingTime) {
    const lockoutModal = document.getElementById('lockoutModal');
    const countdownElement = document.getElementById('lockoutCountdown');
    lockoutModal.style.display = 'block';

    let countdown = remainingTime;
    const interval = setInterval(function () {
        countdown--;
        countdownElement.innerText = countdown;
        if (countdown <= 0) {
            clearInterval(interval);
            closeLockoutModal();  // Cerrar la ventana emergente automáticamente cuando el contador llegue a cero
        }
    }, 1000);
}

function closeLockoutModal() {
    document.getElementById('lockoutModal').style.display = 'none';
}
