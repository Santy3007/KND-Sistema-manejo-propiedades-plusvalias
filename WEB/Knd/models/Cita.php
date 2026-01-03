<?php
require_once __DIR__ . '/../config/database.php';

class Cita {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT c.*, p.pro_direccion, p.pro_tipo
            FROM citas c
            JOIN pro_propiedades p ON c.pro_id = p.pro_id
            ORDER BY c.cita_fecha
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllByUser($per_id) {
        $stmt = $this->pdo->prepare("
            SELECT c.*, p.pro_direccion, p.pro_tipo
            FROM citas c
            JOIN pro_propiedades p ON c.pro_id = p.pro_id
            WHERE p.per_id = ?
            ORDER BY c.cita_fecha
        ");
        $stmt->execute([$per_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllByProperty($pro_id) {
        $stmt = $this->pdo->prepare("
            SELECT c.*, p.pro_direccion, p.pro_tipo
            FROM citas c
            JOIN pro_propiedades p ON c.pro_id = p.pro_id
            WHERE c.pro_id = ?
            ORDER BY c.cita_fecha
        ");
        $stmt->execute([$pro_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare('
            SELECT c.*, p.pro_direccion, p.pro_tipo 
            FROM citas c
            JOIN pro_propiedades p ON c.pro_id = p.pro_id
            WHERE c.cita_id = ?
        ');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare('INSERT INTO citas 
            (pro_id, cita_nombre, cita_email, cita_telefono, cita_fecha, cita_descripcion, cita_estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?)');
    
        $stmt->execute([
            $data['pro_id'],
            $data['cita_nombre'],
            $data['cita_email'],
            $data['cita_telefono'],
            $data['cita_fecha'],
            $data['cita_descripcion'],
            $data['cita_estado'] ?? 'pendiente'
        ]);
        
        return $this->pdo->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare('UPDATE citas 
            SET pro_id = ?, cita_nombre = ?, cita_email = ?, cita_telefono = ?, 
                cita_fecha = ?, cita_descripcion = ?, cita_estado = ?
            WHERE cita_id = ?');

        $stmt->execute([
            $data['pro_id'],
            $data['cita_nombre'],
            $data['cita_email'],
            $data['cita_telefono'],
            $data['cita_fecha'],
            $data['cita_descripcion'],
            $data['cita_estado'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM citas WHERE cita_id = ?');
        $stmt->execute([$id]);
    }
    
    public function getEventsForCalendar($per_id = null) {
        if ($per_id) {
            $stmt = $this->pdo->prepare("
                SELECT c.cita_id, c.cita_nombre, c.cita_fecha, c.cita_estado, 
                       p.pro_direccion, p.pro_tipo, p.pro_id
                FROM citas c
                JOIN pro_propiedades p ON c.pro_id = p.pro_id
                WHERE p.per_id = ?
                ORDER BY c.cita_fecha
            ");
            $stmt->execute([$per_id]);
        } else {
            $stmt = $this->pdo->query("
                SELECT c.cita_id, c.cita_nombre, c.cita_fecha, c.cita_estado, 
                       p.pro_direccion, p.pro_tipo, p.pro_id
                FROM citas c
                JOIN pro_propiedades p ON c.pro_id = p.pro_id
                ORDER BY c.cita_fecha
            ");
        }
    
        $events = [];
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($results as $row) {
            $color = '#3788d8'; // Azul por defecto
            switch ($row['cita_estado']) {
                case 'confirmada': $color = '#28a745'; break; // Verde
                case 'cancelada': $color = '#dc3545'; break; // Rojo
                case 'completada': $color = '#6c757d'; break; // Gris
            }
    
            $events[] = [
                'id' => $row['cita_id'],
                'title' => $row['cita_nombre'] . ' - ' . $row['pro_direccion'],
                'start' => $row['cita_fecha'],
                'color' => $color,
                'extendedProps' => [
                    'pro_id' => $row['pro_id'],
                    'pro_tipo' => $row['pro_tipo'],
                    'pro_direccion' => $row['pro_direccion'],
                    'estado' => $row['cita_estado']
                ]
            ];
        }
        
        return $events;
    }
    public function getCitasByEstado($estado, $rol_id) {
        $stmt = $this->pdo->prepare("
            SELECT c.*, p.pro_direccion, p.pro_tipo
            FROM citas c
            JOIN pro_propiedades p ON c.pro_id = p.pro_id
            JOIN perfiles u ON p.per_id = u.per_id
            WHERE c.cita_estado = ?
            AND u.rol_id = ?
            ORDER BY c.cita_fecha DESC
        ");
        $stmt->execute([$estado, $rol_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    
}
?>