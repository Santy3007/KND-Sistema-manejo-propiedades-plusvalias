<?php
require_once '../../config/database.php'; // Ajusta la ruta según tu estructura

global $pdo;

// Verificar si el ID de la propiedad está en la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: ID de propiedad no encontrado.");
}

$propiedadId = intval($_GET['id']); // Obtener el ID de la propiedad desde la URL

// Consultar la base de datos para obtener los datos de la propiedad
$stmt = $pdo->prepare("SELECT * FROM pro_propiedades WHERE pro_id = :pro_id");
$stmt->bindParam(':pro_id', $propiedadId, PDO::PARAM_INT);
$stmt->execute();
$propiedad = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si la propiedad existe
if (!$propiedad) {
    die("Error: Propiedad no encontrada.");
}
?>

<?php
$id = isset($_GET['id']) ? $_GET['id'] : ''; // Obtiene el ID si existe
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

<link rel="stylesheet" type="text/css" href="../../css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="../../fonts/font-awesome/css/font-awesome.css">

<link rel="stylesheet" type="text/css" href="../../css/style.css">
<link rel="stylesheet" type="text/css" href="../../css/nivo-lightbox/nivo-lightbox.css">
<link rel="stylesheet" type="text/css" href="../../css/nivo-lightbox/default.css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
    .contenedor-principal {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 20px;
        padding: 20px;
        max-width: 1200px;
        margin: auto;
    }

    /* Ajuste de cada contenedor */
    .contenedor {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        flex: 1;
        min-width: 350px;
        max-width: 100%;
    }


    .contenedor h3 {
        margin-bottom: 15px;
        text-align: center;
    }

    .contenedor input, 
    .contenedor select {
        width: 100%;
        padding: 8px;
        margin: 5px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .btn-calcular {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px;
        width: 100%;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
    }

    .btn-calcular:hover {
        background-color: #0056b3;
    }

    .resumen-simulacion {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        background: #f9f9f9;
        padding: 15px;
        border-radius: 10px;
    }

    /* Estilos para la tabla de amortización */
    .contenedor-tabla {
        flex: 1.3; /* Aumentamos el ancho solo para la tabla */
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        overflow-x: auto; /* Permite desplazamiento horizontal si es necesario */
        min-width: 450px; /* Evita que sea muy pequeño en pantallas reducidas */
    }

    /* Tabla de amortización */
    .tabla-amortizacion {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
    }

    /* Celdas de la tabla */
    .tabla-amortizacion th, .tabla-amortizacion td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
        word-wrap: break-word; /* Ajuste para evitar desbordes */
    }

    /* Fondo para los encabezados */
    .tabla-amortizacion th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
        
</style>

</head>
<body id="page-top" data-spy="scroll" data-target=".navbar-fixed-top">

<nav id="menu" class="navbar navbar-default navbar-fixed-top">
  <div class="container"> 
    <div class="navbar-header">
      <a class="navbar-brand page-scroll" href="#page-top">PlusvaliaBlk</a>
    </div>
  </div>
</nav>

<div id="services">
  <div class="container">
    <div class="section-title">
      <h2>Simulación de Crédito</h2>
      <a href="detalle.php<?php echo $id ? '?id=' . $id : ''; ?>" class="btn-volver">Volver</a>
    </div>   
</div>


<div class="contenedor-principal">

    <!-- Contenedor 1: Formulario de Simulación -->
    <div class="contenedor">
        <h3>Datos del Préstamo</h3>
        <form id="formSimulador">
            <label for="monto">Monto del Préstamo:</label>
            <input type="text" id="monto" name="monto" value="<?php echo $propiedad['pro_precio'] ?? ''; ?>" readonly>

            <label for="fecha_inicio">Fecha de Inicio:</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" required>

            <label for="institucion">Institución Financiera:</label>
            <select id="institucion" name="institucion" required>
                <option value="">Seleccione...</option>
                <?php
                $stmt = $pdo->query("SELECT fin_nombre, fin_tasa_interes FROM fin_instituciones");
                while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$fila['fin_tasa_interes']}'>{$fila['fin_nombre']}</option>";
                }
                ?>
            </select>

            <label for="tasa">Tasa de Interés (%):</label>
            <input type="text" id="tasa" name="tasa" readonly>

            <label for="plazo">Plazo (Años):</label>
            <input type="number" id="plazo" name="plazo" min="1" max="30" required>

            <label for="frecuencia">Frecuencia de Pago:</label>
            <select id="frecuencia" name="frecuencia">
                <option value="12">Mensual</option>
                <option value="4">Trimestral</option>
            </select>

            <label for="metodo">Método de Amortización:</label>
            <select id="metodo" name="metodo">
                <option value="frances">Sistema Francés</option>
                <option value="aleman">Sistema Alemán</option>
            </select>

            <button type="submit" class="btn-calcular">Calcular</button>
        </form>
    </div>

    <!-- Contenedor 2: Resumen de la Simulación -->
    <div class="contenedor">
        <h3>Resumen de la Simulación</h3>
        <div class="resumen-simulacion">
            <p>Pago Mensual: <span id="pagoMensual">$0.00</span></p>
            <p>Total a Pagar: <span id="totalPagar">$0.00</span></p>
            <p>Total Intereses: <span id="totalIntereses">$0.00</span></p>
        </div>
    </div>

    <!-- Contenedor 3: Tabla de Amortización -->
    <div class="contenedor-tabla">
        <h3>Tabla de Amortización</h3>
        <table class="tabla-amortizacion">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Cuota</th>
                    <th>Interés</th>
                    <th>Capital</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody id="tablaAmortizacion"></tbody>
        </table>
    </div>

</div>

<script>
// Asigna la tasa de interés seleccionada
document.getElementById('institucion').addEventListener('change', function() {
    let tasaSeleccionada = parseFloat(this.value);
    document.getElementById('tasa').value = isNaN(tasaSeleccionada) ? '' : tasaSeleccionada.toFixed(2);
});

// Calcula las cuotas al presionar "Calcular"
document.getElementById('formSimulador').addEventListener('submit', function(event) {
    event.preventDefault();

    let monto = parseFloat(document.getElementById('monto').value);
    let tasa = parseFloat(document.getElementById('tasa').value) / 100;
    let plazo = parseInt(document.getElementById('plazo').value);
    let frecuencia = parseInt(document.getElementById('frecuencia').value);
    let metodo = document.getElementById('metodo').value;
    let cuotas = plazo * frecuencia;

    if (isNaN(monto) || isNaN(tasa) || isNaN(plazo) || isNaN(frecuencia)) {
        alert("Por favor, complete todos los campos correctamente.");
        return;
    }

    let tablaAmortizacion = document.getElementById("tablaAmortizacion");
    tablaAmortizacion.innerHTML = ""; // Limpiar la tabla antes de calcular

    let planAmortizacion;
    if (metodo === "frances") {
        planAmortizacion = calcularFrances(monto, cuotas, tasa / frecuencia);
    } else {
        planAmortizacion = calcularAleman(monto, cuotas, tasa / frecuencia);
    }

    planAmortizacion.forEach((fila, index) => {
        let row = tablaAmortizacion.insertRow();
        row.innerHTML = `<td>${index + 1}</td>
                         <td>$${fila.cuota.toFixed(2)}</td>
                         <td>$${fila.interes.toFixed(2)}</td>
                         <td>$${fila.capital.toFixed(2)}</td>
                         <td>$${fila.saldo.toFixed(2)}</td>`;
    });

    document.getElementById('pagoMensual').textContent = "$" + planAmortizacion[0].cuota.toFixed(2);
    document.getElementById('totalPagar').textContent = "$" + (planAmortizacion.reduce((sum, f) => sum + f.cuota, 0)).toFixed(2);
    document.getElementById('totalIntereses').textContent = "$" + (planAmortizacion.reduce((sum, f) => sum + f.interes, 0)).toFixed(2);
});

// Método Francés
function calcularFrances(monto, cuotas, tasa) {
    let cuota = (monto * tasa) / (1 - Math.pow(1 + tasa, -cuotas));
    let saldo = monto;
    let tabla = [];

    for (let i = 1; i <= cuotas; i++) {
        let interes = saldo * tasa;
        let capital = cuota - interes;
        saldo -= capital;
        tabla.push({ cuota, interes, capital, saldo });
    }

    return tabla;
}

// Método Alemán
function calcularAleman(monto, cuotas, tasa) {
    let saldo = monto;
    let cuotaCapital = monto / cuotas;
    let tabla = [];

    for (let i = 1; i <= cuotas; i++) {
        let interes = saldo * tasa;
        let cuota = cuotaCapital + interes;
        saldo -= cuotaCapital;
        tabla.push({ cuota, interes, capital: cuotaCapital, saldo });
    }

    return tabla;
}
</script>





<div id="services">
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
