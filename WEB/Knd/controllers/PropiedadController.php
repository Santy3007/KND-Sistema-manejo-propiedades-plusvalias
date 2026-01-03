<?php
require_once 'models/Propiedad.php';

class PropiedadController {
    private $model;

    public function __construct() {
        $this->model = new Propiedad();
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol_id'])) {
            die("Error: No has iniciado sesión.");
        }
    
        $per_id = $_SESSION['user_id'];
        $rol_id = $_SESSION['rol_id']; // Obtener el rol del usuario autenticado
    
        if ($rol_id == 1) {
            $propiedades = $this->model->getAll(); // Administrador ve todas las propiedades
        } else {
            $propiedades = $this->model->getAllByUser($per_id); // Otros ven solo sus propiedades
        }
    
        require 'views/propiedades/index.php';
    }
    


    public function show($id) {
        $propiedad = $this->model->getById($id);
        require 'views/propiedades/show.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $_POST['per_id'] = $_SESSION['user_id']; // Asignar el ID del usuario autenticado
                $this->model->create($_POST, $_FILES);
                $success = true;
            } catch (Exception $e) {
                $success = false;
            }
            require 'views/propiedades/create.php';
        } else {
            require 'views/propiedades/create.php';
        }
    }
    
    
    

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $propiedadExistente = $this->model->getById($id);
                $imagenes = !empty($_FILES['pro_imagenes']['name'][0]) ? $this->procesarImagenes($_FILES['pro_imagenes']) : $propiedadExistente['pro_imagenes'];
                $pdf = !empty($_FILES['pro_planos']['name']) ? $this->procesarPDF($_FILES['pro_planos']) : $propiedadExistente['pro_planos'];
    
                $this->model->update($id, $_POST, $imagenes, $pdf);
                $success = true; // Éxito
            } catch (Exception $e) {
                $success = false; // Fallo
            }
            require 'views/propiedades/edit.php'; // Recarga la vista después de actualizar
        } else {
            $propiedad = $this->model->getById($id);
            require 'views/propiedades/edit.php';
        }
    }
    
    
    

    public function delete($id) {
        $this->model->delete($id);
        header('Location: index.php?controller=propiedad&action=index');
        exit();
    }

    private function procesarImagenes($imagenes) {
        $rutas = [];
        foreach ($imagenes['tmp_name'] as $key => $tmp_name) {
            $nombreArchivo = time() . '_' . $imagenes['name'][$key];
            move_uploaded_file($tmp_name, "uploads/" . $nombreArchivo);
            $rutas[] = "uploads/" . $nombreArchivo;
        }
        return implode(',', $rutas);
    }

    private function procesarPDF($pdf) {
        $nombreArchivo = time() . '_' . $pdf['name'];
        move_uploaded_file($pdf['tmp_name'], "uploads/" . $nombreArchivo);
        return "uploads/" . $nombreArchivo;
    }

    public function filtrar() {
        global $pdo;
        $whereClauses = [];
        $params = [];
    
        if (!empty($_GET['pro_tipo'])) {
            $whereClauses[] = "p.pro_tipo = ?";
            $params[] = $_GET['pro_tipo'];
        }
    
        if (!empty($_GET['busqueda'])) {
            $whereClauses[] = "(p.pro_descripcion LIKE ? OR p.pro_direccion LIKE ?)";
            $params[] = "%" . $_GET['busqueda'] . "%";
            $params[] = "%" . $_GET['busqueda'] . "%";
        }
    
        $whereSql = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";
    
        $stmt = $pdo->prepare("
            SELECT p.*, c.ciudad_nombre
            FROM pro_propiedades p
            JOIN ciudades c ON p.pro_ciudad = c.ciudad_id
            $whereSql
        ");
    
        $stmt->execute($params);
        $propiedades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        echo json_encode($propiedades);
    }
    

    
}
?>
