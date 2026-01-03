<?php
require_once 'models/Propiedad.php';

class InicioController {
    private $model;

    public function __construct() {
        $this->model = new Propiedad();
    }

    public function index() {
        $propiedades = $this->model->getAll();
        require 'views/inicio/inicio.php';
    }
}
?>
