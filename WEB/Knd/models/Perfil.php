<?php
require_once 'config/database.php';

class Perfil {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query('SELECT p.*, r.rol_nombre FROM perfiles p LEFT JOIN roles r ON p.rol_id = r.rol_id');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM perfiles WHERE per_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare('INSERT INTO perfiles (per_nombre, per_apellido, per_email, per_password, rol_id, per_status) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$data['per_nombre'], $data['per_apellido'], $data['per_email'], $data['per_password'], $data['rol_id'], $data['per_status']]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare('UPDATE perfiles SET per_nombre = ?, per_apellido = ?, per_email = ?, per_password = ?, rol_id = ?, per_status = ? WHERE per_id = ?');
        $stmt->execute([$data['per_nombre'], $data['per_apellido'], $data['per_email'], $data['per_password'], $data['rol_id'], $data['per_status'], $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM perfiles WHERE per_id = ?');
        $stmt->execute([$id]);
    }

    public function getRoles() {
        $stmt = $this->pdo->query("SELECT rol_id, rol_nombre FROM roles WHERE rol_status = 'A'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function aceptarPerfil($id) {
        $stmt = $this->pdo->prepare("UPDATE perfiles SET per_status = 'I' WHERE per_id = ?");
        $stmt->execute([$id]);
    }
    
    
}
?>
