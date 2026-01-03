<?php
require_once 'models/FinInstitucion.php';

class FinInstitucionController {
    private $model;

    public function __construct() {
        $this->model = new FinInstitucion();
    }

    public function index() {
        $instituciones = $this->model->getAll();
        require 'views/fininstitucion/index.php';
    }

    public function show($id) {
        $institucion = $this->model->getById($id);
        require 'views/fininstitucion/show.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar que la tasa de interés no sea negativa
            if ($_POST['fin_tasa_interes'] < 0) {
                $error = "La tasa de interés no puede ser negativa.";
            } else {
                try {
                    $this->model->create($_POST);
                    $success = true;
                } catch (Exception $e) {
                    $success = false;
                }
            }
            require 'views/fininstitucion/create.php';
        } else {
            require 'views/fininstitucion/create.php';
        }
    }

    public function edit($id) {
        $institucion = $this->model->getById($id);
    
        if (!$institucion) {
            $error = "La institución financiera no existe.";
            require 'views/fininstitucion/index.php';
            return;
        }
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar que la tasa de interés no sea negativa
            if ($_POST['fin_tasa_interes'] < 0) {
                $error = "La tasa de interés no puede ser negativa.";
            } else {
                try {
                    $this->model->update($id, $_POST);
                    $success = true;
                } catch (Exception $e) {
                    $success = false;
                }
            }
            require 'views/fininstitucion/edit.php';
        } else {
            require 'views/fininstitucion/edit.php';
        }
    }

    public function delete($id) {
        $this->model->delete($id);
        header('Location: index.php?controller=fininstitucion&action=index');
        exit();
    }
}
?>
