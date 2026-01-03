<?php
require_once 'config/database.php';

class Ciudad {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query('SELECT c.*, p.provincia_nombre 
                                   FROM ciudades c
                                   JOIN provincias p ON c.provincia_id = p.provincia_id
                                   ORDER BY c.ciudad_nombre');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM ciudades WHERE ciudad_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByProvincia($provincia_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM ciudades WHERE provincia_id = ?');
        $stmt->execute([$provincia_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare('INSERT INTO ciudades (ciudad_nombre, provincia_id) VALUES (?, ?)');
        $stmt->execute([$data['ciudad_nombre'], $data['provincia_id']]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare('UPDATE ciudades SET ciudad_nombre = ?, provincia_id = ? WHERE ciudad_id = ?');
        $stmt->execute([$data['ciudad_nombre'], $data['provincia_id'], $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM ciudades WHERE ciudad_id = ?');
        $stmt->execute([$id]);
    }
}
?>
