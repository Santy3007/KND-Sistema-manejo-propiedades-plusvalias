<?php
require_once __DIR__ . '/../models/Propiedad.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

// Validar el parámetro 'pro_tipo'
$tiposValidos = ['Casa', 'Departamento', 'Terreno', 'Local Comercial', 'Otro'];

if (!isset($_GET['pro_tipo']) || !in_array($_GET['pro_tipo'], $tiposValidos)) {
    echo json_encode(['status' => 'error', 'message' => 'Tipo de propiedad inválido o no proporcionado.']);
    exit();
}

$pro_tipo = $_GET['pro_tipo'];

try {
    $propiedadModel = new Propiedad();

    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM pro_propiedades WHERE pro_tipo = ?");
    $stmt->execute([$pro_tipo]);
    $propiedades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener nombres de provincias y ciudades
    $stmtProvincias = $pdo->query("SELECT provincia_id, provincia_nombre FROM provincias");
    $provincias = $stmtProvincias->fetchAll(PDO::FETCH_KEY_PAIR);

    $stmtCiudades = $pdo->query("SELECT ciudad_id, ciudad_nombre FROM ciudades");
    $ciudades = $stmtCiudades->fetchAll(PDO::FETCH_KEY_PAIR);

    $formattedProperties = [];

    foreach ($propiedades as $propiedad) {
        $formattedProperties[] = [
            'pro_id' => $propiedad['pro_id'],
            'pro_tipo' => $propiedad['pro_tipo'],
            'pro_provincia' => $provincias[$propiedad['pro_provincia']] ?? 'Desconocida',
            'pro_ciudad' => $ciudades[$propiedad['pro_ciudad']] ?? 'Desconocida',
            'pro_area_terreno' => $propiedad['pro_area_terreno'] . 'm2',
            'pro_alto_total' => $propiedad['pro_alto_total'] . 'm',
            'pro_celular_propietario' => $propiedad['pro_celular_propietario'],
            'pro_imagenes' => array_map(function ($img) {
                return 'http://' . $_SERVER['HTTP_HOST'] . '/Knd/' . $img;
            }, explode(',', $propiedad['pro_imagenes']))
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $formattedProperties]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
