<?php
require_once 'config/database.php';

// Asegurar que se recibe el ID de la provincia
if (isset($_POST['provincia_id'])) {
    global $pdo;
    $provincia_id = $_POST['provincia_id'];
    
    try {
        // Consulta para obtener las ciudades de la provincia seleccionada
        $stmt = $pdo->prepare("SELECT * FROM ciudades WHERE provincia_id = ?");
        $stmt->execute([$provincia_id]);
        $ciudades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Generar opciones para el dropdown
        echo "<option value=''>Seleccione una Ciudad</option>";
        foreach ($ciudades as $ciudad) {
            echo "<option value='" . $ciudad['ciudad_id'] . "'>" . $ciudad['ciudad_nombre'] . "</option>";
        }
    } catch (PDOException $e) {
        echo "Error al cargar las ciudades: " . $e->getMessage();
    }
} else {
    echo "<option value=''>Seleccione una Provincia Primero</option>";
}
?>
