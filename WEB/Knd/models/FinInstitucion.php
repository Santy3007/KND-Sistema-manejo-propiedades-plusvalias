<?php
require_once 'config/database.php';

class FinInstitucion {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query('SELECT * FROM fin_instituciones');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM fin_instituciones WHERE fin_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare('INSERT INTO fin_instituciones (fin_nombre, fin_tasa_interes, fin_terminos) VALUES (?, ?, ?)');
        $stmt->execute([$data['fin_nombre'], $data['fin_tasa_interes'], $data['fin_terminos']]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare('UPDATE fin_instituciones SET fin_nombre = ?, fin_tasa_interes = ?, fin_terminos = ? WHERE fin_id = ?');
        $stmt->execute([$data['fin_nombre'], $data['fin_tasa_interes'], $data['fin_terminos'], $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM fin_instituciones WHERE fin_id = ?');
        $stmt->execute([$id]);
    }
}
?>
