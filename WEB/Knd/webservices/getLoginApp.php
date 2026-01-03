<?php
require_once '../config/database.php';
require_once '../models/Propiedad.php'; // Asegúrate que la ruta sea la correcta

global $pdo;
header("Content-Type: application/json");
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Obtener la acción de la URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

if (empty($action)) {
    echo json_encode(['status' => 'error', 'message' => 'No se especificó acción.']);
    exit;
}

if ($action != 'login') {
    echo json_encode(['status' => 'error', 'message' => 'Acción incorrecta.']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['status' => 'error', 'message' => 'Método incorrecto. Se esperaba POST.']);
    exit;
}

// Leer el cuerpo de la solicitud JSON
$data = json_decode(file_get_contents('php://input'), true);

$per_email = isset($data['per_email']) ? trim($data['per_email']) : '';
$per_password = isset($data['per_password']) ? trim($data['per_password']) : '';

if (!empty($per_email) && !empty($per_password)) {
    try {
        // Buscar usuario en la tabla `perfiles`
        $sql = "SELECT per_id, per_nombre, per_apellido, per_email, per_password, rol_id, per_status 
                FROM perfiles 
                WHERE per_email = :per_email 
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':per_email', $per_email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Comparar la contraseña (¡asegúrate de usar hash en producción!)
            if ($user['per_password'] === $per_password) {
                // Verificar estado del usuario
                if ($user['per_status'] !== 'A') {
                    echo json_encode(['status' => 'error', 'message' => 'Usuario inactivo.']);
                } else {
                    // Instanciar el modelo de Propiedad para obtener las propiedades
                    $propiedadModel = new Propiedad();
                    
                    // Según el rol, se obtienen las propiedades
                    if ($user['rol_id'] == 1) {
                        // Administrador: obtiene todas las propiedades
                        $propiedades = $propiedadModel->getAll();
                    } else {
                        // Otros roles: obtiene solo las propiedades del usuario
                        $propiedades = $propiedadModel->getAllByUser($user['per_id']);
                    }
                    
                    // Procesar las imágenes para todas las propiedades
                    foreach ($propiedades as $key => $propiedad) {
                        // Verificar si hay imágenes
                        if (isset($propiedad['pro_imagenes']) && !empty($propiedad['pro_imagenes'])) {
                            $imagenes = explode(',', $propiedad['pro_imagenes']);
                            $urlsCompletas = [];
                            
                            // Construir URLs completas para cada imagen
                            foreach ($imagenes as $imagen) {
                                if (!empty(trim($imagen))) {
                                    $urlsCompletas[] = 'http://' . $_SERVER['HTTP_HOST'] . '/knd/' . trim($imagen);
                                }
                            }
                            
                            // Asignar el array de URLs
                            $propiedades[$key]['pro_imagenes'] = $urlsCompletas;
                        } else {
                            // Si no hay imágenes, asignar un array vacío
                            $propiedades[$key]['pro_imagenes'] = [];
                        }
                    }
                    
                    // Retornar datos de usuario junto con las propiedades
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Login exitoso.',
                        'user' => [
                            'per_id'      => $user['per_id'],
                            'per_nombre'  => $user['per_nombre'],
                            'per_apellido'=> $user['per_apellido'],
                            'per_email'   => $user['per_email'],
                            'rol_id'      => $user['rol_id']
                        ],
                        'propiedades' => $propiedades
                    ]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Contraseña incorrecta.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos.']);
}
?>