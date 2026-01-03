<?php
require_once __DIR__ . '/../../config/database.php'; // Incluir conexión
global $pdo; // Asegurar que la variable está disponible

// Consulta SQL para obtener las solicitudes con el nombre de la propiedad
$sql = "
    SELECT 
        s.id,
        CONCAT(p.pro_tipo, ' en ', c.ciudad_nombre, ', ', 
               p.pro_area_terreno, 'm², ', p.pro_habitaciones, ' habitaciones, ', 
               CASE 
                   WHEN p.pro_disponibilidad = 1 THEN 'Disponible' 
                   WHEN p.pro_disponibilidad = 2 THEN 'Ocupado' 
                   WHEN p.pro_disponibilidad = 3 THEN 'Reservado' 
                   ELSE 'Estado desconocido' 
               END
        ) AS propiedad,
        s.nombre,
        s.correo,
        s.fecha_cita,
        s.mensaje,
        s.fecha_creacion
    FROM solicitudes s
    JOIN pro_propiedades p ON s.pro_id = p.pro_id
    JOIN ciudades c ON p.pro_ciudad = c.ciudad_id";


$stmt = $pdo->prepare($sql);
$stmt->execute();
$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PlusvaliaBlk - Gestión de Solicitudes</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <li class="nav-item"><a class="nav-link" href="index.php?controller=propiedad&action=index"><i class="fas fa-building"></i><span>Propiedades</span></a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?controller=provincia&action=index"><i class="fas fa-map-marker-alt"></i><span>Provincias</span></a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?controller=ciudad&action=index"><i class="fas fa-city"></i><span>Ciudades</span></a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?controller=fininstitucion&action=index"><i class="fas fa-university"></i><span>Instituciones Financieras</span></a></li>
            <li class="nav-item active"><a class="nav-link" href="index.php?controller=solicitudes&action=index"><i class="fas fa-file-alt"></i><span>Solicitudes</span></a></li>
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
                    <h1 class="h3 mb-4 text-gray-800">Gestión de Solicitudes</h1>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Propiedad</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Fecha de Cita</th>
                                <th>Mensaje</th>
                                <th>Fecha de Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudes as $solicitud): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($solicitud['id']); ?></td>
                                <td><?php echo htmlspecialchars($solicitud['propiedad']); ?></td>

                                <td><?php echo htmlspecialchars($solicitud['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($solicitud['correo']); ?></td>
                                <td><?php echo htmlspecialchars($solicitud['fecha_cita']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($solicitud['mensaje'])); ?></td>
                                <td><?php echo htmlspecialchars($solicitud['fecha_creacion']); ?></td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-delete" data-id="<?php echo $solicitud['id']; ?>">Eliminar</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <script src="vendor/jquery/jquery.min.js"></script>
                    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
                    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
                    <script src="js/sb-admin-2.min.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
