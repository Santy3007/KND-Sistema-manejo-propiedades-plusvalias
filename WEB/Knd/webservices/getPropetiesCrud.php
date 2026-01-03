<?php
require_once '../config/database.php';

global $pdo;

// Habilitar errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Obtener el parámetro de acción de la URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Acción: Listar propiedades
if ($action == 'listar') {
    try {
        $stmt = $pdo->query("SELECT * FROM pro_propiedades");
        $propiedades = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $propiedades]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}


elseif ($action == 'crear' && $_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $per_id = isset($data['per_id']) ? intval($data['per_id']) : 0;
    $pro_tipo = isset($data['pro_tipo']) ? trim($data['pro_tipo']) : '';
    $pro_provincia = isset($data['pro_provincia']) ? trim($data['pro_provincia']) : '';
    $pro_ciudad = isset($data['pro_ciudad']) ? trim($data['pro_ciudad']) : '';
    $pro_descripcion = isset($data['pro_descripcion']) ? trim($data['pro_descripcion']) : '';
    $pro_area_terreno = isset($data['pro_area_terreno']) ? floatval($data['pro_area_terreno']) : 0;
    $pro_alto_total = isset($data['pro_alto_total']) ? floatval($data['pro_alto_total']) : 0;
    $pro_banos = isset($data['pro_baños']) ? intval($data['pro_baños']) : 0;
    $pro_estacionamientos = isset($data['pro_estacionamientos']) ? trim($data['pro_estacionamientos']) : NULL;
    $pro_habitaciones = isset($data['pro_habitaciones']) ? intval($data['pro_habitaciones']) : 0;
    $pro_disponibilidad = isset($data['pro_disponibilidad']) ? trim($data['pro_disponibilidad']) : '';
    $pro_direccion = isset($data['pro_direccion']) ? trim($data['pro_direccion']) : '';
    $pro_precio = isset($data['pro_precio']) ? floatval($data['pro_precio']) : 0;
    $pro_estado = isset($data['pro_estado']) ? trim($data['pro_estado']) : '';
    $pro_celular_propietario = isset($data['pro_celular_propietario']) ? trim($data['pro_celular_propietario']) : '';
    $pro_nombre_propietario = isset($data['pro_nombre_propietario']) ? trim($data['pro_nombre_propietario']) : '';
    $pro_video = isset($data['pro_video']) ? trim($data['pro_video']) : NULL;

    // Aquí se procesa pro_imagenes para que, si hay un solo elemento, se guarde como string.
    if (isset($data['pro_imagenes']) && !empty($data['pro_imagenes'])) {
        if (is_array($data['pro_imagenes']) && count($data['pro_imagenes']) === 1) {
            // Si hay solo un elemento, lo extraemos directamente
            $pro_imagenes = $data['pro_imagenes'][0];
        } else {
            // Si hay varios elementos, se pueden guardar en formato JSON sin escapar las barras
            $pro_imagenes = json_encode($data['pro_imagenes'], JSON_UNESCAPED_SLASHES);
        }
    } else {
        $pro_imagenes = NULL;
    }

    $pro_planos = isset($data['pro_planos']) && !empty($data['pro_planos']) ? trim($data['pro_planos']) : NULL;

    // Verificar si los datos requeridos están presentes
    if ($per_id > 0 && !empty($pro_tipo) && !empty($pro_provincia) && !empty($pro_ciudad) && !empty($pro_descripcion) && $pro_area_terreno > 0 && $pro_alto_total > 0 && $pro_banos > 0 && $pro_precio > 0) {
        try {
            // Consulta SQL para insertar los datos
            $sql = "INSERT INTO pro_propiedades (
                per_id, pro_tipo, pro_provincia, pro_ciudad, pro_descripcion, 
                pro_area_terreno, pro_alto_total, `pro_baños`, pro_estacionamientos, 
                pro_habitaciones, pro_disponibilidad, pro_direccion, pro_precio, 
                pro_estado, pro_imagenes, pro_planos, pro_celular_propietario, 
                pro_nombre_propietario, pro_video) 
            VALUES (:per_id, :pro_tipo, :pro_provincia, :pro_ciudad, :pro_descripcion, 
                    :pro_area_terreno, :pro_alto_total, :pro_banos, :pro_estacionamientos, 
                    :pro_habitaciones, :pro_disponibilidad, :pro_direccion, :pro_precio, 
                    :pro_estado, :pro_imagenes, :pro_planos, :pro_celular_propietario, 
                    :pro_nombre_propietario, :pro_video)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':per_id', $per_id, PDO::PARAM_INT);
            $stmt->bindParam(':pro_tipo', $pro_tipo, PDO::PARAM_STR);
            $stmt->bindParam(':pro_provincia', $pro_provincia, PDO::PARAM_STR);
            $stmt->bindParam(':pro_ciudad', $pro_ciudad, PDO::PARAM_STR);
            $stmt->bindParam(':pro_descripcion', $pro_descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':pro_area_terreno', $pro_area_terreno, PDO::PARAM_STR);
            $stmt->bindParam(':pro_alto_total', $pro_alto_total, PDO::PARAM_STR);
            $stmt->bindParam(':pro_banos', $pro_banos, PDO::PARAM_INT);
            $stmt->bindParam(':pro_estacionamientos', $pro_estacionamientos, PDO::PARAM_STR);
            $stmt->bindParam(':pro_habitaciones', $pro_habitaciones, PDO::PARAM_INT);
            $stmt->bindParam(':pro_disponibilidad', $pro_disponibilidad, PDO::PARAM_STR);
            $stmt->bindParam(':pro_direccion', $pro_direccion, PDO::PARAM_STR);
            $stmt->bindParam(':pro_precio', $pro_precio, PDO::PARAM_STR);
            $stmt->bindParam(':pro_estado', $pro_estado, PDO::PARAM_STR);
            $stmt->bindParam(':pro_imagenes', $pro_imagenes, $pro_imagenes === NULL ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':pro_planos', $pro_planos, $pro_planos === NULL ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':pro_celular_propietario', $pro_celular_propietario, PDO::PARAM_STR);
            $stmt->bindParam(':pro_nombre_propietario', $pro_nombre_propietario, PDO::PARAM_STR);
            $stmt->bindParam(':pro_video', $pro_video, $pro_video === NULL ? PDO::PARAM_NULL : PDO::PARAM_STR);

            // Ejecutar la consulta
            $stmt->execute();
            echo json_encode(['status' => 'success', 'message' => 'Propiedad creada correctamente.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos para crear la propiedad.']);
    }
}



// Acción: Actualizar propiedad
elseif ($action == 'actualizar' && $_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el cuerpo de la solicitud en formato JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // Asignar los valores (los opcionales se asignan a NULL si no se proporcionan)
    $pro_id = isset($data['pro_id']) ? intval($data['pro_id']) : 0;  // ID de la propiedad que se va a actualizar
    $per_id = isset($data['per_id']) ? intval($data['per_id']) : 0;
    $pro_tipo = isset($data['pro_tipo']) ? trim($data['pro_tipo']) : '';
    $pro_provincia = isset($data['pro_provincia']) ? trim($data['pro_provincia']) : '';
    $pro_ciudad = isset($data['pro_ciudad']) ? trim($data['pro_ciudad']) : '';
    $pro_descripcion = isset($data['pro_descripcion']) ? trim($data['pro_descripcion']) : '';
    $pro_area_terreno = isset($data['pro_area_terreno']) ? floatval($data['pro_area_terreno']) : 0;
    $pro_alto_total = isset($data['pro_alto_total']) ? floatval($data['pro_alto_total']) : 0;
    $pro_baños = isset($data['pro_baños']) ? intval($data['pro_baños']) : 0;
    $pro_estacionamientos = isset($data['pro_estacionamientos']) && !empty($data['pro_estacionamientos']) ? trim($data['pro_estacionamientos']) : NULL;
    $pro_habitaciones = isset($data['pro_habitaciones']) ? intval($data['pro_habitaciones']) : 0;
    $pro_disponibilidad = isset($data['pro_disponibilidad']) ? trim($data['pro_disponibilidad']) : '';
    $pro_direccion = isset($data['pro_direccion']) ? trim($data['pro_direccion']) : '';
    $pro_precio = isset($data['pro_precio']) ? floatval($data['pro_precio']) : 0;
    $pro_estado = isset($data['pro_estado']) ? trim($data['pro_estado']) : '';
    $pro_video = isset($data['pro_video']) ? trim($data['pro_video']) : NULL;

    // Manejar pro_imagenes: Si es array, convertirlo en cadena; si es string, usar trim()
    if (isset($data['pro_imagenes']) && !empty($data['pro_imagenes'])) {
        if (is_array($data['pro_imagenes'])) {
            $pro_imagenes = implode(",", $data['pro_imagenes']);
        } else {
            $pro_imagenes = trim($data['pro_imagenes']);
        }
    } else {
        $pro_imagenes = NULL;
    }
    
    $pro_planos = isset($data['pro_planos']) && !empty($data['pro_planos']) ? trim($data['pro_planos']) : NULL;
    $pro_celular_propietario = isset($data['pro_celular_propietario']) ? trim($data['pro_celular_propietario']) : '';
    $pro_nombre_propietario = isset($data['pro_nombre_propietario']) ? trim($data['pro_nombre_propietario']) : '';

    // Validar que el ID de la propiedad esté presente y que se envíen los datos necesarios
    if ($pro_id > 0 && $per_id > 0 && !empty($pro_tipo) && !empty($pro_provincia) && !empty($pro_ciudad) && !empty($pro_descripcion) && $pro_area_terreno > 0 && $pro_alto_total > 0 && $pro_baños > 0 && $pro_precio > 0) {
        try {
            // Consulta para actualizar los datos de la propiedad
            $sql = "UPDATE pro_propiedades SET 
            per_id = :per_id, 
            pro_tipo = :pro_tipo, 
            pro_provincia = :pro_provincia, 
            pro_ciudad = :pro_ciudad, 
            pro_descripcion = :pro_descripcion, 
            pro_area_terreno = :pro_area_terreno, 
            pro_alto_total = :pro_alto_total, 
            pro_baños = :pro_banos, 
            pro_estacionamientos = :pro_estacionamientos, 
            pro_habitaciones = :pro_habitaciones, 
            pro_disponibilidad = :pro_disponibilidad, 
            pro_direccion = :pro_direccion, 
            pro_precio = :pro_precio, 
            pro_estado = :pro_estado, 
            pro_imagenes = :pro_imagenes, 
            pro_planos = :pro_planos, 
            pro_celular_propietario = :pro_celular_propietario, 
            pro_nombre_propietario = :pro_nombre_propietario, 
            pro_video = :pro_video 
        WHERE pro_id = :pro_id";
            
            $stmt = $pdo->prepare($sql);

            // Vincular los parámetros
            $stmt->bindParam(':pro_id', $pro_id, PDO::PARAM_INT);
            $stmt->bindParam(':per_id', $per_id, PDO::PARAM_INT);
            $stmt->bindParam(':pro_tipo', $pro_tipo, PDO::PARAM_STR);
            $stmt->bindParam(':pro_provincia', $pro_provincia, PDO::PARAM_STR);
            $stmt->bindParam(':pro_ciudad', $pro_ciudad, PDO::PARAM_STR);
            $stmt->bindParam(':pro_descripcion', $pro_descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':pro_area_terreno', $pro_area_terreno, PDO::PARAM_STR);
            $stmt->bindParam(':pro_alto_total', $pro_alto_total, PDO::PARAM_STR);
            $stmt->bindParam(':pro_banos', $pro_baños, PDO::PARAM_INT);  // Usamos :pro_banos (sin ñ)
            $stmt->bindParam(':pro_estacionamientos', $pro_estacionamientos, PDO::PARAM_STR);
            $stmt->bindParam(':pro_habitaciones', $pro_habitaciones, PDO::PARAM_INT);
            $stmt->bindParam(':pro_disponibilidad', $pro_disponibilidad, PDO::PARAM_STR);
            $stmt->bindParam(':pro_direccion', $pro_direccion, PDO::PARAM_STR);
            $stmt->bindParam(':pro_precio', $pro_precio, PDO::PARAM_STR);
            $stmt->bindParam(':pro_estado', $pro_estado, PDO::PARAM_STR);
            $stmt->bindParam(':pro_imagenes', $pro_imagenes, PDO::PARAM_STR);
            $stmt->bindParam(':pro_planos', $pro_planos, PDO::PARAM_STR);
            $stmt->bindParam(':pro_celular_propietario', $pro_celular_propietario, PDO::PARAM_STR);
            $stmt->bindParam(':pro_nombre_propietario', $pro_nombre_propietario, PDO::PARAM_STR);
            $stmt->bindParam(':pro_video', $pro_video, $pro_video === NULL ? PDO::PARAM_NULL : PDO::PARAM_STR);

            // Ejecutar la consulta
            $stmt->execute();

            echo json_encode(['status' => 'success', 'message' => 'Propiedad actualizada correctamente.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos para actualizar la propiedad.']);
    }
}


// Acción: Eliminar propiedad
elseif ($action == 'eliminar' && $_SERVER["REQUEST_METHOD"] == "POST") {
    // Leer el cuerpo de la solicitud como JSON
    $inputData = json_decode(file_get_contents("php://input"), true);

    // Verificar si el JSON contiene el pro_id
    if (isset($inputData['pro_id'])) {
        $pro_id = intval($inputData['pro_id']); // Asegurarse de que es un número entero

        if ($pro_id > 0) {
            try {
                $sql = "DELETE FROM pro_propiedades WHERE pro_id = :pro_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':pro_id', $pro_id, PDO::PARAM_INT);
                $stmt->execute();
                echo json_encode(['status' => 'success', 'message' => 'Propiedad eliminada correctamente.']);
            } catch (PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'El ID de la propiedad es inválido.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se especificó el ID de la propiedad.']);
    }
}
// Acción: Listar provincias
elseif ($action == 'listar_provincias') {
    try {
        $stmt = $pdo->query("SELECT provincia_id, provincia_nombre FROM provincias");
        $provincias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $provincias]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// Acción: Listar ciudades por provincia_id
elseif ($action == 'listar_ciudades') {
    $provincia_id = isset($_GET['provincia_id']) ? intval($_GET['provincia_id']) : 0;

    if ($provincia_id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT ciudad_id, ciudad_nombre FROM ciudades WHERE provincia_id = :provincia_id");
            $stmt->bindParam(':provincia_id', $provincia_id, PDO::PARAM_INT);
            $stmt->execute();
            $ciudades = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $ciudades]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'El ID de la provincia es inválido.']);
    }
}
?>
