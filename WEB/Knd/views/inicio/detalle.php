<?php
require_once '../../config/database.php';
require_once '../../models/Propiedad.php';

global $pdo;

if (!isset($_GET['id'])) {
    echo "Propiedad no encontrada.";
    exit();
}

$propiedadId = $_GET['id'];
$propiedadModel = new Propiedad();
$propiedad = $propiedadModel->getById($propiedadId);

if (!$propiedad) {
    echo "Propiedad no encontrada.";
    exit();
}

$stmtCiudades = $pdo->query("SELECT ciudad_id, ciudad_nombre FROM ciudades");
$ciudades = $stmtCiudades->fetchAll(PDO::FETCH_KEY_PAIR);

$stmtProvincias = $pdo->query("SELECT provincia_id, provincia_nombre FROM provincias");
$provincias = $stmtProvincias->fetchAll(PDO::FETCH_KEY_PAIR);

$imagenes = explode(',', $propiedad['pro_imagenes']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PlusvaliaBlk</title>
<meta name="description" content="">
<meta name="author" content="">

<link rel="shortcut icon" href="../../img/favicon.ico" type="image/x-icon">
<link rel="apple-touch-icon" href="../../img/apple-touch-icon.png">
<link rel="stylesheet" href="../../estilosJava/detalle.css">

<link rel="stylesheet" type="text/css"  href="../../css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="../../fonts/font-awesome/css/font-awesome.css">


<link rel="stylesheet" type="text/css" href="../../css/style.css">
<link rel="stylesheet" type="text/css" href="../../css/nivo-lightbox/nivo-lightbox.css">
<link rel="stylesheet" type="text/css" href="../../css/nivo-lightbox/default.css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>
<body id="page-top" data-spy="scroll" data-target=".navbar-fixed-top">

<nav id="menu" class="navbar navbar-default navbar-fixed-top">
  <div class="container"> 
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      <a class="navbar-brand page-scroll" href="#page-top">PlusvaliaBlk</a>
      <div class="phone"><span>Comunicate:</span>0960511346</div>
    </div>
    
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#portfolio" class="page-scroll">Detalles</a></li>
        <li><a href="#testimonials" class="page-scroll">Opiniones</a></li>
        <li><a href="http://localhost/knd/index.php?controller=login&action=index" class="btn-ingresar">Ingresar</a></li>
      </ul>
    </div>
 
  </div>
</nav>

<div id="services">
  <div class="container">
    <div class="section-title">
      <h2>ㅤ</h2>
      <a href="../../index.php" class="btn-volver">Volver</a>
    </div>
    <div class="contenedor-detalle">
    <div class="galeria-contenedor">
    <div class="imagen-principal">
        <img id="property-image" src="../../<?php echo trim($imagenes[0]); ?>" alt="Imagen Propiedad">
    </div>

    <div class="galeria-miniaturas" id="miniatura-container">
        <?php foreach (array_slice($imagenes, 1) as $index => $imagen): ?>
            <img class="miniatura" src="../../<?php echo trim($imagen); ?>" alt="Imagen Propiedad" onclick="cambiarImagen(<?php echo $index + 1; ?>)">
        <?php endforeach; ?>
    </div>
</div>
    <div class="info-contenedor">
        <div class="info-propiedad">
            <h1><?php echo htmlspecialchars($propiedad['pro_tipo']); ?> en 
                <?php echo isset($ciudades[$propiedad['pro_ciudad']]) 
                    ? htmlspecialchars($ciudades[$propiedad['pro_ciudad']]) 
                    : 'Ciudad desconocida'; ?>
            </h1>
            <h2>Venta desde $<?php echo number_format($propiedad['pro_precio'], 2, '.', ','); ?></h2>
            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($propiedad['pro_direccion']); ?></p>
            <div class="detalle-info">
                <div class="detalle-item"><i class="fas fa-comment-dots"></i> Discripción: <?php echo htmlspecialchars($propiedad['pro_descripcion']); ?></div>
                <div class="detalle-item"><i class="fas fa-expand"></i> Área: <?php echo number_format($propiedad['pro_area_terreno'], 2, '.', ','); ?> m²</div>
                <div class="detalle-item"><i class="fas fa-building"></i> Alto: <?php echo number_format($propiedad['pro_alto_total'], 2, '.', ','); ?> m</div>
                <div class="detalle-item"><i class="fas fa-bath"></i> Baños: <?php echo htmlspecialchars($propiedad['pro_baños']); ?></div>
                <div class="detalle-item"><i class="fas fa-bed"></i> Habitaciones: <?php echo htmlspecialchars($propiedad['pro_habitaciones']); ?></div>
                <div class="detalle-item"><i class="fas fa-car"></i> Estacionamientos: <?php echo !empty($propiedad['pro_estacionamientos']) ? htmlspecialchars($propiedad['pro_estacionamientos']) : 'N/A'; ?></div>
                <div class="detalle-item"><i class="fas fa-user"></i> Propietario: <?php echo htmlspecialchars($propiedad['pro_nombre_propietario']); ?></div> 
            </div>
        </div>

        
        <a href="simulacion.php?id=<?php echo $propiedadId; ?>" class="btn-simulador-credito">Simulación de Crédito</a>
        <style>
          .btn-simulador-credito {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #1a252f;
            color: #ffffff;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 8px;
            text-decoration: none;
            
            width: 150px;
            height: 60px;
            text-align: center;

            line-height: 1;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

          .btn-simulador-credito:hover {
              color: white !important; 
              background-color: #1a252f; 
              transform: scale(1.05); 
          }


        </style>

        <div class="botones-wrapper">
            <div class="botones-container">
                <?php
                    $nombrePropietario = htmlspecialchars($propiedad['pro_nombre_propietario']);
                    $ciudadPropiedad = isset($ciudades[$propiedad['pro_ciudad']]) ? htmlspecialchars($ciudades[$propiedad['pro_ciudad']]) : 'Ciudad desconocida';
                    $mensajeWhatsapp = "Estimado/a $nombrePropietario: Me dirijo a usted con el fin de expresar mi interés en la propiedad ubicada en $ciudadPropiedad. Agradecería recibir más información al respecto. Quedo atento/a a su pronta respuesta. Atentamente, (Ingrese Su Nombre Aqui)";
                    $numeroWhatsApp = $propiedad['pro_celular_propietario'];
                    $whatsappLink = "https://wa.me/593" . preg_replace('/\D/', '', $numeroWhatsApp) . "?text=" . urlencode($mensajeWhatsapp);
                ?>
                <?php if (!empty($propiedad['pro_planos'])): ?>
                    <a href="../../<?php echo htmlspecialchars($propiedad['pro_planos']); ?>" target="_blank" class="btn-ver-planos">
                        <i class="fas fa-file-pdf"></i> Ver Planos
                    </a>
                <?php endif; ?>
                <a href="<?php echo $whatsappLink; ?>" target="_blank" class="btn-contactar-whatsapp">
                    <i class="fab fa-whatsapp"></i> Contactar por WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    let imagenes = <?php echo json_encode($imagenes); ?>;
    let primeraImagenAgregada = false; 

    function cambiarImagen(index) {
        let imagenPrincipal = document.getElementById('property-image');
        let miniaturasContainer = document.getElementById('miniatura-container');

        imagenPrincipal.src = '../../' + imagenes[index];

        if (!primeraImagenAgregada && index !== 0) {
            let miniaturaInicial = document.createElement("img");
            miniaturaInicial.classList.add("miniatura");
            miniaturaInicial.id = "miniatura-principal"; 
            miniaturaInicial.src = '../../' + imagenes[0];
            miniaturaInicial.alt = "Imagen Propiedad";
            miniaturaInicial.onclick = function() { cambiarImagen(0); };

            miniaturasContainer.insertBefore(miniaturaInicial, miniaturasContainer.firstChild);
            primeraImagenAgregada = true; 
        }

        if (index === 0 && primeraImagenAgregada) {
            let miniaturaPrincipal = document.getElementById("miniatura-principal");
            if (miniaturaPrincipal) {
                miniaturaPrincipal.remove(); 
            }
            primeraImagenAgregada = false; 
          
        }

        let miniaturas = document.querySelectorAll('.miniatura');
        miniaturas.forEach(img => img.classList.remove('miniatura-seleccionada'));
        if (miniaturas[index]) {
            miniaturas[index].classList.add('miniatura-seleccionada');
        }
    }
</script>

<div id="services">
  <div class="container">
    <div class="section-title">
      <h2>Ubicación del proyecto</h2>
    </div>

    <div id="map-container">
      <div id="map"></div>
    </div>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let direccion = "<?php echo addslashes($propiedad['pro_direccion']); ?>";
            
            
            let map = L.map('map').setView([0, 0], 15); 
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(direccion)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        let lat = data[0].lat;
                        let lon = data[0].lon;
                        map.setView([lat, lon], 15);

                        L.marker([lat, lon]).addTo(map)
                            .openPopup();
                    } else {
                        console.error("No se encontraron coordenadas para la dirección.");
                    }
                })
                .catch(error => console.error("Error en la geocodificación: ", error));
        });
    </script>

    <style>
        #map-container {
            width: 100%; 
            max-width: 800px;
            margin: 20px auto; 
            text-align: center;
        }

        
        #map {
            width: 100%; 
            height: 400px; 
            border: 2px solid #ddd; 
            border-radius: 10px; 
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</div>
<div id="portfolio">
  <div class="container">
    <div class="section-title">
      <h2>Búsquedas más populares</h2>
    </div>
    <div class="row">
      <div class="col-md-4">
        <div class="testimonial">
          <div class="testimonial-content">
            <p>Inmuebles en venta en Quito</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial">
          <div class="testimonial-content">
            <p>Locales comerciales en alquiler en Quito</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial">
          <div class="testimonial-content">
            <p>Terrenos en venta en Riobamba</p>
          </div>
        </div>
      </div>
      <div class="row"> </div>
      <div class="col-md-4">
        <div class="testimonial">
          <div class="testimonial-content">
            <p>Departamentos baratos en alquiler en Quito</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial">
          <div class="testimonial-content">
            <p>Casas en venta en Ambato</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial">
          <div class="testimonial-content">
            <p>Casas en venta en Ibarra</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div id="testimonials">
  <div class="container">
    <div class="section-title">
      <h2>Opiniones de nuestros clientes</h2>
    </div>
    <div class="row">
      <div class="col-md-4">
        <div class="testimonial">
          <div class="testimonial-image">
            <i class="fas fa-user-circle fa-3x"></i>
          </div>
          <div class="testimonial-content">
            <p>"Gracias a esta página encontré la casa de mis sueños. El proceso de compra fue claro y rápido. El equipo siempre estuvo disponible para resolver mis dudas. ¡Recomiendo completamente este servicio!"</p>
            <div class="testimonial-meta"> - Laura Gómez </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial">
          <div class="testimonial-image">
            <i class="fas fa-user-circle fa-3x"></i>
          </div>
          <div class="testimonial-content">
            <p>"Excelente experiencia de compra. La atención fue personalizada y me guiaron durante todo el proceso, desde la selección hasta la firma del contrato. ¡Estoy muy satisfecho con mi nuevo hogar!"</p>
            <div class="testimonial-meta"> - Roberto Sánchez </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial">
          <div class="testimonial-image">
            <i class="fas fa-user-circle fa-3x"></i>
          </div>
          <div class="testimonial-content">
            <p>"Una plataforma fácil de usar, con opciones claras y atractivas. Pude encontrar exactamente lo que buscaba y en el área que deseaba. Muy recomendados si buscas una propiedad que se ajuste a tus necesidades."</p>
            <div class="testimonial-meta"> - Mariana Pérez </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="footer">
  <div class="container text-center">
    <p>&copy; Blk <a href="http://www.PlusvaliaBlk.com" rel="nofollow">PlusvaliaBlk</a></p>
  </div>
</div>
<script type="text/javascript" src="../../js/jquery.1.11.1.js"></script> 
<script type="text/javascript" src="../../js/bootstrap.js"></script> 
<script type="text/javascript" src="../../js/SmoothScroll.js"></script> 
<script type="text/javascript" src="../../js/nivo-lightbox.js"></script> 
<script type="text/javascript" src="../../js/jqBootstrapValidation.js"></script> 
<script type="text/javascript" src="../../js/contact_me.js"></script> 
<script type="text/javascript" src="../../js/main.js"></script>
<!-- Botón fijo para abrir el formulario -->
<button id="btnEnviarMensaje" class="btn-enviar-mensaje">Enviar mensaje</button>

<!-- Formulario de contacto oculto -->
<div id="formularioMensaje" class="formulario-mensaje">
    <div class="formulario-contenido">
        <span class="cerrar" onclick="cerrarFormulario()">&times;</span>
        <h2>Enviar Mensaje</h2>
        <form id="formMensaje">
            <input type="hidden" id="pro_id" name="pro_id" value="<?php echo $propiedadId; ?>">            
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
            <small class="error" id="errorNombre"></small>

            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" required>
            <small class="error" id="errorCorreo"></small>

            <label for="fecha_cita">Fecha de cita:</label>
            <input type="date" id="fecha_cita" name="fecha_cita" required>
            <small class="error" id="errorFecha"></small>

            <label for="mensaje">Mensaje:</label>
            <textarea id="mensaje" name="mensaje" required></textarea>

            <button type="submit" class="btn-enviar">Enviar</button>
        </form>
    </div>
</div>

<!-- Mensaje de éxito oculto -->
<div id="mensajeExito" class="mensaje-exito">
    <div class="mensaje-contenido">
        <span class="cerrar" onclick="cerrarMensajeExito()">&times;</span>
        <h2>¡Tu mensaje fue enviado con éxito!</h2>
        <button class="btn-reenviar" onclick="volverAEnviar()">Volver a enviar</button>
    </div>
</div>

<!-- Estilos -->
<style>
    .btn-enviar-mensaje {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: red;
        color: white;
        border: none;
        padding: 15px 20px;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        z-index: 1000;
    }

    .formulario-mensaje, .mensaje-exito {
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 300px;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        text-align: center;
        border: 1px solid #ddd;
        display: none;
    }

    .cerrar {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 20px;
        cursor: pointer;
    }

    .formulario-contenido input, 
    .formulario-contenido textarea {
        width: 100%;
        padding: 8px;
        margin: 5px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .formulario-contenido textarea {
        resize: none;
    }

    .btn-enviar {
        background-color: red;
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
        width: 100%;
        border-radius: 10px;
        font-size: 16px;
    }

    .btn-enviar:hover {
        background-color: darkred;
    }

    .btn-reenviar {
        background-color: darkred;
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
        width: 100%;
        border-radius: 10px;
        font-size: 16px;
        margin-top: 10px;
    }

    .btn-reenviar:hover {
        background-color: red;
    }
</style>

<!-- JavaScript con AJAX -->
<script>
    document.getElementById('btnEnviarMensaje').addEventListener('click', function () {
        document.getElementById('formularioMensaje').style.display = 'block';
        document.getElementById('btnEnviarMensaje').style.display = 'none';
    });

    function cerrarFormulario() {
        document.getElementById('formularioMensaje').style.display = 'none';
        document.getElementById('btnEnviarMensaje').style.display = 'block';
    }

    function cerrarMensajeExito() {
        document.getElementById('mensajeExito').style.display = 'none';
        document.getElementById('btnEnviarMensaje').style.display = 'block';
    }

    function volverAEnviar() {
        document.getElementById('mensajeExito').style.display = 'none';
        limpiarFormulario(); // Limpia los campos antes de volver a abrir el formulario
        document.getElementById('formularioMensaje').style.display = 'block';
    }

    function limpiarFormulario() {
        document.getElementById('formMensaje').reset(); // Reinicia todos los campos del formulario
    }

    document.getElementById('formMensaje').addEventListener('submit', function(event) {
        event.preventDefault(); 

        let formData = new FormData(this);

        fetch('../../controllers/guardar_solicitud.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log("Respuesta del servidor:", data);
            if (data.trim() === "success") {
                document.getElementById('formularioMensaje').style.display = 'none';
                document.getElementById('mensajeExito').style.display = 'block';
                limpiarFormulario(); // Limpia los campos después de enviar
            } else {
                alert("Error al enviar la solicitud.");
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>

</body>
</html>
