<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Cita.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php?controller=login&action=index');
    exit();
}

$citaModel = new Cita();
$citasCompletadas = $citaModel->getCitasByEstado('completada', $_SESSION['rol_id']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Cita - Simulación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2980b9;
            --secondary-color: #34495e;
            --light-bg: #f8f9fa;
            --card-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        html, body {
            height: 100%;
            margin: 0;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .content-wrapper {
            flex: 1 0 auto; /* Esto permite que el contenido se expanda */
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
            margin-bottom: 2rem;
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
        
        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        
        .table th {
            background-color: rgba(41, 128, 185, 0.1);
            color: var(--secondary-color);
            font-weight: 600;
            padding: 1rem;
            border: none;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-top: 1px solid #e0e6ed;
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(41, 128, 185, 0.05);
        }
        
        .btn-custom-primary {
            background: linear-gradient(to right, var(--primary-color), #3498db);
            border: none;
            border-radius: 0.75rem;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 0.2rem 0.4rem rgba(52, 152, 219, 0.3);
        }
        
        .btn-custom-primary:hover {
            background: linear-gradient(to right, #3498db, var(--primary-color));
            transform: translateY(-3px);
            box-shadow: 0 0.4rem 0.6rem rgba(52, 152, 219, 0.4);
        }
        
        .custom-icon {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        .footer-company {
            background-color: var(--secondary-color);
            color: white;
            text-align: center;
            padding: 1rem 0;
            font-size: 0.9rem;
            flex-shrink: 0; /* Evita que el footer se contraiga */
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #7f8c8d;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #bdc3c7;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="content-wrapper">
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-auto">
                    <i class="fas fa-calendar-check fa-3x"></i>
                </div>
                <div class="col">
                    <h1 class="display-5 fw-bold">Seleccionar Cita para Simulación</h1>
                    <p class="lead mb-0">Elija una cita completada para realizar una simulación financiera</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="custom-card">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-list-alt custom-icon fa-lg"></i>
                <span>Citas Completadas Disponibles</span>
            </div>
            <div class="card-body">
                <?php if (count($citasCompletadas) > 0): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag custom-icon"></i>ID</th>
                                    <th><i class="fas fa-home custom-icon"></i>Propiedad</th>
                                    <th><i class="fas fa-user custom-icon"></i>Cliente</th>
                                    <th><i class="fas fa-calendar-day custom-icon"></i>Fecha</th>
                                    <th><i class="fas fa-cogs custom-icon"></i>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($citasCompletadas as $cita): ?>
                                    <tr>
                                        <td><?php echo $cita['cita_id']; ?></td>
                                        <td><?php echo htmlspecialchars($cita['pro_direccion']); ?></td>
                                        <td><?php echo htmlspecialchars($cita['cita_nombre']); ?></td>
                                        <td><?php echo date("d/m/Y H:i", strtotime($cita['cita_fecha'])); ?></td>
                                        <td>
                                            <a href="simulacion.php?cita_id=<?php echo $cita['cita_id']; ?>" class="btn btn-custom-primary">
                                                <i class="fas fa-calculator me-2"></i>Simular
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h4>No hay citas completadas disponibles</h4>
                        <p>Cuando complete citas con clientes, aparecerán aquí para realizar simulaciones.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="d-flex justify-content-center">
            <a href="javascript:history.back();" class="btn btn-custom-primary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
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
</body>
</html>