<?php
session_start();
require_once '../../config/database.php';

// Directory for uploads
$uploadsDir = $_SERVER['DOCUMENT_ROOT'] . '/knd/uploads/';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol_id'])) {
    die("Error: No has iniciado sesión.");
}

// Function to upload multiple images
function uploadMultipleImages($files) {
    global $uploadsDir;
    $uploadedPaths = [];

    if (!empty($files['name'][0])) {
        foreach ($files['name'] as $key => $filename) {
            $filePath = 'uploads/' . time() . '_' . basename($filename);
            $fullPath = $uploadsDir . time() . '_' . basename($filename);
            if (move_uploaded_file($files["tmp_name"][$key], $fullPath)) {
                $uploadedPaths[] = $filePath;
            }
        }
    }
    return implode(',', $uploadedPaths);
}

// Function to upload single file (PDF)
function uploadSingleFile($file) {
    global $uploadsDir;
    if (!empty($file['name']) && $file['error'] == UPLOAD_ERR_OK) {
        $filePath = 'uploads/' . time() . '_' . basename($file["name"]);
        $fullPath = $uploadsDir . time() . '_' . basename($file["name"]);
        if (move_uploaded_file($file["tmp_name"], $fullPath)) {
            return $filePath;
        }
    }
    return null;
}

// Load provinces
try {
    $stmt = $pdo->query("SELECT * FROM provincias");
    $provincias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($provincias === false) {
        $provincias = [];
    }
} catch (PDOException $e) {
    echo "Error al cargar provincias: " . $e->getMessage();
    $provincias = [];
}

// Load cities for the specific province
function loadCities($pdo, $provinciaId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM ciudades WHERE provincia_id = ?");
        $stmt->execute([$provinciaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Process form submission
$success = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['id'])) {
    try {
        $propiedadId = $_GET['id'];

        // Retrieve existing property data
        $stmtExistente = $pdo->prepare("SELECT * FROM pro_propiedades WHERE pro_id = ?");
        $stmtExistente->execute([$propiedadId]);
        $propiedadExistente = $stmtExistente->fetch(PDO::FETCH_ASSOC);

        // Handle image uploads - use existing images if no new ones uploaded
        $imagenesRuta = !empty($_FILES['pro_imagenes']['name'][0]) 
            ? uploadMultipleImages($_FILES['pro_imagenes']) 
            : $propiedadExistente['pro_imagenes'];

        // Handle PDF upload - use existing PDF if no new one uploaded
        $planosRuta = !empty($_FILES['pro_planos']['name']) 
            ? uploadSingleFile($_FILES['pro_planos']) 
            : $propiedadExistente['pro_planos'];

        // Prepare update statement
        $stmt = $pdo->prepare('UPDATE pro_propiedades 
            SET pro_tipo = ?, pro_provincia = ?, pro_ciudad = ?, pro_descripcion = ?, 
                pro_area_terreno = ?, pro_alto_total = ?, pro_disponibilidad = ?, 
                pro_direccion = ?, pro_nombre_propietario = ?, pro_celular_propietario = ?, 
                pro_precio = ?, pro_estado = ?, pro_imagenes = ?, pro_planos = ?, 
                pro_baños = ?, pro_habitaciones = ?, pro_estacionamientos = ? 
            WHERE pro_id = ?');

        // Handle parking field
        $pro_estacionamientos = isset($_POST['pro_estacionamientos']) && $_POST['pro_estacionamientos'] !== '' 
            ? $_POST['pro_estacionamientos'] 
            : 'N/A';

        // Check if anything actually changed
        $dataChanged = false;
        $fields = [
            'pro_tipo', 'pro_provincia', 'pro_ciudad', 'pro_descripcion', 'pro_area_terreno', 
            'pro_alto_total', 'pro_disponibilidad', 'pro_direccion', 'pro_nombre_propietario', 
            'pro_celular_propietario', 'pro_precio', 'pro_estado', 'pro_baños', 
            'pro_habitaciones', 'pro_estacionamientos'
        ];

        foreach ($fields as $field) {
            if (isset($_POST[$field]) && $_POST[$field] != $propiedadExistente[$field]) {
                $dataChanged = true;
                break;
            }
        }

        // Only update if something changed or files were uploaded
        if ($dataChanged || !empty($_FILES['pro_imagenes']['name'][0]) || !empty($_FILES['pro_planos']['name'])) {
            $stmt->execute([
                $_POST['pro_tipo'],
                $_POST['pro_provincia'],
                $_POST['pro_ciudad'],
                $_POST['pro_descripcion'],
                $_POST['pro_area_terreno'],
                $_POST['pro_alto_total'],
                $_POST['pro_disponibilidad'],
                $_POST['pro_direccion'],
                $_POST['pro_nombre_propietario'],
                $_POST['pro_celular_propietario'],
                $_POST['pro_precio'],
                $_POST['pro_estado'],
                $imagenesRuta,
                $planosRuta,
                $_POST['pro_baños'],
                $_POST['pro_habitaciones'],
                $pro_estacionamientos,
                $propiedadId
            ]);
            $success = true;
        } else {
            $success = false; // No changes made
        }
    } catch (PDOException $e) {
        $success = false;
        error_log("Error updating property: " . $e->getMessage());
    }
}

// Load property details
if (isset($_GET['id'])) {
    $propiedadId = $_GET['id'];
    try {
        $stmtPropiedad = $pdo->prepare("SELECT * FROM pro_propiedades WHERE pro_id = ?");
        $stmtPropiedad->execute([$propiedadId]);
        $propiedad = $stmtPropiedad->fetch(PDO::FETCH_ASSOC);

        // Load cities for the specific province
        if ($propiedad) {
            $ciudades = loadCities($pdo, $propiedad['pro_provincia']);
        }
    } catch (PDOException $e) {
        echo "Error al cargar la propiedad: " . $e->getMessage();
        $propiedad = null;
    }
} else {
    echo "ID de propiedad no proporcionado.";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PlusvaliaBlk-EditarPropiedades</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
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
        }
        #pdfPreview {
            width: 50%;
            height: 500px;
            border: 1px solid #ccc;
        }
        .pdf-info {
            margin: 10px 0;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
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
            <li class="nav-item"><a class="nav-link" href="http://localhost/knd/views/agentes/index.php"><i class="fas fa-building"></i><span>Propiedades</span></a></li>
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
                                <a class="dropdown-item" href="index.php?controller=login&action=logout">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar Sesión
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Editar Propiedad</h1>
                    <form action="edit.php?id=<?php echo htmlspecialchars($propiedad['pro_id']); ?>" method="POST" enctype="multipart/form-data" class="form">
                        <div class="form-group">
                            <label for="pro_tipo">Tipo de Proyecto:</label>
                            <select name="pro_tipo" id="pro_tipo" class="form-control" required>
                                <option value="Casa" <?php echo $propiedad['pro_tipo'] == 'Casa' ? 'selected' : ''; ?>>Casa</option>
                                <option value="Departamento" <?php echo $propiedad['pro_tipo'] == 'Departamento' ? 'selected' : ''; ?>>Departamento</option>
                                <option value="Terreno" <?php echo $propiedad['pro_tipo'] == 'Terreno' ? 'selected' : ''; ?>>Terreno</option>
                                <option value="Local Comercial" <?php echo $propiedad['pro_tipo'] == 'Local Comercial' ? 'selected' : ''; ?>>Local Comercial</option>
                                <option value="Otro" <?php echo $propiedad['pro_tipo'] == 'Otro' ? 'selected' : ''; ?>>Otro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pro_provincia">Provincia:</label>
                            <select name="pro_provincia" id="pro_provincia" class="form-control" required>
                                <option value="">Seleccione una Provincia</option>
                                <?php foreach ($provincias as $provincia): ?>
                                    <option value="<?php echo $provincia['provincia_id']; ?>" 
                                        <?php echo $provincia['provincia_id'] == $propiedad['pro_provincia'] ? 'selected' : ''; ?>>
                                        <?php echo $provincia['provincia_nombre']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pro_ciudad">Ciudad:</label>
                            <select name="pro_ciudad" id="pro_ciudad" class="form-control" required>
                                <?php if (!empty($ciudades)): ?>
                                    <?php foreach ($ciudades as $ciudad): ?>
                                        <option value="<?php echo $ciudad['ciudad_id']; ?>" 
                                            <?php echo $ciudad['ciudad_id'] == $propiedad['pro_ciudad'] ? 'selected' : ''; ?>>
                                            <?php echo $ciudad['ciudad_nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">Seleccione una Provincia primero</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pro_direccion">Dirección Exacta:</label>
                            <input type="text" name="pro_direccion" id="pro_direccion" class="form-control" value="<?php echo $propiedad['pro_direccion']; ?>" required>
                        </div>

                        <div class="form-group">
                            <div id="map" style="height: 400px;" class="mb-3"></div>
                        </div>

                        <div class="form-group">
                            <label for="pro_descripcion">Descripción:</label>
                            <textarea name="pro_descripcion" class="form-control" required><?php echo $propiedad['pro_descripcion']; ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pro_baños">Baños:</label>
                                    <input type="number" name="pro_baños" class="form-control" min="0" 
                                        value="<?php echo isset($propiedad['pro_baños']) ? htmlspecialchars($propiedad['pro_baños']) : ''; ?>" 
                                        required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pro_habitaciones">Habitaciones:</label>
                                    <input type="number" name="pro_habitaciones" class="form-control" min="0" 
                                        value="<?php echo isset($propiedad['pro_habitaciones']) ? htmlspecialchars($propiedad['pro_habitaciones']) : ''; ?>" 
                                        required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pro_estacionamientos">Estacionamientos:</label>
                                    <input type="text" name="pro_estacionamientos" id="pro_estacionamientos" class="form-control"
                                        value="<?php echo isset($propiedad['pro_estacionamientos']) ? htmlspecialchars($propiedad['pro_estacionamientos']) : 'N/A'; ?>" 
                                        pattern="[0-9]+|N/A" 
                                        title="Solo números o 'N/A' son permitidos"
                                        placeholder="Ej: 2 o N/A">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pro_area_terreno">Área del Terreno (m²):</label>
                                    <input type="number" name="pro_area_terreno" class="form-control" step="0.01" min="0" 
                                        value="<?php echo $propiedad['pro_area_terreno']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pro_alto_total">Alto Total (m):</label>
                                    <input type="number" name="pro_alto_total" class="form-control" step="0.01" min="0" 
                                        value="<?php echo $propiedad['pro_alto_total']; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pro_nombre_propietario">Nombre Completo del Propietario:</label>
                                    <input type="text" name="pro_nombre_propietario" id="pro_nombre_propietario" class="form-control"
                                        value="<?php echo htmlspecialchars($propiedad['pro_nombre_propietario']); ?>" maxlength="70" 
                                        pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$" 
                                        title="Solo letras y espacios permitidos (máximo 70 caracteres)" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pro_celular_propietario">Celular del Propietario:</label>
                                    <input type="text" name="pro_celular_propietario" class="form-control" 
                                        pattern="[0-9]{10}" title="Ingrese un número de 10 dígitos"
                                        value="<?php echo htmlspecialchars($propiedad['pro_celular_propietario']); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pro_precio">Precio:</label>
                                    <input type="number" name="pro_precio" class="form-control" step="0.01" min="0" 
                                        value="<?php echo $propiedad['pro_precio']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pro_estado">Estado:</label>
                                    <select name="pro_estado" class="form-control">
                                        <option value="Disponible" <?php echo $propiedad['pro_estado'] == 'Disponible' ? 'selected' : ''; ?>>Disponible</option>
                                        <option value="No Disponible" <?php echo $propiedad['pro_estado'] == 'No Disponible' ? 'selected' : ''; ?>>No Disponible</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pro_disponibilidad">Disponibilidad:</label>
                                    <select name="pro_disponibilidad" class="form-control" required>
                                        <option value="Disponible" <?php echo $propiedad['pro_disponibilidad'] == 'Disponible' ? 'selected' : ''; ?>>Disponible</option>
                                        <option value="Ocupado" <?php echo $propiedad['pro_disponibilidad'] == 'Ocupado' ? 'selected' : ''; ?>>Ocupado</option>
                                        <option value="Reservado" <?php echo $propiedad['pro_disponibilidad'] == 'Reservado' ? 'selected' : ''; ?>>Reservado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pro_imagenes">Subir Imágenes:</label>
                            <input type="file" name="pro_imagenes[]" id="pro_imagenes" class="form-control-file" multiple accept="image/*">
                            <div id="previewContainer" class="mt-3">
                                <?php if (!empty($propiedad['pro_imagenes'])): ?>
                                    <?php 
                                        $imagenes = explode(',', $propiedad['pro_imagenes']); 
                                        foreach ($imagenes as $imagen): 
                                            // Rutas alternativas para imágenes
                                            $rutasAlternativas = [
                                                '../../' . trim($imagen),
                                                $imagen  // Ruta original completa
                                            ];
                                            
                                            $rutaImagen = '';
                                            $encontrado = false;
                                            foreach ($rutasAlternativas as $ruta) {
                                                if (file_exists($ruta)) {
                                                    $rutaImagen = $ruta;
                                                    $encontrado = true;
                                                    break;
                                                }
                                            }
                                            
                                            // Debugging: Mostrar rutas intentadas si no se encuentra
                                            if (!$encontrado) {
                                                error_log("Archivo de imagen no encontrado. Intentó con: " . implode(', ', $rutasAlternativas));
                                            }
                                    ?>
                                        <?php if ($encontrado): ?>
                                            <img src="<?php echo htmlspecialchars($rutaImagen); ?>" 
                                                alt="Imagen de propiedad" 
                                                class="img-thumbnail mr-2 mb-2"
                                                style="max-width: 200px;"
                                                onerror="this.onerror=null; this.src='knd/estilosJava/imagenes/imagen_no_disponible.jpg';">
                                        <?php else: ?>
                                            <div class="alert alert-warning">
                                                <p>Imagen no encontrada. Rutas buscadas:</p>
                                                <ul>
                                                    <?php foreach ($rutasAlternativas as $ruta): ?>
                                                        <li><?php echo htmlspecialchars($ruta); ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="pro_planos">Subir Planos (PDF):</label>
                            <input type="file" name="pro_planos" id="pro_planos" class="form-control-file" accept="application/pdf">
                            <?php if (!empty($propiedad['pro_planos'])): ?>
                                <div id="pdfPreviewContainer" class="mt-3">
                                    <?php
                                    $nombreArchivo = basename(trim($propiedad['pro_planos']));
                                    
                                    $rutasAlternativas = [
                                        'knd/uploads/' . $nombreArchivo,
                                        'knd/uploads/' . trim($propiedad['pro_planos']),
                                        $propiedad['pro_planos'],
                                        '../../uploads/' . $nombreArchivo,
                                        '../knd/uploads/' . $nombreArchivo,
                                        '../uploads/' . $nombreArchivo
                                    ];
                                    
                                    $rutaPDF = '';
                                    $encontrado = false;
                                    foreach ($rutasAlternativas as $ruta) {
                                        if (file_exists($ruta)) {
                                            $rutaPDF = $ruta;
                                            $encontrado = true;
                                            break;
                                        }
                                    }
                                    
                                    // Debugging: Agregar verificación de archivo
                                    if (!$encontrado) {
                                        error_log("Archivo PDF no encontrado. Intentó con: " . implode(', ', $rutasAlternativas));
                                    }
                                    ?>
                                    
                                    <?php if ($encontrado): ?>
                                        <div class="pdf-info">
                                            <p>PDF actual: <?php echo htmlspecialchars($nombreArchivo); ?></p>
                                            <iframe id="pdfPreview" 
                                                    src="<?php echo htmlspecialchars($rutaPDF); ?>" 
                                                    class="w-100"
                                                    style="height: 500px;"
                                                    type="application/pdf">
                                            </iframe>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <p>El archivo PDF no se encuentra en el servidor. Por favor, compruebe que el archivo exista en la carpeta uploads.</p>
                                            <p>Rutas buscadas:</p>
                                            <ul>
                                                <?php foreach ($rutasAlternativas as $ruta): ?>
                                                    <li><?php echo htmlspecialchars($ruta); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Actualizar Propiedad</button>
                            <a href="index.php?controller=propiedad&action=index" class="btn btn-secondary">Volver</a>
                        </div>
                    </form>
                    <script>
                        $(document).ready(function() {
                            // Configuración inicial del mapa
                            var map = L.map('map').setView([-0.1807, -78.4678], 7); 
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 18
                            }).addTo(map);

                            var marker = L.marker([-0.1807, -78.4678], { draggable: true }).addTo(map);

                            // Función para centrar el mapa en una ubicación
                            function centerMapOnLocation(provincia, ciudad) {
                                const query = `${ciudad}, ${provincia}, Ecuador`;
                                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        if(data.length > 0) {
                                            const location = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                                            map.setView(location, 12);
                                            marker.setLatLng(location);
                                        }
                                    })
                                    .catch(error => console.error('Error al geocodificar:', error));
                            }

                            // Cargar la ubicación inicial basada en los valores guardados
                            const provinciaInicial = $('#pro_provincia option:selected').text();
                            const ciudadGuardada = '<?php echo addslashes($propiedad["pro_ciudad"]); ?>';
                            
                            // Manejar el cambio de provincia
                            $('#pro_provincia').change(function() {
                                var provinciaId = $(this).val();
                                
                                $.ajax({
                                    url: '../../get_ciudades.php',
                                    type: 'POST',
                                    data: { provincia_id: provinciaId },
                                    success: function(data) {
                                        $('#pro_ciudad').html(data);
                                    }
                                });
                            });

                            // Cargar las ciudades de la provincia seleccionada y centrar el mapa
                            var selectedProvincia = '<?php echo $propiedad["pro_provincia"]; ?>';
                            if (selectedProvincia) {
                                $.ajax({
                                    url: '../../get_ciudades.php',
                                    type: 'POST',
                                    data: { provincia_id: selectedProvincia },
                                    success: function(data) {
                                        $('#pro_ciudad').html(data);
                                        $('#pro_ciudad').val('<?php echo $propiedad["pro_ciudad"]; ?>');
                                        
                                        // Centrar el mapa en la ubicación guardada
                                        const provinciaSeleccionada = $('#pro_provincia option:selected').text();
                                        const ciudadSeleccionada = $('#pro_ciudad option:selected').text();
                                        centerMapOnLocation(provinciaSeleccionada, ciudadSeleccionada);
                                    }
                                });
                            }

                            // Actualizar mapa cuando cambien provincia o ciudad
                            $('#pro_provincia, #pro_ciudad').change(function() {
                                const provincia = $('#pro_provincia option:selected').text();
                                const ciudad = $('#pro_ciudad option:selected').text();
                                if (provincia && ciudad) {
                                    centerMapOnLocation(provincia, ciudad);
                                }
                            });

                            // Actualizar dirección cuando se mueva el marcador
                            marker.on('dragend', function(event) {
                                const position = marker.getLatLng();
                                fetch(`https://nominatim.openstreetmap.org/reverse?lat=${position.lat}&lon=${position.lng}&format=json`)
                                    .then(response => response.json())
                                    .then(data => {
                                        document.getElementById('pro_direccion').value = data.display_name;
                                    })
                                    .catch(error => console.error('Error al obtener dirección:', error));
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

                            // Previsualización de PDFs
                            const proPlanosInput = document.getElementById('pro_planos');
                            const pdfPreviewContainer = document.getElementById('pdfPreviewContainer');
                            const pdfPreview = document.getElementById('pdfPreview');

                            proPlanosInput.addEventListener('change', (event) => {
                                const file = event.target.files[0];
                                if (file && file.type === 'application/pdf') {
                                    pdfPreviewContainer.style.display = 'block';
                                    pdfPreview.src = URL.createObjectURL(file);
                                } else if (file) {
                                    alert('Por favor, seleccione un archivo PDF válido');
                                    proPlanosInput.value = '';
                                }
                            });
                        });
                    </script>
                    <script>
                        document.querySelector('form').addEventListener('submit', function(event) {
                            const estacionamientos = document.getElementById('pro_estacionamientos');

                            if (estacionamientos.value.trim() === '') {
                                estacionamientos.value = 'N/A';
                            }
                        });
                        </script>
                    <?php if (isset($success)): ?>
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                <?php if ($success): ?>
                                    Swal.fire({
                                        title: '¡Éxito!',
                                        text: '✅ Propiedad actualizada correctamente.',
                                        icon: 'success',
                                        confirmButtonText: 'Ir al listado',
                                        confirmButtonColor: '#3085d6',
                                    }).then((result) => {   
                                        window.location.href = "index.php?controller=propiedad&action=index";
                                    });
                                <?php else: ?>
                                    Swal.fire({
                                        title: 'Error',
                                        text: '❌ Hubo un error al actualizar la propiedad.',
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

    <!-- Scripts remains the same -->
    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../../js/sb-admin-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>