<?php
// Configurar cabecera para que la salida sea JSON
header('Content-Type: application/json');

// Incluir la configuración de la base de datos (ajusta la ruta según tu estructura)
require_once('../config/database.php');

// Verificar que se haya pasado un ID de propiedad en la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Falta el ID de propiedad en la URL.']);
    exit;
}

$propiedadId = intval($_GET['id']);

// Consultar la base de datos para obtener el monto de la propiedad
$stmt = $pdo->prepare("SELECT pro_precio FROM pro_propiedades WHERE pro_id = :pro_id");
$stmt->bindParam(':pro_id', $propiedadId, PDO::PARAM_INT);
$stmt->execute();
$propiedad = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$propiedad) {
    echo json_encode(['error' => 'Propiedad no encontrada.']);
    exit;
}

// Usar el monto definido en la propiedad
$monto = floatval($propiedad['pro_precio']);

// Verificar que se haya enviado el parámetro de institución en POST
if (!isset($_POST['institucion']) || empty($_POST['institucion'])) {
    echo json_encode(['error' => 'Falta el parámetro institucion.']);
    exit;
}

$institucionId = intval($_POST['institucion']);

// Consultar la tasa de interés de la institución financiera
$stmt = $pdo->prepare("SELECT fin_tasa_interes FROM fin_instituciones WHERE fin_id = :institucion");
$stmt->bindParam(':institucion', $institucionId, PDO::PARAM_INT);
$stmt->execute();
$institucion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$institucion) {
    echo json_encode(['error' => 'Institución no encontrada.']);
    exit;
}

// Convertir la tasa (en porcentaje) a decimal
$tasa = floatval($institucion['fin_tasa_interes']) / 100;

// Recoger los demás parámetros enviados por POST

// Plazo en años (requerido)
$plazo = isset($_POST['plazo']) ? intval($_POST['plazo']) : null;

// Recoger la frecuencia de pago: debe ser "mensual" o "trimestral"
if(isset($_POST['frecuencia'])){
    $frecuenciaParam = strtolower(trim($_POST['frecuencia']));
    if($frecuenciaParam === 'mensual'){
         $frecuencia = 12;
    } elseif($frecuenciaParam === 'trimestral'){
         $frecuencia = 4;
    } else {
         echo json_encode(['error' => 'La frecuencia debe ser "mensual" o "trimestral".']);
         exit;
    }
} else {
    // Por defecto se asigna mensual
    $frecuencia = 12;
}

// Método de amortización (opcional, por defecto "frances")
$metodo = isset($_POST['metodo']) ? strtolower($_POST['metodo']) : 'frances';

// Validar que se haya enviado el plazo
if (!$plazo) {
    echo json_encode(['error' => 'Falta el parámetro plazo.']);
    exit;
}

// Calcular el número total de cuotas
$cuotas = $plazo * $frecuencia;

// Calcular la tabla de amortización según el método seleccionado
if ($metodo === 'frances') {
    $tablaAmortizacion = calcularFrances($monto, $cuotas, $tasa / $frecuencia);
} elseif ($metodo === 'aleman') {
    $tablaAmortizacion = calcularAleman($monto, $cuotas, $tasa / $frecuencia);
} else {
    echo json_encode(['error' => 'Método de amortización no válido.']);
    exit;
}

// Calcular totales: pago mensual (primer valor de la tabla), total a pagar y total de intereses
$totalPagar = 0;
$totalIntereses = 0;
$pagoMensual = count($tablaAmortizacion) > 0 ? $tablaAmortizacion[0]['cuota'] : 0;

foreach ($tablaAmortizacion as $fila) {
    $totalPagar += $fila['cuota'];
    $totalIntereses += $fila['interes'];
}

// Preparar la respuesta
$response = [
    'montoPropiedad'  => $monto,
    'tasaInstitucion' => round($tasa * 100, 2), // Se muestra en porcentaje
    'pagoMensual'     => round($pagoMensual, 2),
    'totalPagar'      => round($totalPagar, 2),
    'totalIntereses'  => round($totalIntereses, 2),
    'tablaAmortizacion' => $tablaAmortizacion
];

echo json_encode($response);
exit();

/**
 * Función para calcular la tabla de amortización usando el método Francés.
 */
function calcularFrances($monto, $cuotas, $tasa) {
    $cuota = ($monto * $tasa) / (1 - pow(1 + $tasa, -$cuotas));
    $saldo = $monto;
    $tabla = [];
    
    for ($i = 1; $i <= $cuotas; $i++) {
        $interes = $saldo * $tasa;
        $capital = $cuota - $interes;
        $saldo -= $capital;
        if ($saldo < 0) {
            $saldo = 0;
        }
        $tabla[] = [
            'cuota'   => round($cuota, 2),
            'interes' => round($interes, 2),
            'capital' => round($capital, 2),
            'saldo'   => round($saldo, 2)
        ];
    }
    return $tabla;
}

/**
 * Función para calcular la tabla de amortización usando el método Alemán.
 */
function calcularAleman($monto, $cuotas, $tasa) {
    $saldo = $monto;
    $cuotaCapital = $monto / $cuotas;
    $tabla = [];
    
    for ($i = 1; $i <= $cuotas; $i++) {
        $interes = $saldo * $tasa;
        $cuota = $cuotaCapital + $interes;
        $saldo -= $cuotaCapital;
        if ($saldo < 0) {
            $saldo = 0;
        }
        $tabla[] = [
            'cuota'   => round($cuota, 2),
            'interes' => round($interes, 2),
            'capital' => round($cuotaCapital, 2),
            'saldo'   => round($saldo, 2)
        ];
    }
    return $tabla;
}
