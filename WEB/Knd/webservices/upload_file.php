<?php
// Define la ruta de destino de la carpeta uploads
$target_dir = $_SERVER['DOCUMENT_ROOT'] . '/Knd/uploads/'; // Usar la ruta absoluta

// Verifica si la carpeta existe, si no, créala
if (!file_exists($target_dir)) {
    if (!mkdir($target_dir, 0777, true)) {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo crear el directorio: ' . $target_dir]);
        exit;
    }
}

// Verifica si se recibió el archivo
if (!isset($_FILES["file"]) || $_FILES["file"]["error"] > 0) {
    echo json_encode(['status' => 'error', 'message' => 'No se recibió el archivo o hubo un error en la subida']);
    exit;
}

// Ruta completa donde se guardará el archivo
$target_file = $target_dir . basename($_FILES["file"]["name"]);

// Verifica si el archivo ya existe
if (file_exists($target_file)) {
    echo json_encode(['status' => 'error', 'message' => 'El archivo ya existe: ' . $target_file]);
    exit;
}

// Intenta mover el archivo subido a la ruta de destino
if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    echo json_encode(['status' => 'success', 'file' => $target_file, 'message' => 'Archivo subido correctamente']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al mover el archivo', 'error' => error_get_last(), 'path' => $target_file]);
}
?>