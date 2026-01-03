<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Propiedad.php';
require_once '../../models/Cita.php';
require('../../tcpdf/tcpdf.php'); // Cargar TCPDF

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php?controller=login&action=index');
    exit();
}

// Verificar si se recibió un `cita_id`
if (!isset($_GET['cita_id']) && !isset($_POST['cita_id'])) {
    header('Location: calendar.php');
    exit();
}

$citaModel = new Cita();
$propiedadModel = new Propiedad();

// Obtener la cita y propiedad si se recibe por GET
if (isset($_GET['cita_id'])) {
    $cita_id = $_GET['cita_id'];
    $cita = $citaModel->getById($cita_id);

    if (!$cita || $cita['cita_estado'] !== 'completada') {
        header('Location: calendar.php');
        exit();
    }

    $propiedad = $propiedadModel->getById($cita['pro_id']);
    $propiedad['pro_precio'] = (float) $propiedad['pro_precio']; // Asegurar tipo numérico

    $financiamientoQuery = $pdo->query("SELECT * FROM fin_instituciones");
    $financiamientos = $financiamientoQuery->fetchAll(PDO::FETCH_ASSOC);
}

// ** Generar contrato en PDF si se envió el formulario **
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_compra = $_POST['tipo_compra'];
    $cita_id = $_POST['cita_id'];
    $pro_id = $_POST['pro_id'];

    // Obtener información de la propiedad
    $stmt = $pdo->prepare("SELECT * FROM pro_propiedades WHERE pro_id = ?");
    $stmt->execute([$pro_id]);
    $propiedad = $stmt->fetch(PDO::FETCH_ASSOC);
    $propiedad['pro_precio'] = (float) $propiedad['pro_precio'];

    // Obtener información de la cita
    $stmt = $pdo->prepare("SELECT * FROM citas WHERE cita_id = ?");
    $stmt->execute([$cita_id]);
    $cita = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Configurar la redirección después de generar y descargar el PDF
    $_SESSION['redirect_after_download'] = true;

    // Crear instancia de TCPDF
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    
    // Eliminar cabeceras y pies de página predeterminados
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    $pdf->SetCreator('PlusvaliaBlk');
    $pdf->SetAuthor('PlusvaliaBlk');
    $pdf->SetTitle('Contrato de ' . ($tipo_compra == 'contado' ? 'Compra' : 'Financiamiento'));
    
    // Márgenes
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(true, 15);
    
    // Añadir página
    $pdf->AddPage();
    
    // Definir colores para el diseño
    $colorPrimario = array(41, 128, 185); // Azul
    $colorSecundario = array(52, 73, 94); // Gris oscuro
    $colorFondo = array(236, 240, 241); // Gris claro
    
    // Agregar fondo en el encabezado
    $pdf->Rect(0, 0, $pdf->getPageWidth(), 40, 'F', array(), $colorPrimario);
    
    // Logo y nombre de la empresa
    $pdf->SetY(10);
    $pdf->SetFont('helvetica', 'B', 24);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 10, 'PlusvaliaBlk', 0, 1, 'C');
    $pdf->SetFont('helvetica', 'I', 12);
    $pdf->Cell(0, 8, 'Soluciones Inmobiliarias', 0, 1, 'C');
    
    // Título del documento
    $pdf->SetY(50);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($colorSecundario[0], $colorSecundario[1], $colorSecundario[2]);
    $pdf->Cell(0, 10, 'CONTRATO DE ' . strtoupper($tipo_compra == 'contado' ? 'COMPRA' : 'FINANCIAMIENTO'), 0, 1, 'C');
    
    // Número de contrato
    $numeroContrato = 'CON-' . date('Ymd') . '-' . $cita_id;
    $pdf->SetY(65);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'Contrato N°: ' . $numeroContrato, 0, 1, 'R');
    $pdf->Cell(0, 6, 'Fecha: ' . date('d/m/Y'), 0, 1, 'R');
    
    // Línea divisoria
    $pdf->SetDrawColor($colorPrimario[0], $colorPrimario[1], $colorPrimario[2]);
    $pdf->SetLineWidth(0.5);
    $pdf->Line(15, 80, 195, 80);
    
    // Sección de Información de Propiedad
    $pdf->SetY(85);
    $pdf->SetFillColor($colorFondo[0], $colorFondo[1], $colorFondo[2]);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($colorPrimario[0], $colorPrimario[1], $colorPrimario[2]);
    $pdf->Cell(0, 10, 'INFORMACIÓN DE LA PROPIEDAD', 0, 1, 'L', true);
    
    // Detalles de la propiedad
    $pdf->SetY(100);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetTextColor($colorSecundario[0], $colorSecundario[1], $colorSecundario[2]);
    $pdf->Cell(50, 8, 'Dirección:', 0, 0, 'L');
    $pdf->SetFont('helvetica', '', 11);
    $pdf->MultiCell(0, 8, $propiedad['pro_direccion'], 0, 'L');
    
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(50, 8, 'Tipo de propiedad:', 0, 0, 'L');
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 8, $propiedad['pro_tipo'], 0, 1, 'L');
    
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(50, 8, 'Precio:', 0, 0, 'L');
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 8, '$' . number_format($propiedad['pro_precio'], 2), 0, 1, 'L');
    
    // Información del cliente
    $pdf->SetY(130);
    $pdf->SetFillColor($colorFondo[0], $colorFondo[1], $colorFondo[2]);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($colorPrimario[0], $colorPrimario[1], $colorPrimario[2]);
    $pdf->Cell(0, 10, 'INFORMACIÓN DEL COMPRADOR', 0, 1, 'L', true);
    
    $pdf->SetY(145);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetTextColor($colorSecundario[0], $colorSecundario[1], $colorSecundario[2]);
    $pdf->Cell(50, 8, 'Nombre:', 0, 0, 'L');
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 8, $cita['cita_nombre'] ?: 'Cliente #' . $cita['cliente_id'], 0, 1, 'L');
    
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(50, 8, 'Email:', 0, 0, 'L');
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 8, $cita['cita_email'] ?: 'No disponible', 0, 1, 'L');
    
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(50, 8, 'Teléfono:', 0, 0, 'L');
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 8, $cita['cita_telefono'] ?: 'No disponible', 0, 1, 'L');
    
    // Detalles del tipo de compra
    $pdf->SetY(175);
    $pdf->SetFillColor($colorFondo[0], $colorFondo[1], $colorFondo[2]);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($colorPrimario[0], $colorPrimario[1], $colorPrimario[2]);
    $pdf->Cell(0, 10, 'DETALLES DE LA OPERACIÓN', 0, 1, 'L', true);
    
    $pdf->SetY(190);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetTextColor($colorSecundario[0], $colorSecundario[1], $colorSecundario[2]);
    $pdf->Cell(50, 8, 'Tipo de operación:', 0, 0, 'L');
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 8, ($tipo_compra == 'contado' ? 'Pago en efectivo' : 'Financiamiento bancario'), 0, 1, 'L');
    
    if ($tipo_compra == 'credito') {
        $fin_id = $_POST['fin_id'];
        $plazo = $_POST['plazo'];
        $cuota_mensual = $_POST['cuota_mensual'];

        // Obtener información de la institución financiera
        $stmt = $pdo->prepare("SELECT * FROM fin_instituciones WHERE fin_id = ?");
        $stmt->execute([$fin_id]);
        $institucion = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(50, 8, 'Institución Financiera:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, $institucion['fin_nombre'], 0, 1, 'L');
        
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(50, 8, 'Tasa de interés anual:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, $institucion['fin_tasa_interes'] . '%', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(50, 8, 'Plazo:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, $plazo . ' años (' . ($plazo * 12) . ' meses)', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(50, 8, 'Cuota Mensual:', 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor($colorPrimario[0], $colorPrimario[1], $colorPrimario[2]);
        $pdf->Cell(0, 8, '$' . number_format((float)$cuota_mensual, 2), 0, 1, 'L');
    }
    
    // Términos y condiciones
    $pdf->SetY(225);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($colorPrimario[0], $colorPrimario[1], $colorPrimario[2]);
    $pdf->Cell(0, 10, 'TÉRMINOS Y CONDICIONES', 0, 1, 'L');
    
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor($colorSecundario[0], $colorSecundario[1], $colorSecundario[2]);
    $terminos = "El presente documento constituye un contrato legal entre las partes. La firma del comprador confirma la aceptación de todos los términos y condiciones establecidos para la adquisición de la propiedad descrita. El incumplimiento de cualquiera de las cláusulas puede resultar en la cancelación del acuerdo y posibles penalizaciones.";
    $pdf->MultiCell(0, 5, $terminos, 0, 'J');
    
    // Firmas
    $pdf->SetY(245);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(90, 8, 'Firma del Comprador:', 0, 0, 'L');
    $pdf->Cell(90, 8, 'Firma del Vendedor:', 0, 1, 'L');
    
    $pdf->Ln(15);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->Line(20, 265, 90, 265);
    $pdf->Line(110, 265, 180, 265);
    
    // Pie de página
    $pdf->SetY(270);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->SetTextColor(128, 128, 128);
    $pdf->Cell(0, 5, 'Este documento es generado automáticamente por el sistema de PlusvaliaBlk.', 0, 1, 'C');
    $pdf->Cell(0, 5, 'Para consultas, contacte a nuestro departamento legal al +593 960511346.', 0, 1, 'C');
    
    // Generar y mostrar el PDF
    $pdf->Output('Contrato_' . ($tipo_compra == 'contado' ? 'Compra' : 'Financiamiento') . '.pdf', 'D');
    
    // Redireccionar a la página del calendario después de descargar el PDF
    echo '<script>window.location.href = "http://localhost/knd/views/agentes/calendar.php";</script>';
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulación de Compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #2980b9;
            --secondary-color: #34495e;
            --light-bg: #f8f9fa;
            --card-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color), #1a5276);
            color: white;
            padding: 2.5rem 0;
            border-radius: 0 0 2rem 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
        }
        
        .custom-card {
            border: none;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease;
            overflow: hidden;
        }
        
        .custom-card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background: linear-gradient(to right, var(--primary-color), #3498db);
            color: white;
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-bottom: none;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .property-price {
            font-size: 1.5rem;
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .form-control, .form-select {
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            border: 1px solid #e0e6ed;
        }
        
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(41, 128, 185, 0.25);
            border-color: #3498db;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        .custom-icon {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        .btn-custom-primary {
            background: linear-gradient(to right, var(--primary-color), #3498db);
            border: none;
            border-radius: 0.75rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 0.3rem 0.5rem rgba(52, 152, 219, 0.3);
        }
        
        .btn-custom-primary:hover {
            background: linear-gradient(to right, #3498db, var(--primary-color));
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 0.75rem rgba(52, 152, 219, 0.4);
        }
        
        .btn-custom-secondary {
            background-color: #ecf0f1;
            color: var(--secondary-color);
            border: none;
            border-radius: 0.75rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-custom-secondary:hover {
            background-color: #d6dbdf;
            color: #2c3e50;
        }
        
        .financing-section {
            background-color: var(--light-bg);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-top: 1.5rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .cuota-container {
            background: rgba(41, 128, 185, 0.1);
            border-radius: 1rem;
            padding: 1rem;
        }
        
        .footer-company {
            background-color: var(--secondary-color);
            color: white;
            text-align: center;
            padding: 1rem 0;
            margin-top: 3rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-auto">
                <i class="fas fa-home fa-3x"></i>
            </div>
            <div class="col">
                <h1 class="display-5 fw-bold">Simulación de Compra o Alquiler</h1>
                <p class="lead mb-0">Calcule sus opciones financieras para adquirir esta propiedad</p>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="custom-card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-building custom-icon fa-lg"></i>
                    <span>Detalles de la Propiedad</span>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-map-marker-alt custom-icon"></i>
                        <div>
                            <span class="text-muted small">Dirección</span>
                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($propiedad['pro_direccion']); ?></p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-home custom-icon"></i>
                        <div>
                            <span class="text-muted small">Tipo de Propiedad</span>
                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($propiedad['pro_tipo']); ?></p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-tag custom-icon"></i>
                        <div>
                            <span class="text-muted small">Precio</span>
                            <p class="mb-0 property-price">$<?php echo number_format((float)$propiedad['pro_precio'], 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="custom-card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-user custom-icon fa-lg"></i>
                    <span>Detalles del Cliente</span>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-user-circle custom-icon"></i>
                        <div>
                            <span class="text-muted small">Nombre</span>
                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($cita['cita_nombre'] ?? 'No disponible'); ?></p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-envelope custom-icon"></i>
                        <div>
                            <span class="text-muted small">Email</span>
                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($cita['cita_email'] ?? 'No disponible'); ?></p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-phone-alt custom-icon"></i>
                        <div>
                            <span class="text-muted small">Teléfono</span>
                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($cita['cita_telefono'] ?? 'No disponible'); ?></p>
                        </div>
                    </div>
                    <?php if(!empty($cita['cita_descripcion'])): ?>
                    <div class="d-flex align-items-start">
                        <i class="fas fa-comment-alt custom-icon mt-1"></i>
                        <div>
                            <span class="text-muted small">Descripción</span>
                            <p class="mb-0"><?php echo htmlspecialchars($cita['cita_descripcion']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="custom-card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-calculator custom-icon fa-lg"></i>
                    <span>Opciones de Financiamiento</span>
                </div>
                <div class="card-body">
                    <form action="simulacion.php" method="POST">
                        <input type="hidden" name="pro_id" value="<?php echo $propiedad['pro_id']; ?>">
                        <input type="hidden" name="cita_id" value="<?php echo $cita['cita_id']; ?>">

                        <div class="mb-4">
                            <label for="tipo_compra" class="form-label">
                                <i class="fas fa-money-check-alt custom-icon"></i>
                                Seleccione el tipo de operación:
                            </label>
                            <select class="form-select" id="tipo_compra" name="tipo_compra">
                                <option value="contado">Pago en efectivo</option>
                                <option value="credito">Financiamiento bancario</option>
                            </select>
                        </div>

                        <div id="financiamiento" class="financing-section" style="display: none;">
                            <h5 class="mb-4 text-primary">
                                <i class="fas fa-university me-2"></i>
                                Detalles del Financiamiento
                            </h5>
                            
                            <div class="mb-3">
                                <label for="fin_id" class="form-label">Institución Financiera:</label>
                                <select class="form-select" id="fin_id" name="fin_id">
                                    <?php foreach ($financiamientos as $fin): ?>
                                        <option value="<?php echo $fin['fin_id']; ?>" data-tasa="<?php echo (float)$fin['fin_tasa_interes']; ?>">
                                            <?php echo htmlspecialchars($fin['fin_nombre']); ?> (<?php echo $fin['fin_tasa_interes']; ?>%)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="plazo" class="form-label">Plazo en años:</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="plazo" name="plazo" min="1" max="30" value="10">
                                    <span class="input-group-text">años</span>
                                </div>
                            </div>

                            <div class="mb-3 cuota-container">
                                <label for="cuota_mensual" class="form-label">Cuota Mensual Estimada:</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control fw-bold" id="cuota_mensual" name="cuota_mensual" readonly>
                                    <span class="input-group-text">/mes</span>
                                </div>
                                <div class="form-text text-muted mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Esta es una estimación basada en los parámetros seleccionados.
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <a href="javascript:history.back();" class="btn btn-custom-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver
                            </a>
                            <button type="submit" class="btn btn-custom-primary">
                                <i class="fas fa-file-contract me-2"></i>Generar Contrato
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="footer-company">
    <div class="container">
        <p class="mb-0">
            <i class="fas fa-building me-2"></i>
            PlusvaliaBlk © <?php echo date('Y'); ?> - Soluciones Inmobiliarias
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Mostrar/ocultar sección de financiamiento
document.getElementById("tipo_compra").addEventListener("change", function() {
    const financiamientoSection = document.getElementById("financiamiento");
    financiamientoSection.style.display = this.value === "credito" ? "block" : "none";
    
    // Calcular la cuota al cambiar a crédito
    if (this.value === "credito") {
        calcularCuota();
    }
});

// Añadir event listeners para recalcular cuando cambie la institución o el plazo
document.getElementById("fin_id").addEventListener("change", calcularCuota);
document.getElementById("plazo").addEventListener("change", calcularCuota);
document.getElementById("plazo").addEventListener("input", calcularCuota);

// Función para calcular la cuota mensual
function calcularCuota() {
    // Obtener valores
    const precio = <?php echo $propiedad['pro_precio']; ?>;
    const selectFin = document.getElementById("fin_id");
    const tasaAnual = parseFloat(selectFin.options[selectFin.selectedIndex].getAttribute("data-tasa"));
    const plazoAnios = parseInt(document.getElementById("plazo").value);
    
    // Validar plazo
    if (plazoAnios < 1 || plazoAnios > 30) {
        return;
    }
    
    // Convertir tasa anual a mensual (dividir por 12 y convertir a decimal)
    const tasaMensual = (tasaAnual / 100) / 12;
    
    // Número total de pagos (años * 12 meses)
    const numeroPagos = plazoAnios * 12;
    
    // Calcular la cuota mensual usando la fórmula de amortización
    // Cuota = P * (r * (1 + r)^n) / ((1 + r)^n - 1)
    // Donde P = préstamo, r = tasa mensual, n = número de pagos
    
    const cuota = precio * (tasaMensual * Math.pow(1 + tasaMensual, numeroPagos)) / 
                 (Math.pow(1 + tasaMensual, numeroPagos) - 1);
    
    // Mostrar el resultado formateado en el campo de cuota mensual
    document.getElementById("cuota_mensual").value = cuota.toFixed(2);
}

// Calcular la cuota inicial si el tipo de compra es crédito al cargar la página
window.onload = function() {
    if (document.getElementById("tipo_compra").value === "credito") {
        document.getElementById("financiamiento").style.display = "block";
        calcularCuota();
    }
};
</script>

</body>
</html>