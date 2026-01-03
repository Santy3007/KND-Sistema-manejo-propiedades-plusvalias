<?php
    session_start();
    require_once '../../config/database.php';
    require_once '../../models/Propiedad.php';
    require_once '../../models/AdminCita.php';

    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../../index.php?controller=login&action=index');
        exit();
    }

    // Verificar si el usuario tiene el rol adecuado
    if (!in_array($_SESSION['rol_id'], [1, 2])) {
        header('Location: ../../index.php?controller=dashboard&action=index');
        exit();
    }

    global $pdo;
    $propiedadModel = new Propiedad();
    $citaModel = new AdminCita();

    if ($_SESSION['rol_id'] == 1) {
        $propiedades = $propiedadModel->getAll();
    } else {
        $propiedades = $propiedadModel->getAllByUser($_SESSION['user_id']);
    }
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>PlusvaliaBlk - Calendario de Citas</title>
        <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
        <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css">
        <style>
            .property-card {
                cursor: pointer;
                transition: transform 0.2s;
                margin-bottom: 15px;
            }
            .property-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }
            .property-image {
                height: 150px;
                object-fit: cover;
            }
            .fc-event {
                cursor: pointer;
            }
        </style>
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
                <li class="nav-item">
                    <a class="nav-link" href="http://localhost/knd/index.php?controller=dashboard&action=index">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span></a>
                </li>
                <div class="sidebar-heading">Gestión</div>
                <li class="nav-item"><a class="nav-link" href="/knd/index.php?controller=rol&action=index"><i class="fas fa-user-shield"></i><span>Roles</span></a></li>
                <li class="nav-item"><a class="nav-link" href="/knd/index.php?controller=perfil&action=index"><i class="fas fa-users"></i><span>Perfiles</span></a></li>
                <li class="nav-item"><a class="nav-link" href="/knd/index.php?controller=propiedad&action=index"><i class="fas fa-building"></i><span>Propiedades</span></a></li>
                <li class="nav-item"><a class="nav-link" href="/knd/index.php?controller=provincia&action=index"><i class="fas fa-map-marker-alt"></i><span>Provincias</span></a></li>
                <li class="nav-item"><a class="nav-link" href="/knd/index.php?controller=ciudad&action=index"><i class="fas fa-city"></i><span>Ciudades</span></a></li>
                <li class="nav-item"><a class="nav-link" href="/knd/index.php?controller=fininstitucion&action=index"><i class="fas fa-university"></i><span>Instituciones Financieras</span></a></li>
                <li class="nav-item"><a class="nav-link" href="/knd/index.php?controller=solicitudes&action=index"><i class="fas fa-file-alt"></i><span>Solicitudes</span></a></li>
                <li class="nav-item active"><a class="nav-link" href="admin_calendar.php"><i class="fas fa-calendar-alt"></i><span>Calendario de Citas</span></a></li>
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
                        <h1 class="h3 mb-4 text-gray-800">Calendario de Citas</h1>
                        <div class="d-flex justify-content-end mb-3">
                            <a href="http://localhost/knd/views/agentes/seleccionar_cita.php" class="btn btn-primary">
                                <i class="fas fa-search"></i> Seleccionar Cita Completada
                            </a>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Agenda</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="calendar"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Seleccionar Propiedad</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="input-group mb-3">
                                            <input type="text" id="searchProperty" class="form-control" placeholder="Buscar propiedad...">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button">
                                                    <i class="fas fa-search fa-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="properties-container" style="max-height: 500px; overflow-y: auto;">
                                            <?php foreach ($propiedades as $propiedad): 
                                                // Obtener la primera imagen
                                                $imagenes = explode(',', $propiedad['pro_imagenes']);
                                                $imagenes = explode(',', $propiedad['pro_imagenes']);
                                                $imagen = !empty($imagenes[0]) ? '../../' . $imagenes[0] : '../../img/no-image.jpg';

                                            ?>
                                                <div class="card property-card" data-id="<?php echo $propiedad['pro_id']; ?>" data-propiedad='<?php echo json_encode([
                                                    'id' => $propiedad['pro_id'],
                                                    'direccion' => $propiedad['pro_direccion'],
                                                    'tipo' => $propiedad['pro_tipo'],
                                                    'precio' => $propiedad['pro_precio']
                                                ]); ?>'>
                                                    <img src="<?php echo $imagen; ?>" class="card-img-top property-image" alt="Propiedad">
                                                    <div class="card-body p-2">
                                                        <h6 class="card-title mb-1"><?php echo htmlspecialchars($propiedad['pro_direccion']); ?></h6>
                                                        <p class="card-text small mb-1"><?php echo htmlspecialchars($propiedad['pro_tipo']); ?></p>
                                                        <p class="card-text small text-primary">$<?php echo htmlspecialchars($propiedad['pro_precio']); ?></p>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal para crear/editar cita -->
        <div class="modal fade" id="citaModal" tabindex="-1" role="dialog" aria-labelledby="citaModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="citaModalLabel">Nueva Cita</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="citaForm">
                            <input type="hidden" id="cita_id" name="cita_id">
                            <input type="hidden" id="pro_id" name="pro_id">
                            
                            <div class="form-group">
                                <label for="propiedad_info">Propiedad:</label>
                                <input type="text" class="form-control" id="propiedad_info" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label for="cita_fecha">Fecha y Hora:</label>
                                <input type="datetime-local" class="form-control" id="cita_fecha" name="cita_fecha" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="cita_nombre">Nombre:</label>
                                <input type="text" class="form-control" id="cita_nombre" name="cita_nombre" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="cita_email">Correo Electrónico:</label>
                                <input type="email" class="form-control" id="cita_email" name="cita_email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="cita_telefono">Teléfono:</label>
                                <input type="tel" class="form-control" id="cita_telefono" name="cita_telefono" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="cita_descripcion">Descripción:</label>
                                <textarea class="form-control" id="cita_descripcion" name="cita_descripcion" rows="3"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="cita_estado">Estado:</label>
                                <select class="form-control" id="cita_estado" name="cita_estado">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="confirmada">Confirmada</option>
                                    <option value="cancelada">Cancelada</option>
                                    <option value="completada">Completada</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="deleteCitaBtn" style="display: none;">Eliminar</button>
                        <button type="button" class="btn btn-primary" id="saveCitaBtn">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="../../vendor/jquery/jquery.min.js"></script>
        <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="../../js/sb-admin-2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/locales-all.min.js"></script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let selectedProperty = null;
                let calendar = null;

                // Inicializar FullCalendar
                const calendarEl = document.getElementById('calendar');
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    locale: 'es',
                    selectable: true,
                    selectMirror: true,
                    editable: true,
                    dayMaxEvents: true,
                    events: function(info, successCallback, failureCallback) {
                    fetch('../../index.php?controller=adminCita&action=getEvents')
                        .then(response => response.json())
                        .then(data => {
                            console.log("Eventos obtenidos del servidor:", data); // 🔍 Depuración
                            successCallback(data);
                        })
                        .catch(error => {
                            console.error('Error al cargar eventos:', error);
                            failureCallback(error);
                        });
                     },
                    select: function(info) {
                        if (!selectedProperty) {
                            Swal.fire({
                                title: 'Seleccione una propiedad',
                                text: 'Debe seleccionar una propiedad antes de crear una cita',
                                icon: 'warning',
                                confirmButtonText: 'Aceptar'
                            });
                            return;
                        }

                        // Formatear la fecha para el input datetime-local
                        const selectedDate = new Date(info.start);
                        selectedDate.setMinutes(selectedDate.getMinutes() - selectedDate.getTimezoneOffset());
                        document.getElementById('cita_fecha').value = selectedDate.toISOString().slice(0, 16);

                        // Mostrar modal para nueva cita
                        document.getElementById('citaModalLabel').textContent = 'Nueva Cita';
                        document.getElementById('citaForm').reset();
                        document.getElementById('cita_id').value = '';
                        document.getElementById('pro_id').value = selectedProperty.id;
                        document.getElementById('propiedad_info').value = `${selectedProperty.tipo} - ${selectedProperty.direccion}`;
                        document.getElementById('deleteCitaBtn').style.display = 'none';
                        $('#citaModal').modal('show');

                        calendar.unselect();
                    },
                    eventClick: function(info) {
                        const eventId = info.event.id;

                        // Cargar detalles de la cita
                        fetch(`../../index.php?controller=adminCita&action=getById&id=${eventId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data) {
                                    const citaFecha = new Date(data.cita_fecha);
                                    citaFecha.setMinutes(citaFecha.getMinutes() - citaFecha.getTimezoneOffset());

                                    document.getElementById('citaModalLabel').textContent = 'Editar Cita';
                                    document.getElementById('cita_id').value = data.cita_id;
                                    document.getElementById('pro_id').value = data.pro_id;
                                    document.getElementById('propiedad_info').value = `${data.pro_tipo} - ${data.pro_direccion}`;
                                    document.getElementById('cita_fecha').value = citaFecha.toISOString().slice(0, 16);
                                    document.getElementById('cita_nombre').value = data.cita_nombre;
                                    document.getElementById('cita_email').value = data.cita_email;
                                    document.getElementById('cita_telefono').value = data.cita_telefono;
                                    document.getElementById('cita_descripcion').value = data.cita_descripcion;
                                    document.getElementById('cita_estado').value = data.cita_estado;
                                    document.getElementById('deleteCitaBtn').style.display = 'block';

                                    $('#citaModal').modal('show');
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'No se pudo cargar la información de la cita',
                                        icon: 'error',
                                        confirmButtonText: 'Aceptar'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error al cargar la cita:', error);
                            });
                    }
                });

                calendar.render();

                // Seleccionar propiedad
                document.querySelectorAll('.property-card').forEach(card => {
                    card.addEventListener('click', function() {
                        const propertyData = JSON.parse(this.getAttribute('data-propiedad'));
                        selectedProperty = propertyData;

                        document.querySelectorAll('.property-card').forEach(c => c.classList.remove('border-primary', 'border'));
                        this.classList.add('border-primary', 'border');

                        Swal.fire({
                            title: 'Propiedad seleccionada',
                            text: `${propertyData.tipo} - ${propertyData.direccion}`,
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    });
                });

                // Guardar cita
                document.getElementById('saveCitaBtn').addEventListener('click', function() {
                    const form = document.getElementById('citaForm');
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }

                    const formData = new FormData(form);
                    const citaId = formData.get('cita_id');
                    const action = citaId ? 'update' : 'create';

                    fetch(`../../index.php?controller=adminCita&action=${action}${citaId ? '&id=' + citaId : ''}`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Éxito',
                                text: citaId ? 'Cita actualizada correctamente' : 'Cita creada correctamente',
                                icon: 'success',
                                confirmButtonText: 'Aceptar'
                            }).then(() => {
                                $('#citaModal').modal('hide');
                                calendar.refetchEvents();
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                });

                // Eliminar cita
                document.getElementById('deleteCitaBtn').addEventListener('click', function() {
                    const citaId = document.getElementById('cita_id').value;

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡Esta acción no se puede deshacer!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`../../index.php?controller=adminCita&action=delete&id=${citaId}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Eliminado', 'La cita ha sido eliminada correctamente', 'success')
                                        .then(() => {
                                            $('#citaModal').modal('hide');
                                            calendar.refetchEvents();
                                        });
                                    }
                                });
                        }
                    });
                });
            });
        </script>
</body>
</html>        