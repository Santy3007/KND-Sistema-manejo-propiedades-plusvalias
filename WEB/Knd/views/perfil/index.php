<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PlusvaliaBlk</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                    <h1 class="h3 mb-4 text-gray-800">Gestión de Perfiles</h1>
                    <a href="index.php?controller=perfil&action=create" class="btn btn-primary mb-3">Crear Nuevo Perfil</a>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Status</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($perfiles as $perfil): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($perfil['per_id']); ?></td>
                                <td><?php echo htmlspecialchars($perfil['per_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($perfil['per_apellido']); ?></td>
                                <td><?php echo htmlspecialchars($perfil['per_email']); ?></td>
                                <td><?php echo htmlspecialchars($perfil['rol_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($perfil['per_status']); ?></td>
                                <td>
                                    <a href="index.php?controller=perfil&action=edit&id=<?php echo $perfil['per_id']; ?>" class="btn btn-warning">Editar</a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-delete" data-id="<?php echo $perfil['per_id']; ?>">Eliminar</a>
                                    <?php if ($perfil['per_status'] == 'P'): ?>
                                        <button class="btn btn-success btn-aceptar" data-id="<?php echo $perfil['per_id']; ?>">Aceptar</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
                const perfilId = this.getAttribute('data-id');
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
                        window.location.href = `index.php?controller=perfil&action=delete&id=${perfilId}`;
                    }
                });
            });
        });
    </script>
    <script>
    document.querySelectorAll('.btn-aceptar').forEach(button => {
        button.addEventListener('click', function () {
            const perfilId = this.getAttribute('data-id');

            Swal.fire({
                title: '¿Aceptar este perfil?',
                text: "El estado cambiará a Inactivo.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, aceptar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('index.php?controller=perfil&action=aceptar', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'id=' + perfilId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Aceptado', 'El perfil ha sido aceptado.', 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', 'No se pudo aceptar el perfil.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>

</body>
</html>