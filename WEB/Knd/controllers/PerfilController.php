<?php
require_once 'models/Perfil.php';

class PerfilController {
    private $model;

    public function __construct() {
        $this->model = new Perfil();
    }

    public function index() {
        $perfiles = $this->model->getAll();
        require 'views/perfil/index.php';
    }
    


    public function show($id) {
        $perfil = $this->model->getById($id);
        require 'views/perfil/show.php';
    }

    public function create() {
        $success = null; // Bandera de éxito o error
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->model->create($_POST);
                $success = true; // Indica que la creación fue exitosa
            } catch (Exception $e) {
                $success = false; // Indica un error en la creación
            }
        }
        $roles = $this->model->getRoles();
        require 'views/perfil/create.php'; // Volver a la vista con el mensaje
    }
    

    public function edit($id) {
        $success = null; // Bandera de éxito o error
        $perfil = $this->model->getById($id);
    
        // Verificar que el perfil existe y no sea SuperAdmin
        if (!$perfil || $perfil['rol_id'] == 1) {
            header('Location: index.php?controller=perfil&action=index');
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->model->update($id, $_POST);
                $success = true; // Indica éxito en la actualización
            } catch (Exception $e) {
                $success = false; // Indica error en la actualización
            }
        }
    
        $roles = $this->model->getRoles();
        require 'views/perfil/edit.php'; // Volver a la vista con el mensaje
    }
    
    
    public function delete($id) {
        $perfil = $this->model->getById($id);
    
        // Verificar si el perfil es SuperAdmin (rol_id = 1)
        if ($perfil['rol_id'] == 1) {
            header('Location: index.php?controller=perfil&action=index');
            exit();
        }
    
        $this->model->delete($id);
        header('Location: index.php?controller=perfil&action=index');
        exit();
    }

    public function aceptar() {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $this->model->aceptarPerfil($id);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
   }

    
}
?>
