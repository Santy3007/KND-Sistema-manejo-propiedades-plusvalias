<?php
require_once 'config/database.php';

class Usuario {
    private $pdo;

    public function __construct() {
        global $pdo;
        if (!$pdo) {
            throw new Exception('Database connection not established.');
        }
        $this->pdo = $pdo;
    }

    public function getUserByEmailAndPassword($email, $password) {
        $stmt = $this->pdo->prepare('SELECT * FROM perfiles WHERE per_email = ? AND per_password = ?');
        $stmt->execute([$email, $password]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmail($email) {
        $stmt = $this->pdo->prepare('SELECT * FROM perfiles WHERE per_email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createSuperAdmin($email, $password) {
        $stmt = $this->pdo->prepare('INSERT INTO perfiles (per_nombre, per_apellido, per_email, per_password, rol_id, per_status) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute(['Super', 'Admin', $email, $password, 1, 'A']);
    }

    public function updatePassword($userId, $newPassword) {
        $stmt = $this->pdo->prepare('UPDATE perfiles SET per_password = ? WHERE per_id = ?');
        $stmt->execute([$newPassword, $userId]);
    }


    public function updatePasswordPlainText($email, $newPassword) {
        $sql = "UPDATE perfiles SET per_password = :password WHERE per_email = :email";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':password' => $newPassword, ':email' => $email]);
    }
    
    
    public function updateStatusToActive($email) {
        $sql = "UPDATE perfiles SET per_status = 'A' WHERE per_email = :email";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':email' => $email]);
    }
    
}
?>
