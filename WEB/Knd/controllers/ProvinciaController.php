<?php
require_once 'models/Provincia.php';

class ProvinciaController {
    private $provinciaModel;

    public function __construct() {
        $this->provinciaModel = new Provincia();
    }

    public function index() {
        $provincias = $this->provinciaModel->getAll();
        require_once 'views/provincia/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->provinciaModel->create($_POST);
                $success = true;
            } catch (Exception $e) {
                $success = false;
            }
            require_once 'views/provincia/create.php';
        } else {
            require_once 'views/provincia/create.php';
        }
    }

    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->provinciaModel->update($_POST['provincia_id'], $_POST);
                $success = true;
            } catch (Exception $e) {
                $success = false;
            }
            $provincia = $this->provinciaModel->getById($_POST['provincia_id']); // Asegurar que la variable se defina
            require_once 'views/provincia/edit.php';
        } else {
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $provincia = $this->provinciaModel->getById($_GET['id']);
            } else {
                $provincia = null;
            }
            require_once 'views/provincia/edit.php';
        }
    }
    
    public function delete($id) {
        $this->provinciaModel->delete($id);
        header("Location: index.php?controller=provincia&action=index");
    }
}
?>
