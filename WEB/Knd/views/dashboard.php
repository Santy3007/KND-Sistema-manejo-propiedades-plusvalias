<?php
require_once 'config/database.php'; 
global $pdo;

try {
    $totalPropiedades = $pdo->query("SELECT COUNT(*) FROM pro_propiedades")->fetchColumn();
    $totalSolicitudes = $pdo->query("SELECT COUNT(*) FROM solicitudes")->fetchColumn();
    $totalEmpresas = $pdo->query("SELECT COUNT(*) FROM empresas")->fetchColumn();
    $totalUsuarios = $pdo->query("SELECT COUNT(*) FROM perfiles WHERE per_status = 'A'")->fetchColumn(); 

 
    $queryTipos = $pdo->query("
        SELECT pro_tipo, COUNT(*) AS total 
        FROM pro_propiedades 
        GROUP BY pro_tipo
    ");
    $propiedadesPorTipo = $queryTipos->fetchAll(PDO::FETCH_ASSOC);

    $labelsTipos = [];
    $dataTipos = [];

    foreach ($propiedadesPorTipo as $p) {
        $labelsTipos[] = '"' . $p['pro_tipo'] . '"';
        $dataTipos[] = $p['total'];
    }

    $labelsTipos = implode(',', $labelsTipos);
    $dataTipos = implode(',', $dataTipos);

    $queryDisponibilidad = $pdo->query("
        SELECT pro_disponibilidad, COUNT(*) AS total 
        FROM pro_propiedades 
        GROUP BY pro_disponibilidad
    ");
    $propiedadesPorDisponibilidad = $queryDisponibilidad->fetchAll(PDO::FETCH_ASSOC);

    $labelsDisponibilidad = [];
    $dataDisponibilidad = [];

    foreach ($propiedadesPorDisponibilidad as $p) {
        $labelsDisponibilidad[] = '"' . $p['pro_disponibilidad'] . '"';
        $dataDisponibilidad[] = $p['total'];
    }

    $labelsDisponibilidad = implode(',', $labelsDisponibilidad);
    $dataDisponibilidad = implode(',', $dataDisponibilidad);

    $queryUltimasPropiedades = $pdo->query("
        SELECT pro_tipo, pro_direccion, pro_precio, 
               SUBSTRING_INDEX(pro_imagenes, ',', 1) AS primera_imagen 
        FROM pro_propiedades 
        ORDER BY pro_id DESC 
        LIMIT 3
    ");
    $ultimasPropiedades = $queryUltimasPropiedades->fetchAll(PDO::FETCH_ASSOC);

    $queryPrecios = $pdo->query("
        SELECT pro_provincia, ROUND(AVG(pro_precio), 2) AS precio_promedio
        FROM pro_propiedades
        GROUP BY pro_provincia
        ORDER BY precio_promedio DESC
        LIMIT 5
    ");

    $preciosPorProvincia = $queryPrecios->fetchAll(PDO::FETCH_ASSOC);

    $labelsPrecios = [];
    $dataPrecios = [];

    foreach ($preciosPorProvincia as $p) {
        $labelsPrecios[] = '"' . $p['pro_provincia'] . '"';
        $dataPrecios[] = $p['precio_promedio'];
    }

    $labelsPrecios = implode(',', $labelsPrecios);
    $dataPrecios = implode(',', $dataPrecios);

} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>PlusvaliaBlk</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="http://localhost/knd/index.php?controller=dashboard&action=index">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Plusvalia<sup>Blk</sup></div>
            </a>

            <hr class="sidebar-divider my-0">
            <li class="nav-item active">
                <a class="nav-link" href="http://localhost/knd/index.php?controller=dashboard&action=index">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
                    
            </li>

            <div class="sidebar-heading">Gestión</div>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=rol&action=index">
                    <i class="fas fa-user-shield"></i>
                    <span>Roles</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=perfil&action=index">
                    <i class="fas fa-users"></i>
                    <span>Perfiles</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=propiedad&action=index">
                    <i class="fas fa-building"></i>
                    <span>Propiedades</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=provincia&action=index">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Provincias</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=ciudad&action=index">
                    <i class="fas fa-city"></i>
                    <span>Ciudades</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=FinInstitucion&action=index">
                    <i class="fas fa-university"></i>
                    <span>Instituciones Financieras </span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=solicitudes&action=index">
                    <i class="fas fa-file-alt"></i>
                    <span>Solicitudes </span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="views/admin/admin_calendar.php">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Calendario de Citas</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo $_SESSION['user_name']; ?>
                                </span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="index.php?controller=login&action=logout">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar Sesión
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Información General</h1>
                    <a href="reporte.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                        <i class="fas fa-download fa-sm text-white-50"></i> Generar Reporte
                    </a>    
                </div>
                <div class="row">
 
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Propiedades Disponibles</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalPropiedades; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-home fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Solicitudes Pendientes</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalSolicitudes; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Empresas Registradas</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalEmpresas; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-building fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Usuarios Activos</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalUsuarios; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-8 col-lg-7">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Propiedades por Tipo</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-bar">
                                    <canvas id="propiedadesPorTipoChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                        <script>
                        var ctx1 = document.getElementById("propiedadesPorTipoChart").getContext('2d');

                        var propiedadesPorTipoChart = new Chart(ctx1, {
                            type: 'bar',
                            data: {
                                labels: [<?php echo $labelsTipos; ?>], 
                                datasets: [{
                                    label: "Número de Propiedades",
                                    data: [<?php echo $dataTipos; ?>], 
                                    backgroundColor: ["#4e73df", "#1cc88a", "#36b9cc", "#f6c23e", "#e74a3b"],
                                    borderColor: "#4e73df",
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                        </script>
                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Disponibilidad de Propiedades</h6>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                        aria-labelledby="dropdownMenuLink">
                                        <div class="dropdown-header">Opciones:</div>
                                        <a class="dropdown-item" href="#">Actualizar</a>
                                        <a class="dropdown-item" href="#">Ver Detalles</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-pie">
                                    <canvas id="disponibilidadChart"></canvas>
                                </div>
                                <div class="mt-4 text-center small">
                                    <span class="mr-2">
                                        <i class="fas fa-circle text-primary"></i> Disponible
                                    </span>
                                    <span class="mr-2">
                                        <i class="fas fa-circle text-success"></i> Ocupado
                                    </span>
                                    <span class="mr-2">
                                        <i class="fas fa-circle text-danger"></i> Reservado
                                    </span>
                                </div>
                            </div>
                            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                            <script>
                            var ctx2 = document.getElementById("disponibilidadChart").getContext('2d');

                            var disponibilidadChart = new Chart(ctx2, {
                                type: 'doughnut',
                                data: {
                                    labels: [<?php echo $labelsDisponibilidad; ?>], 
                                    datasets: [{
                                        data: [<?php echo $dataDisponibilidad; ?>], 
                                        backgroundColor: ["#4e73df", "#1cc88a", "#e74a3b"], 
                                        hoverBackgroundColor: ["#2e59d9", "#17a673", "#b71c1c"],
                                        hoverBorderColor: "rgba(234, 236, 244, 1)"
                                    }]
                                },
                                options: {
                                    maintainAspectRatio: false,
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            position: 'top'
                                        }
                                    }
                                }
                            });
                            </script>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Precio Promedio por Provincia</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-bar">
                                    <canvas id="precioPropiedadesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                    <script>
                    var ctx = document.getElementById("precioPropiedadesChart").getContext('2d');

                    var precioPropiedadesChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: [<?php echo $labelsPrecios; ?>], 
                            datasets: [{
                                label: "Precio Promedio ($)",
                                data: [<?php echo $dataPrecios; ?>], 
                                backgroundColor: ["#4e73df", "#1cc88a", "#36b9cc", "#f6c23e", "#e74a3b"],
                                borderColor: "#4e73df",
                                borderWidth: 1
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            indexAxis: 'y', 
                            scales: {
                                x: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                    </script>
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Últimas Propiedades Añadidas</h6>
                            </div>
                            <div class="card-body">
                                <?php foreach ($ultimasPropiedades as $prop) { ?>
                                    <div class="row align-items-center mb-3">
                                        <div class="col-md-4">
                                            <img class="img-fluid rounded" src="<?php echo $prop['primera_imagen']; ?>" 
                                                alt="Propiedad" style="width: 100%;">
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="text-primary font-weight-bold"><?php echo $prop['pro_tipo']; ?></h6>
                                            <p class="mb-1"><?php echo $prop['pro_direccion']; ?></p>
                                            <p class="mb-0 font-weight-bold">$<?php echo number_format($prop['pro_precio'], 2); ?></p>
                                        </div>
                                    </div>
                                    <hr>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; BLK 2025</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>
    
    <script>
document.addEventListener("DOMContentLoaded", function() {
    setTimeout(() => {
        // Capturar los gráficos como imágenes en base64
        var canvas1 = document.getElementById("propiedadesPorTipoChart");
        var canvas2 = document.getElementById("disponibilidadChart");
        var canvas3 = document.getElementById("precioPropiedadesChart");

        if (!canvas1 || !canvas2 || !canvas3) {
            console.error("❌ Uno o más gráficos no fueron encontrados en el DOM.");
            return;
        }

        var img1 = canvas1.toDataURL("image/png");
        var img2 = canvas2.toDataURL("image/png");
        var img3 = canvas3.toDataURL("image/png");

        console.log("📤 Enviando imágenes al servidor...");

        fetch("guardar_grafico.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ img1, img2, img3 })
        })
        .then(response => response.text())
        .then(data => console.log("✅ Respuesta del servidor:", data))
        .catch(error => console.error("❌ Error en fetch:", error));
    }, 5000); // Espera 5 segundos para asegurarse de que los gráficos estén renderizados
});
</script>


</body>
</html>
