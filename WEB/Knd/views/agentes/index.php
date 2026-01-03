<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Propiedad.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php?controller=login&action=index');
    exit();
}

// Verificar si el usuario tiene el rol adecuado
if ($_SESSION['rol_id'] != 20) {
    header('Location: ../../index.php?controller=dashboard&action=index');
    exit();
}

global $pdo;
$propiedadModel = new Propiedad();

if ($_SESSION['rol_id'] == 1) {
    $propiedades = $propiedadModel->getAll();
} else {
    $propiedades = $propiedadModel->getAllByUser($_SESSION['user_id']); 
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
    <title>PlusvaliaBlk - Listado de Propiedades de agentes</title>
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="http://localhost/knd/views/agentes/index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Plusvalia<sup>Blk</sup></div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item active">
                <a class="nav-link" href="http://localhost/knd/views/agentes/index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <div class="sidebar-heading">Gestión</div>
            <li class="nav-item active"><a class="nav-link" href="http://localhost/knd/views/agentes/index.php"><i class="fas fa-building"></i><span>Propiedades</span></a></li>
            <li class="nav-item"><a class="nav-link" href="http://localhost/knd/views/agentes/calendar.php"><i class="fas fa-calendar-alt"></i><span>Calendario de Citas</span></a></li>
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
                                <img class="img-profile rounded-circle" src="../../img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="http://localhost/knd/index.php?controller=login&action=index">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar Sesión
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Listado de Propiedades</h1>
                    <a href="http://localhost/knd/views/agentes/create.php" class="btn btn-primary mb-3">Crear Nueva Propiedad</a>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Creador</th>
                                <th>Tipo</th>
                                <th>Disponibilidad</th>
                                <th>Dirección</th>
                                <th>Nombre del propietario</th>
                                <th>Numero del propietario</th>
                                <th>Precio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($propiedades as $propiedad): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($propiedad['pro_id']); ?></td>
                                    <td><?php echo htmlspecialchars($propiedad['propietario_nombre'] ?? 'Sin propietario'); ?></td>
                                    <td><?php echo htmlspecialchars($propiedad['pro_tipo']); ?></td>
                                    <td><?php echo htmlspecialchars($propiedad['pro_disponibilidad']); ?></td>
                                    <td><?php echo htmlspecialchars($propiedad['pro_direccion']); ?></td>
                                    <td><?php echo htmlspecialchars($propiedad['pro_nombre_propietario']); ?></td>
                                    <td><?php echo htmlspecialchars($propiedad['pro_celular_propietario']); ?></td>
                                    <td>$<?php echo htmlspecialchars($propiedad['pro_precio']); ?></td>
                                    <td>
                                        <a href="http://localhost/knd/views/agentes/show.php?id=<?php echo $propiedad['pro_id']; ?>" class="btn btn-info">Ver</a>
                                        <a href="http://localhost/knd/views/agentes/edit.php?id=<?php echo $propiedad['pro_id']; ?>" class="btn btn-warning">Editar</a>
                                        <button class="btn btn-danger btn-delete" data-id="<?php echo $propiedad['pro_id']; ?>">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <script>
                        document.querySelectorAll('.btn-delete').forEach(button => {
                            button.addEventListener('click', function () {
                                const propiedadId = this.getAttribute('data-id');
                                Swal.fire({
                                    title: '¿Estás seguro?',
                                    text: "¡Esta acción no se puede deshacer!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Sí, eliminar',
                                    cancelButtonText: 'Cancelar'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        fetch(`../../index.php?controller=propiedad&action=delete&id=${propiedadId}`)
                                            .then(response => {
                                                if (response.ok) {
                                                    Swal.fire({
                                                        title: 'Eliminado',
                                                        text: 'La propiedad ha sido eliminada correctamente.',
                                                        icon: 'success',
                                                        confirmButtonText: 'Aceptar'
                                                    }).then(() => {
                                                        window.location.href = 'http://localhost/knd/views/agentes/index.php';
                                                    });
                                                } else {
                                                    Swal.fire({
                                                        title: 'Error',
                                                        text: 'Hubo un problema al eliminar la propiedad.',
                                                        icon: 'error',
                                                        confirmButtonText: 'Aceptar'
                                                    });
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error:', error);
                                                Swal.fire({
                                                    title: 'Error',
                                                    text: 'No se pudo conectar con el servidor.',
                                                    icon: 'error',
                                                    confirmButtonText: 'Aceptar'
                                                });
                                            });
                                    }
                                });
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../../js/sb-admin-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
