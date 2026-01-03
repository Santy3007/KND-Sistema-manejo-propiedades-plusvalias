<?php
require_once 'models/Rol.php';

class RolController {
    private $model;

    public function __construct() {
        $this->model = new Rol();
    }

    public function index() {
        $roles = $this->model->getAll();
        require 'views/rol/index.php';
    }

    public function show($id) {
        $rol = $this->model->getById($id);
        require 'views/rol/show.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->model->create($_POST);
                $success = true; // Bandera de éxito
            } catch (Exception $e) {
                $success = false; // Bandera de error
            }
            require 'views/rol/create.php'; // Volver a la vista con el mensaje
        } else {
            require 'views/rol/create.php';
        }
    }

    public function edit($id) {
        $rol = $this->model->getById($id);
    
        if (!$rol) {
            $error = "El rol no existe.";
            require 'views/rol/index.php';
            return;
        }
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->model->update($id, $_POST);
                $success = true; 
            } catch (Exception $e) {
                $success = false;
            }
            require 'views/rol/edit.php';
        } else {
            require 'views/rol/edit.php';
        }
    }
    

    public function delete($id) {
        $this->model->delete($id);
        header('Location: index.php?controller=rol&action=index');
        exit();
    }
}
?>
