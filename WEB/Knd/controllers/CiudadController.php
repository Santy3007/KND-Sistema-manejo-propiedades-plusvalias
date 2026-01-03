<?php
require_once 'models/Ciudad.php';
require_once 'models/Provincia.php';

class CiudadController {
    private $ciudadModel;
    private $provinciaModel;

    public function __construct() {
        $this->ciudadModel = new Ciudad();
        $this->provinciaModel = new Provincia();
    }

    public function index() {
        $ciudades = $this->ciudadModel->getAll();
        require_once 'views/ciudad/index.php';
    }

    public function create() {
        $provincias = $this->provinciaModel->getAll();
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->ciudadModel->create($_POST);
                $success = true;
            } catch (Exception $e) {
                $success = false;
            }
        }

        require_once 'views/ciudad/create.php';
    }

    public function edit() {
        $provincias = $this->provinciaModel->getAll();
        $success = null;
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['ciudad_id'])) {
                die("Error: No se recibió un ID en la solicitud.");
            }
    
            $ciudad_id = $_POST['ciudad_id'];
    
            try {
                $this->ciudadModel->update($ciudad_id, $_POST);
                $success = true;
            } catch (Exception $e) {
                $success = false;
            }
    
            $ciudad = $this->ciudadModel->getById($ciudad_id);
            require_once 'views/ciudad/edit.php';
            exit;
        }
    
        if (!isset($_GET['id'])) {
            die("Error: No se recibió un ID en la URL.");
        }
    
        $ciudad_id = $_GET['id'];
        $ciudad = $this->ciudadModel->getById($ciudad_id);
    
        if (!$ciudad) {
            die("Error: La ciudad con ID $ciudad_id no existe.");
        }
    
        require_once 'views/ciudad/edit.php';
    }
    
    
    public function delete() {
        $this->ciudadModel->delete($_GET['id']);
        header("Location: index.php?controller=ciudad&action=index");
    }
}
?>
