<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PlusvaliaBlk-CrearPerfil</title>
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
            <li class="nav-item active"><a class="nav-link" href="index.php?controller=perfil&action=index"><i class="fas fa-users"></i><span>Perfiles</span></a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?controller=propiedad&action=index"><i class="fas fa-building"></i><span>Propiedades</span></a></li>
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
                    <h1 class="h3 mb-4 text-gray-800">Crear Nuevo Perfil</h1>
                    <form action="index.php?controller=perfil&action=create" method="post" class="form">
                        <div class="form-group">
                            <label for="per_nombre">Nombre:</label>
                            <input type="text" id="per_nombre" name="per_nombre" class="form-control" placeholder="Ingresa tu nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="per_apellido">Apellido:</label>
                            <input type="text" id="per_apellido" name="per_apellido" class="form-control" placeholder="Ingresa tu apellido" required>
                        </div>
                        <div class="form-group">
                            <label for="per_email">Email:</label>
                            <input type="email" id="per_email" name="per_email" class="form-control" placeholder="Ingresa tu email" required>
                        </div>
                        <div class="form-group">
                            <label for="per_password">Contraseña:</label>
                            <input type="password" id="per_password" name="per_password" class="form-control" placeholder="Crea una contraseña" required>
                        </div>
                        <div class="form-group">
                            <label for="rol_id">Rol:</label>
                            <select id="rol_id" name="rol_id" class="form-control" required>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?php echo $rol['rol_id']; ?>">
                                        <?php echo $rol['rol_nombre']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="per_status">Estado:</label>
                            <select id="per_status" name="per_status" class="form-control" required>
                                <option value="A">Activo</option>
                                <option value="I">Pendiente</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Crear Perfil" class="btn btn-primary">
                            <a href="index.php?controller=perfil&action=index" class="btn btn-secondary">Volver</a>
                        </div>
                    </form>
                    <script src="vendor/jquery/jquery.min.js"></script>
                    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
                    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
                    <script src="js/sb-admin-2.min.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            <?php if ($success): ?>
                                Swal.fire({
                                    title: '¡Éxito!',
                                    text: '✅ Perfil creado exitosamente.',
                                    icon: 'success',
                                    showCancelButton: true,
                                    confirmButtonText: 'Ir al listado',
                                    cancelButtonText: 'Permanecer aquí',
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "index.php?controller=perfil&action=index";
                                    }
                                });
                            <?php endif; ?>
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
