<?php
require_once 'config/database.php';

class Provincia {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query('SELECT * FROM provincias ORDER BY provincia_nombre');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM provincias WHERE provincia_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare('INSERT INTO provincias (provincia_nombre) VALUES (?)');
        $stmt->execute([$data['provincia_nombre']]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare('UPDATE provincias SET provincia_nombre = ? WHERE provincia_id = ?');
        $stmt->execute([$data['provincia_nombre'], $id]);
    }

    public function delete($id)
    {
        try {
            // Primero elimina las ciudades asociadas
            $sql = "DELETE FROM ciudades WHERE provincia_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Luego elimina la provincia
            $sql = "DELETE FROM provincias WHERE provincia_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error al eliminar: " . $e->getMessage();
        }
    }


}
?>
