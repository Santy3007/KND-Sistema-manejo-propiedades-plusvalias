<?php
require_once 'models/Solicitud.php';

class SolicitudesController {
    private $model;

    public function __construct() {
        $this->model = new Solicitud();
    }

    public function index() {
        $solicitudes = $this->model->getAll();
        require 'views/solicitudes/index.php';
    }

    public function show($id) {
        $solicitud = $this->model->getById($id);
        require 'views/solicitudes/show.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->model->create($_POST);
                $success = true;
            } catch (Exception $e) {
                $success = false;
            }
            require 'views/solicitudes/create.php';
        } else {
            require 'views/solicitudes/create.php';
        }
    }

    public function edit($id) {
        $solicitud = $this->model->getById($id);
    
        if (!$solicitud) {
            $error = "La solicitud no existe.";
            require 'views/solicitudes/index.php';
            return;
        }
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->model->update($id, $_POST);
                $success = true;
            } catch (Exception $e) {
                $success = false;
            }
            require 'views/solicitudes/edit.php';
        } else {
            require 'views/solicitudes/edit.php';
        }
    }

    public function delete($id) {
        $this->model->delete($id);
        header('Location: index.php?controller=solicitudes&action=index');
        exit();
    }
}
?>
