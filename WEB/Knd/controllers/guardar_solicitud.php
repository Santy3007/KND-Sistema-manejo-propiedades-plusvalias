<?php
require_once '../config/database.php';

global $pdo;

// Habilitar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pro_id = isset($_POST['pro_id']) ? intval($_POST['pro_id']) : 0;
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $fecha_cita = isset($_POST['fecha_cita']) ? $_POST['fecha_cita'] : '';
    $mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';

    if ($pro_id > 0 && !empty($nombre) && !empty($correo) && !empty($fecha_cita) && !empty($mensaje)) {
        try {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "INSERT INTO solicitudes (pro_id, nombre, correo, fecha_cita, mensaje) 
                    VALUES (:pro_id, :nombre, :correo, :fecha_cita, :mensaje)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':pro_id', $pro_id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_cita', $fecha_cita, PDO::PARAM_STR);
            $stmt->bindParam(':mensaje', $mensaje, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo "success";
            } else {
                echo "error en la ejecución de la consulta.";
            }
        } catch (PDOException $e) {
            echo "Error SQL: " . $e->getMessage(); // Mostrar error específico
        }
    } else {
        echo "Error: Datos incompletos.";
    }
} else {
    echo "Error: Método no permitido.";
}
?>
