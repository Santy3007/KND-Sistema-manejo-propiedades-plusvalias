<?php
require_once 'config/database.php';

class Solicitud {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query('SELECT * FROM solicitudes');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM solicitudes WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare('INSERT INTO solicitudes (pro_id, nombre, correo, fecha_cita, mensaje) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$data['pro_id'], $data['nombre'], $data['correo'], $data['fecha_cita'], $data['mensaje']]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare('UPDATE solicitudes SET pro_id = ?, nombre = ?, correo = ?, fecha_cita = ?, mensaje = ? WHERE id = ?');
        $stmt->execute([$data['pro_id'], $data['nombre'], $data['correo'], $data['fecha_cita'], $data['mensaje'], $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM solicitudes WHERE id = ?');
        $stmt->execute([$id]);
    }
}
?>
