<?php
require_once __DIR__ . '/../models/AdminCita.php';

class AdminCitaController {
    private $adminCitaModel;
    

    
    public function __construct() {
        $this->adminCitaModel = new AdminCita();
    }

    // Obtener todas las citas para el calendario
    public function getEvents() {
        // Verifica si la sesión ya está iniciada antes de iniciarla
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            return;
        }
    
        // Verifica si el usuario es administrador
        if (!in_array($_SESSION['rol_id'], [1, 2])) {
            echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
            return;
        }
    
        // Para administradores, mostramos todas las citas sin filtrar
        $events = $this->adminCitaModel->getEventsForCalendar();
    
        header('Content-Type: application/json');
        echo json_encode($events);
    }
    
    // Obtener detalles de una cita por ID
    public function getById() {
        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de cita no especificado']);
            return;
        }

        $cita = $this->adminCitaModel->getById($_GET['id']);
        if ($cita) {
            echo json_encode($cita);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cita no encontrada']);
        }
    }

    // Crear una nueva cita
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'pro_id' => $_POST['pro_id'] ?? null,
                'cita_nombre' => $_POST['cita_nombre'] ?? '',
                'cita_email' => $_POST['cita_email'] ?? '',
                'cita_telefono' => $_POST['cita_telefono'] ?? '',
                'cita_fecha' => $_POST['cita_fecha'] ?? '',
                'cita_descripcion' => $_POST['cita_descripcion'] ?? '',
                'cita_estado' => $_POST['cita_estado'] ?? 'pendiente'
            ];

            if (empty($data['pro_id']) || empty($data['cita_nombre']) || empty($data['cita_email']) || empty($data['cita_fecha'])) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos requeridos deben ser llenados']);
                return;
            }

            $citaId = $this->adminCitaModel->create($data);
            if ($citaId) {
                echo json_encode(['success' => true, 'message' => 'Cita creada con éxito', 'cita_id' => $citaId]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear la cita']);
            }
        }
    }

    // Actualizar una cita existente
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $data = [
                'pro_id' => $_POST['pro_id'] ?? null,
                'cita_nombre' => $_POST['cita_nombre'] ?? '',
                'cita_email' => $_POST['cita_email'] ?? '',
                'cita_telefono' => $_POST['cita_telefono'] ?? '',
                'cita_fecha' => $_POST['cita_fecha'] ?? '',
                'cita_descripcion' => $_POST['cita_descripcion'] ?? '',
                'cita_estado' => $_POST['cita_estado'] ?? 'pendiente'
            ];

            if (empty($data['pro_id']) || empty($data['cita_nombre']) || empty($data['cita_email']) || empty($data['cita_fecha'])) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos requeridos deben ser llenados']);
                return;
            }

            $this->adminCitaModel->update($id, $data);
            echo json_encode(['success' => true, 'message' => 'Cita actualizada con éxito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido o ID de cita no especificado']);
        }
    }

    // Eliminar una cita
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $this->adminCitaModel->delete($id);
            echo json_encode(['success' => true, 'message' => 'Cita eliminada con éxito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de cita no especificado']);
        }
    }

    // Actualizar la fecha de una cita al moverla en el calendario
    public function updateDate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $citaFecha = $_POST['cita_fecha'] ?? '';

            if (empty($citaFecha)) {
                echo json_encode(['success' => false, 'message' => 'Fecha no proporcionada']);
                return;
            }

            $this->adminCitaModel->update($id, ['cita_fecha' => $citaFecha]);
            echo json_encode(['success' => true, 'message' => 'Fecha de la cita actualizada']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de cita no especificado']);
        }
    }
}
?>