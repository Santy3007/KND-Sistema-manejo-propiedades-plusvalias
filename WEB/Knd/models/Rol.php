<?php
require_once 'config/database.php';

class Rol {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query('SELECT * FROM roles');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM roles WHERE rol_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare('INSERT INTO roles (rol_nombre, rol_status) VALUES (?, ?)');
        $stmt->execute([$data['rol_nombre'], $data['rol_status']]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare('UPDATE roles SET rol_nombre = ?, rol_status = ? WHERE rol_id = ?');
        $stmt->execute([$data['rol_nombre'], $data['rol_status'], $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM roles WHERE rol_id = ?');
        $stmt->execute([$id]);
    }
}
?>
