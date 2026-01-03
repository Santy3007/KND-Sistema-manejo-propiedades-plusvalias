<?php
require_once __DIR__ . '/../config/database.php';

class Propiedad {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT p.*, 
                   c.ciudad_nombre, 
                   pr.provincia_nombre, 
                   CONCAT(per.per_nombre, ' ', per.per_apellido) AS propietario_nombre
            FROM pro_propiedades p
            JOIN ciudades c ON p.pro_ciudad = c.ciudad_id
            JOIN provincias pr ON p.pro_provincia = pr.provincia_id
            LEFT JOIN perfiles per ON p.per_id = per.per_id
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function getAllByUser($per_id) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, 
                   c.ciudad_nombre, 
                   pr.provincia_nombre, 
                   CONCAT(per.per_nombre, ' ', per.per_apellido) AS propietario_nombre
            FROM pro_propiedades p
            JOIN ciudades c ON p.pro_ciudad = c.ciudad_id
            JOIN provincias pr ON p.pro_provincia = pr.provincia_id
            LEFT JOIN perfiles per ON p.per_id = per.per_id
            WHERE p.per_id = ?
        ");
        $stmt->execute([$per_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
        
    
    

    public function getById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM pro_propiedades WHERE pro_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    

    public function create($data, $files) {
        $planosRuta = $this->uploadFile($files['pro_planos']);
        $imagenesRuta = $this->uploadMultipleImages($files['pro_imagenes']);
    
        $stmt = $this->pdo->prepare('INSERT INTO pro_propiedades 
            (pro_tipo, pro_provincia, pro_ciudad, pro_descripcion, pro_area_terreno, pro_alto_total, pro_disponibilidad, 
             pro_direccion, pro_nombre_propietario, pro_celular_propietario, pro_precio, pro_estado, pro_imagenes, 
             pro_planos, pro_baños, pro_habitaciones, pro_estacionamientos, per_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    
        $pro_estacionamientos = isset($data['pro_estacionamientos']) && $data['pro_estacionamientos'] !== '' 
            ? $data['pro_estacionamientos'] 
            : 'N/A';
    
        $stmt->execute([
            $data['pro_tipo'],
            $data['pro_provincia'],
            $data['pro_ciudad'],
            $data['pro_descripcion'],
            $data['pro_area_terreno'],
            $data['pro_alto_total'],
            $data['pro_disponibilidad'],
            $data['pro_direccion'],
            $data['pro_nombre_propietario'],
            $data['pro_celular_propietario'],
            $data['pro_precio'],
            $data['pro_estado'],
            $imagenesRuta,
            $planosRuta,
            $data['pro_baños'],
            $data['pro_habitaciones'],
            $pro_estacionamientos,
            $data['per_id'] // Se guarda el ID del usuario autenticado
        ]);
    }
    

    public function update($id, $data, $imagenesRuta = null, $planosRuta = null) {
        if (!$id || !is_numeric($id)) {
            throw new Exception("ID no válido.");
        }

        $propiedadExistente = $this->getById($id);

        $imagenesRuta = $imagenesRuta ?: $propiedadExistente['pro_imagenes'];
        $planosRuta = $planosRuta ?: $propiedadExistente['pro_planos'];

        $stmt = $this->pdo->prepare('UPDATE pro_propiedades 
            SET pro_tipo = ?, pro_provincia = ?, pro_ciudad = ?, pro_descripcion = ?, pro_area_terreno = ?, pro_alto_total = ?, pro_disponibilidad = ?, pro_direccion = ?, pro_nombre_propietario = ?, pro_celular_propietario = ?, pro_precio = ?, pro_estado = ?, pro_imagenes = ?, pro_planos = ?, pro_baños = ?, pro_habitaciones = ?, pro_estacionamientos = ? 
            WHERE pro_id = ?');

        $pro_estacionamientos = isset($data['pro_estacionamientos']) && $data['pro_estacionamientos'] !== '' 
        ? $data['pro_estacionamientos'] 
        : 'N/A';


        $stmt->execute([
            $data['pro_tipo'],
            $data['pro_provincia'],
            $data['pro_ciudad'],
            $data['pro_descripcion'],
            $data['pro_area_terreno'],
            $data['pro_alto_total'],
            $data['pro_disponibilidad'],
            $data['pro_direccion'],
            $data['pro_nombre_propietario'],
            $data['pro_celular_propietario'],
            $data['pro_precio'],
            $data['pro_estado'],
            $imagenesRuta,
            $planosRuta,
            $data['pro_baños'],
            $data['pro_habitaciones'],
            $pro_estacionamientos,
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM pro_propiedades WHERE pro_id = ?');
        $stmt->execute([$id]);
    }

    private function uploadFile($file) {
        if (!empty($file['name']) && $file['error'] == UPLOAD_ERR_OK) {
            $targetDir = "uploads/";
            $filePath = $targetDir . time() . '_' . basename($file["name"]);
            if (move_uploaded_file($file["tmp_name"], $filePath)) {
                return $filePath;
            }
        }
        return null;
    }

    private function uploadMultipleImages($files) {
        $targetDir = "uploads/";
        $uploadedPaths = [];

        if (!empty($files['name'][0])) {
            foreach ($files['name'] as $key => $filename) {
                $filePath = $targetDir . time() . '_' . basename($filename);
                if (move_uploaded_file($files["tmp_name"][$key], $filePath)) {
                    $uploadedPaths[] = $filePath;
                }
            }
        }
        return implode(',', $uploadedPaths);
    }
}
?>
