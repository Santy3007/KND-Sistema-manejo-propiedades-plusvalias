<?php
require_once __DIR__ . '/../models/Propiedad.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID de propiedad inválido.']);
    exit();
}

try {
    $propiedadModel = new Propiedad();
    $propiedad = $propiedadModel->getById($_GET['id']);

    if ($propiedad) {
        global $pdo;

        $stmtProvincias = $pdo->query("SELECT provincia_id, provincia_nombre FROM provincias");
        $provincias = $stmtProvincias->fetchAll(PDO::FETCH_KEY_PAIR);

        $stmtCiudades = $pdo->query("SELECT ciudad_id, ciudad_nombre FROM ciudades");
        $ciudades = $stmtCiudades->fetchAll(PDO::FETCH_KEY_PAIR);

        $propiedad['pro_provincia'] = $provincias[$propiedad['pro_provincia']] ?? 'Desconocida';
        $propiedad['pro_ciudad'] = $ciudades[$propiedad['pro_ciudad']] ?? 'Desconocida';

        $propiedad['pro_area_terreno'] = $propiedad['pro_area_terreno'] . 'm2';
        $propiedad['pro_alto_total'] = $propiedad['pro_alto_total'] . 'm';

        $baseURL = 'http://' . $_SERVER['HTTP_HOST'] . '/knd/';

        $imagenes = explode(',', $propiedad['pro_imagenes']);
        foreach ($imagenes as &$imagen) {
            $imagen = $baseURL . $imagen;
        }
        $propiedad['pro_imagenes'] = $imagenes;

        // URL completa del plano
        if (!empty($propiedad['pro_planos'])) {
            $propiedad['pro_planos'] = $baseURL . $propiedad['pro_planos'];
        }

        echo json_encode(['status' => 'success', 'data' => $propiedad]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Propiedad no encontrada.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
