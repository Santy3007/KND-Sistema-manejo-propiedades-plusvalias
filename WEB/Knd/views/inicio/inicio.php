<?php
require_once 'config/database.php';
require_once 'models/Propiedad.php';

global $pdo;
$propiedadModel = new Propiedad();
$propiedades = array_filter($propiedadModel->getAll(), function($propiedad) {
    return $propiedad['pro_estado'] !== 'No Disponible';
});

$stmtProvincias = $pdo->query("SELECT provincia_id, provincia_nombre FROM provincias");
$provincias = $stmtProvincias->fetchAll(PDO::FETCH_KEY_PAIR);

$stmtCiudades = $pdo->query("SELECT ciudad_id, ciudad_nombre FROM ciudades");
$ciudades = $stmtCiudades->fetchAll(PDO::FETCH_KEY_PAIR);
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PlusvaliaBlk</title>
<meta name="description" content="">
<meta name="author" content="">

<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
<link rel="apple-touch-icon" href="img/apple-touch-icon.png">
<link rel="stylesheet" href="estilosJava/inicio.css">


<link rel="stylesheet" type="text/css"  href="css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.css">


<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/nivo-lightbox/nivo-lightbox.css">
<link rel="stylesheet" type="text/css" href="css/nivo-lightbox/default.css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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
        <li><a href="#services" class="page-scroll">Propiedades</a></li>
        <li><a href="#portfolio" class="page-scroll">Detalles</a></li>
        <li><a href="#testimonials" class="page-scroll">Opiniones</a></li>
        <li><a href="index.php?controller=login&action=index" class="btn-ingresar">Ingresar</a></li>
      </ul>
    </div>
 
  </div>
</nav>
<header id="header">
  <div class="intro">
    <div class="overlay">
      <div class="container">
        <div class="row">
          <div class="col-md-8 col-md-offset-2 intro-text">
            <h1>Encuentra tu hogar</h1>
            <div class="search-bar">
                <form id="searchForm" class="filter-form">
                    <input type="hidden" name="tipo_busqueda" id="tipo_busqueda" value="alquilar"> 

                    <select name="pro_tipo" id="pro_tipo" class="filter-select">
                        <option value="">Seleccione tipo de propiedad</option>
                        <option value="Casa">Casa</option>
                        <option value="Departamento">Departamento</option>
                        <option value="Terreno">Terreno</option>
                        <option value="Local Comercial">Local Comercial</option>
                        <option value="Otro">Otro</option>
                    </select>

                    <input type="text" id="busqueda" name="busqueda" placeholder="Ubicación o características (ej: piscina)" class="filter-input">

                    <button type="button" id="buscarBtn" class="filter-button">Buscar</button>
                </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const buscarBtn = document.getElementById("buscarBtn");
    const tipoPropiedad = document.getElementById("pro_tipo");
    const busqueda = document.getElementById("busqueda");
    const propiedadesGrid = document.querySelector(".propiedades-grid");

    function actualizarPropiedades() {
        let tipo = tipoPropiedad.value;
        let query = busqueda.value;

        fetch(`index.php?controller=propiedad&action=filtrar&pro_tipo=${tipo}&busqueda=${query}`)
          .then(response => response.json())
          .then(data => {
              propiedadesGrid.innerHTML = "";
              data.forEach(propiedad => {
                  let imgSrc = propiedad.pro_imagenes.split(",")[0];
                  let html = `
                      <div class="propiedad">
                          <div class="image-container">
                              <img src="${imgSrc}" alt="Imagen Propiedad" class="property-image">
                          </div>
                          <h2>$${parseFloat(propiedad.pro_precio).toLocaleString()}</h2>
                          <p>${propiedad.pro_tipo} en ${propiedad.ciudad_nombre}</p>
                          <div class="detalles">
                              <div class="detalle-item"><span>📏</span> ${propiedad.pro_area_terreno} m²</div>
                              <div class="detalle-item"><span>🏗️</span> ${propiedad.pro_alto_total} m</div>
                              <div class="detalle-item"><span>📞</span> ${propiedad.pro_celular_propietario}</div>
                          </div>
                          <a href="views/inicio/detalle.php?id=${propiedad.pro_id}" class="btn-detalles">Ver Detalles</a>
                      </div>
                  `;
                  propiedadesGrid.innerHTML += html;
              });
          })
          .catch(error => console.error("Error en la búsqueda:", error));
    }

    buscarBtn.addEventListener("click", actualizarPropiedades);
    tipoPropiedad.addEventListener("change", actualizarPropiedades);
    busqueda.addEventListener("input", actualizarPropiedades);
});
</script>

<div id="get-touch">
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-md-6 col-md-offset-1">
        <h3>Somos tu mejor opción</h3>
        <p>PlusvaliaBlk</p>
      </div>
      <div class="col-xs-12 col-md-4">
        <a href="views/inicio/crear_agente.php" class="btn btn-custom btn-lg page-scroll">Trabaja con nosotros</a>
      </div>
    </div>
  </div>
</div>

<div id="services">
  <div class="container">
    <div class="section-title">
      <h2>Propiedades</h2>
    </div>
    <div class="propiedades-grid">
      <?php foreach ($propiedades as $propiedad): ?>
          <div class="propiedad">
              <div class="image-container">
                  <img src="<?php echo explode(',', $propiedad['pro_imagenes'])[0]; ?>" alt="Imagen Propiedad" class="property-image">
                  <input type="hidden" class="image-list" value="<?php echo htmlspecialchars($propiedad['pro_imagenes']); ?>">
              </div>
  
              <h2>$<?php echo number_format($propiedad['pro_precio'], 0, ',', '.'); ?></h2>
              <p><?php echo $propiedad['pro_tipo']; ?> en 
                  <?php 
                      echo isset($ciudades[$propiedad['pro_ciudad']]) 
                          ? htmlspecialchars($ciudades[$propiedad['pro_ciudad']]) 
                          : 'Ciudad desconocida'; 
                  ?>
              </p>
              <div class="detalles">
                  <div class="detalle-item">
                      <span class="icon">📏</span>
                      <span>Área del Terreno:</span>
                      <span><?php echo number_format($propiedad['pro_area_terreno'], 2, '.', ','); ?> m²</span>
                  </div>
                  <div class="detalle-item">
                      <span class="icon">🏗️</span>
                      <span>Alto Total:</span>
                      <span><?php echo number_format($propiedad['pro_alto_total'], 2, '.', ','); ?> m</span>
                  </div>
                  <div class="detalle-item">
                      <span class="icon">📞</span>
                      <span>Contacto:</span>
                      <span><?php echo htmlspecialchars($propiedad['pro_celular_propietario']); ?></span>
                  </div>
              </div>
  
              <a href="views/inicio/detalle.php?id=<?php echo $propiedad['pro_id']; ?>" class="btn-detalles">Ver Detalles</a>
          </div>
      <?php endforeach; ?>
  </div>  
  </div>
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
<script type="text/javascript" src="js/jquery.1.11.1.js"></script> 
<script type="text/javascript" src="js/bootstrap.js"></script> 
<script type="text/javascript" src="js/SmoothScroll.js"></script> 
<script type="text/javascript" src="js/nivo-lightbox.js"></script> 
<script type="text/javascript" src="js/jqBootstrapValidation.js"></script> 
<script type="text/javascript" src="js/contact_me.js"></script> 
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>
