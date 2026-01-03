<?php
require_once 'config/database.php';
require_once 'models/Propiedad.php';

global $pdo;
$propiedadModel = new Propiedad();
$propiedad = $propiedadModel->getById($_GET['id']);

if (!$propiedad) {
    echo "Propiedad no encontrada.";
    exit();
}

// Obtener nombres de provincias y ciudades
$stmtProvincias = $pdo->query("SELECT provincia_id, provincia_nombre FROM provincias");
$provincias = $stmtProvincias->fetchAll(PDO::FETCH_KEY_PAIR);

$stmtCiudades = $pdo->query("SELECT ciudad_id, ciudad_nombre FROM ciudades");
$ciudades = $stmtCiudades->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PlusvaliaBlk - Detalle de Propiedad</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
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
            <li class="nav-item"><a class="nav-link" href="index.php?controller=rol&action=index"><i class="fas fa-user-shield"></i><span>Roles</span></a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?controller=perfil&action=index"><i class="fas fa-users"></i><span>Perfiles</span></a></li>
            <li class="nav-item active"><a class="nav-link" href="index.php?controller=propiedad&action=index"><i class="fas fa-building"></i><span>Propiedades</span></a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?controller=provincia&action=index"><i class="fas fa-map-marker-alt"></i><span>Provincias</span></a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?controller=ciudad&action=index"><i class="fas fa-city"></i><span>Ciudades</span></a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?controller=fininstitucion&action=index"><i class="fas fa-university"></i><span>Instituciones Financieras</span></a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?controller=solicitudes&action=index"><i class="fas fa-file-alt"></i><span>Solicitudes</span></a></li>
            <li class="nav-item"><a class="nav-link" href="views/admin/admin_calendar.php"><i class="fas fa-calendar-alt"></i><span>Calendario de Citas</span></a></li>
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
                    <h1 class="h3 mb-4 text-gray-800">Detalle de Propiedad</h1>
                    <div class="property-details">
                        <p><strong>Tipo:</strong> <?php echo htmlspecialchars($propiedad['pro_tipo']); ?></p>
                        <p><strong>Provincia:</strong> <?php echo htmlspecialchars($provincias[$propiedad['pro_provincia']] ?? 'Desconocido'); ?></p>
                        <p><strong>Dirección:</strong> <?php echo htmlspecialchars($propiedad['pro_direccion']); ?></p>
                        <p><strong>Ciudad:</strong> <?php echo htmlspecialchars($ciudades[$propiedad['pro_ciudad']] ?? 'Desconocido'); ?></p>
                        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($propiedad['pro_descripcion']); ?></p>
                        <p><strong>Baños:</strong> <?php echo htmlspecialchars($propiedad['pro_baños']); ?></p>
                        <p><strong>Estacionamientos:</strong> <?php echo htmlspecialchars($propiedad['pro_estacionamientos'] ?? 'N/A'); ?></p>
                        <p><strong>Habitaciones:</strong> <?php echo htmlspecialchars($propiedad['pro_habitaciones']); ?></p>
                        <p><strong>Área del Terreno:</strong> <?php echo htmlspecialchars($propiedad['pro_area_terreno']); ?> m²</p>
                        <p><strong>Alto Total:</strong> <?php echo htmlspecialchars($propiedad['pro_alto_total']); ?> m</p>
                        <p><strong>Disponibilidad:</strong> <?php echo htmlspecialchars($propiedad['pro_disponibilidad']); ?></p>
                        <p><strong>Nombre del propietario:</strong> <?php echo htmlspecialchars($propiedad['pro_nombre_propietario']); ?></p>
                        <p><strong>Celular del Propietario:</strong> <?php echo htmlspecialchars($propiedad['pro_celular_propietario']); ?></p>
                        <p><strong>Precio:</strong> $<?php echo htmlspecialchars($propiedad['pro_precio']); ?></p>
                    </div>
                    <h2>Imágenes</h2>
                    <div id="imageGallery">
                        <?php 
                        $imagenes = explode(',', $propiedad['pro_imagenes']);
                        foreach ($imagenes as $imagen): ?>
                            <img src="<?php echo htmlspecialchars($imagen); ?>" alt="Imagen Propiedad" width="150" height="150" style="object-fit: cover; margin-right: 10px;">
                        <?php endforeach; ?>
                    </div>
                    <h2>Plano del Proyecto</h2>
                    <?php if (!empty($propiedad['pro_planos'])): ?>
                        <iframe src="<?php echo htmlspecialchars($propiedad['pro_planos']); ?>" width="600" height="400"></iframe>
                    <?php endif; ?>
                </div>
                <a href="index.php?controller=propiedad&action=index" class="btn btn-secondary mt-3">Volver al listado</a>
            </div>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
