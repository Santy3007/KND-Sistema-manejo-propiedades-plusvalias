<?php
require_once 'models/Usuario.php';

class LoginController {
    private $model;

    public function __construct() {
        $this->model = new Usuario();
        $this->createSuperAdminIfNotExists(); // Check and create SuperAdmin if not exists
    }

    private function createSuperAdminIfNotExists() {
        $superAdminEmail = 'supersuper@admin.com';
        $superAdmin = $this->model->getUserByEmail($superAdminEmail);

        if (!$superAdmin) {
            $this->model->createSuperAdmin($superAdminEmail, 'admin');
        }
    }

    private function startSessionIfNotStarted() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        $this->startSessionIfNotStarted();
        if (isset($_SESSION['lockout_time']) && time() < $_SESSION['lockout_time']) {
            $remainingTime = $_SESSION['lockout_time'] - time();
            require 'views/login/index.php';
            echo "<script>showLockoutModal($remainingTime);</script>";
        } else {
            require 'views/login/index.php';
        }
    }

    public function authenticate() {
        $this->startSessionIfNotStarted();
    
        if (isset($_SESSION['lockout_time']) && time() < $_SESSION['lockout_time']) {
            $remainingTime = $_SESSION['lockout_time'] - time();
            require 'views/login/index.php';
            echo "<script>showLockoutModal($remainingTime);</script>";
            return;
        }
    
        $email = $_POST['email'];
        $password = $_POST['password'];
    
        $user = $this->model->getUserByEmailAndPassword($email, $password);
    
        if ($user) {
            if ($user['per_status'] === 'P') {
                require 'views/login/index.php';
                echo "<script>showErrorModal('Su cuenta está en revisión. No puede iniciar sesión hasta que sea aprobada.');</script>";
                return;
            }
    
            if ($user['per_status'] === 'I') {
                header("Location: index.php?controller=login&action=changePasswordForm&email=" . urlencode($email));
                exit();
            }
    
            $_SESSION['user_id'] = $user['per_id'];
            $_SESSION['user_name'] = $user['per_nombre'];
            $_SESSION['per_email'] = $user['per_email'];
            $_SESSION['rol_id'] = $user['rol_id']; 
            unset($_SESSION['login_attempts']);
    
            // Redirigir si el usuario es un agente (rol_id = 20)
            if ($user['rol_id'] == 20) {
                header('Location: views/agentes/index.php');
            } else {
                header('Location: index.php?controller=dashboard&action=index');
            }
            exit();
        } else {
            if (!isset($_SESSION['login_attempts'])) {
                $_SESSION['login_attempts'] = 0;
            }
            $_SESSION['login_attempts']++;
    
            if ($_SESSION['login_attempts'] >= 3) {
                $_SESSION['lockout_time'] = time() + 10;
                $remainingTime = 10;
                require 'views/login/index.php';
                echo "<script>showLockoutModal($remainingTime);</script>";
            } else {
                require 'views/login/index.php';
                echo "<script>showErrorModal('Credenciales incorrectas. Intento " . $_SESSION['login_attempts'] . " de 3.');</script>";
            }
        }
    }
    
    

    public function changePasswordForm() {
        require 'views/login/change_password.php';
    }

    public function changePassword() {
        $this->startSessionIfNotStarted();
        $newPassword = $_POST['new_password'];
        $email = $_POST['email'];
        
        if ($this->model->updatePasswordPlainText($email, $newPassword)) {
            $this->model->updateStatusToActive($email);
            echo "<script>alert('Contraseña actualizada correctamente. Estado cambiado a Activo.'); window.location.href='index.php?controller=login&action=index';</script>";
        } else {
            echo "<script>alert('Error al actualizar la contraseña.'); window.history.back();</script>";
        }
    }

    public function logout() {
        $this->startSessionIfNotStarted();
        session_destroy();
        header('Location: index.php?controller=login&action=index');
        exit();
    }
}
?>
