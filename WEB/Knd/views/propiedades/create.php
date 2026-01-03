<?php
require_once 'config/database.php';

global $pdo;
try {
    $stmt = $pdo->query("SELECT * FROM provincias");
    $provincias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al cargar provincias: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PlusvaliaBlk-CrearPropiedades</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        #map {
            height: 350px; 
            width: 50%; 
            margin-top: 10px; 
            border: 2px solid #000;
        }
        #previewContainer {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }
        #previewContainer img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        #pdfPreviewContainer {
            margin-top: 20px;
            display: none;
        }
        #pdfPreview {
            width: 50%;
            height: 500px;
            border: none;
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
            <li class="nav-item active">
                <a class="nav-link" href="http://localhost/knd/index.php?controller=dashboard&action=index">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <div class="sidebar-heading">Gestión</div>
            <input type="hidden" name="per_id" value="<?php echo $_SESSION['user_id']; ?>">
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
                    <h1 class="h3 mb-4 text-gray-800">Crear Propiedad</h1>
                    <form action="index.php?controller=propiedad&action=create" method="POST" enctype="multipart/form-data" class="form">
                        <div class="form-group">
                            <label for="pro_tipo">Tipo de Proyecto:</label>
                            <select name="pro_tipo" class="form-control" required>
                                <option value="Casa">Casa</option>
                                <option value="Departamento">Departamento</option>
                                <option value="Terreno">Terreno</option>
                                <option value="Local Comercial">Local Comercial</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pro_provincia">Provincia:</label>
                            <select name="pro_provincia" id="pro_provincia" class="form-control" required>
                                <option value="">Seleccione una Provincia</option>
                                <?php foreach ($provincias as $provincia): ?>
                                    <option value="<?php echo $provincia['provincia_id']; ?>"><?php echo $provincia['provincia_nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pro_ciudad">Ciudad:</label>
                            <select name="pro_ciudad" id="pro_ciudad" class="form-control" required>
                                <option value="">Seleccione una Provincia primero</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pro_direccion">Dirección Exacta:</label>
                            <input type="text" name="pro_direccion" id="pro_direccion" class="form-control" required>
                            <div id="map"></div>
                        </div>
                        <div class="form-group">
                            <label for="pro_descripcion">Descripción:</label>
                            <textarea name="pro_descripcion" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="pro_baños">Número de Baños:</label>
                            <input type="number" name="pro_baños" class="form-control" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="pro_habitaciones">Número de Habitaciones:</label>
                            <input type="number" name="pro_habitaciones" class="form-control" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="pro_estacionamientos">Número de Estacionamientos (o N/A):</label>
                            <input type="text" name="pro_estacionamientos" class="form-control" pattern="\d+|N/A" title="Ingrese un número o 'N/A'">
                        </div>
                        <div class="form-group">
                            <label for="pro_area_terreno">Área del Terreno (m²):</label>
                            <input type="number" name="pro_area_terreno" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="pro_alto_total">Alto Total (m):</label>
                            <input type="number" name="pro_alto_total" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="pro_nombre_propietario">Nombre Completo del Propietario:</label>
                            <input type="text" name="pro_nombre_propietario" id="pro_nombre_propietario" class="form-control" maxlength="70" 
                                pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$" title="Solo letras y espacios permitidos (máximo 70 caracteres)" required>
                        </div>
                        <div class="form-group">
                            <label for="pro_celular_propietario">Celular del Propietario:</label>
                            <input type="text" name="pro_celular_propietario" class="form-control" pattern="[0-9]{10}" title="Ingrese un número de 10 dígitos" required>
                        </div>
                        <div class="form-group">
                            <label for="pro_precio">Precio:</label>
                            <input type="number" name="pro_precio" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="pro_estado">Estado:</label>
                            <select name="pro_estado" class="form-control">
                                <option value="Disponible">Activo</option>
                                <option value="No Disponible">Inactivo</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pro_disponibilidad">Disponibilidad:</label>
                            <select name="pro_disponibilidad" class="form-control" required>
                                <option value="Disponible">Disponible</option>
                                <option value="Ocupado">Ocupado</option>
                                <option value="Reservado">Reservado</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pro_imagenes">Subir Imágenes:</label>
                            <input type="file" name="pro_imagenes[]" id="pro_imagenes" class="form-control" multiple accept="image/*">
                            <div id="previewContainer"></div>
                        </div>
                        <div class="form-group">
                            <label for="pro_planos">Subir Planos (PDF):</label>
                            <input type="file" name="pro_planos" id="pro_planos" class="form-control" accept="application/pdf">
                            <div id="pdfPreviewContainer">
                                <iframe id="pdfPreview"></iframe>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Registrar Propiedad" class="btn btn-primary">
                            <a href="index.php?controller=propiedad&action=index" class="btn btn-secondary">Volver</a>
                        </div>
                    </form>

                    <script>
                        $(document).ready(function() {
                            $('#pro_provincia').change(function() {
                                var provinciaId = $(this).val();
                                
                                $.ajax({
                                    url: 'get_ciudades.php',
                                    type: 'POST',
                                    data: { provincia_id: provinciaId },
                                    success: function(data) {
                                        $('#pro_ciudad').html(data);
                                    }
                                });
                            });
                        });

                        var map = L.map('map').setView([-0.1807, -78.4678], 7); 
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 18
                        }).addTo(map);

                        var marker = L.marker([-0.1807, -78.4678], { draggable: true }).addTo(map);

                        $('#pro_provincia, #pro_ciudad').change(function() {
                            var provincia = $('#pro_provincia option:selected').text();
                            var ciudad = $('#pro_ciudad option:selected').text();
                            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${ciudad}, ${provincia}, Ecuador`)
                                .then(response => response.json())
                                .then(data => {
                                    if(data.length > 0) {
                                        var location = [data[0].lat, data[0].lon];
                                        map.setView(location, 12);
                                        marker.setLatLng(location);
                                    }
                                });
                        });

                        marker.on('dragend', function(event) {
                            var position = marker.getLatLng();
                            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${position.lat}&lon=${position.lng}&format=json`)
                                .then(response => response.json())
                                .then(data => {
                                    document.getElementById('pro_direccion').value = data.display_name;
                                });
                        });

                        // Previsualización de imágenes
                        const proImagenesInput = document.getElementById('pro_imagenes');
                        const previewContainer = document.getElementById('previewContainer');

                        proImagenesInput.addEventListener('change', (event) => {
                            previewContainer.innerHTML = '';
                            const files = event.target.files;

                            Array.from(files).forEach(file => {
                                const fileReader = new FileReader();
                                fileReader.onload = (e) => {
                                    const img = document.createElement('img');
                                    img.src = e.target.result;
                                    previewContainer.appendChild(img);  
                                };
                                fileReader.readAsDataURL(file);
                            });
                        });

                        const proPlanosInput = document.getElementById('pro_planos');
                        const pdfPreviewContainer = document.getElementById('pdfPreviewContainer');
                        const pdfPreview = document.getElementById('pdfPreview');

                        proPlanosInput.addEventListener('change', (event) => {
                            const file = event.target.files[0];
                            if (file && file.type === 'application/pdf') {
                                const fileReader = new FileReader();
                                fileReader.onload = (e) => {
                                    pdfPreview.src = e.target.result;
                                    pdfPreviewContainer.style.display = 'block';
                                };
                                fileReader.readAsDataURL(file);
                            } else {
                                pdfPreviewContainer.style.display = 'none';
                            }
                        });
                    </script>

                    <?php if (isset($success)): ?>
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                <?php if ($success): ?>
                                    Swal.fire({
                                        title: '¡Éxito!',
                                        text: '✅ Propiedad creada exitosamente.',
                                        icon: 'success',
                                        showCancelButton: true,
                                        confirmButtonText: 'Ir al listado',
                                        cancelButtonText: 'Permanecer aquí',
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = "index.php?controller=propiedad&action=index";
                                        }
                                    });
                                <?php else: ?>
                                    Swal.fire({
                                        title: 'Error',
                                        text: '❌ Hubo un error al crear la propiedad. Inténtalo de nuevo.',
                                        icon: 'error',
                                        confirmButtonText: 'Aceptar',
                                        confirmButtonColor: '#d33'
                                    });
                                <?php endif; ?>
                            });
                        </script>
                    <?php endif; ?>
                </div>
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
