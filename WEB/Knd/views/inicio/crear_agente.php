<?php
require_once '../../config/database.php';
require '../../PHPMailer/PHPMailer.php';
require '../../PHPMailer/SMTP.php';
require '../../PHPMailer/Exception.php';

global $pdo;
$success = false;
$correoAdmin = "alexanderasanza2004@gmail.com"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nombre = $_POST['per_nombre'];
  $apellido = $_POST['per_apellido'];
  $email = $_POST['per_email'];
  $password = $_POST['per_password'];
  $razon = $_POST['razon_trabajo']; 
  $trabajaOtro = $_POST['trabaja_otro'];
  $empresaNombre = isset($_POST['empresa_nombre']) ? $_POST['empresa_nombre'] : null;
  $rol_id = 20; 
  $status = 'P'; 

  $stmt = $pdo->prepare("SELECT per_id FROM perfiles WHERE per_email = ?");
  $stmt->execute([$email]);

  if ($stmt->rowCount() > 0) {
      $success = false;
  } else {
      $stmt = $pdo->prepare("INSERT INTO perfiles (per_nombre, per_apellido, per_email, per_password, rol_id, per_status) 
                             VALUES (?, ?, ?, ?, ?, ?)");
      if ($stmt->execute([$nombre, $apellido, $email, $password, $rol_id, $status])) {
          $per_id = $pdo->lastInsertId(); 
          if ($trabajaOtro === "Sí" && !empty($empresaNombre)) {
              $stmtEmpresa = $pdo->prepare("INSERT INTO empresas (emp_nombre, per_id) VALUES (?, ?)");
              $stmtEmpresa->execute([$empresaNombre, $per_id]);
          }

          $success = true;

          $mail = new PHPMailer\PHPMailer\PHPMailer();
          $mail->isSMTP();
          $mail->Host = 'smtp.gmail.com';
          $mail->SMTPAuth = true;
          $mail->Username = 'uffthevear@gmail.com';
          $mail->Password = 'iuix petm glgz jjqz';
          $mail->SMTPSecure = 'tls';
          $mail->Port = 587;
          $mail->CharSet = 'UTF-8';

          $mail->setFrom('uffthevear@gmail.com', 'Solicitud de Agente - PlusvaliaBlk');
          $mail->addAddress($correoAdmin);

          $mensajeCorreo = "📩 **Nueva Solicitud de Agente** 📩\n\n"
                         . "👤 Nombre: $nombre $apellido\n"
                         . "📧 Email: $email\n"
                         . "📝 Razón para trabajar con nosotros:\n$razon\n\n";
          
          if ($trabajaOtro === "Sí" && !empty($empresaNombre)) {
              $mensajeCorreo .= "🏢 Trabaja en otra empresa: Sí\n"
                              . "🏢 Nombre de la Empresa: $empresaNombre\n\n";
          } else {
              $mensajeCorreo .= "🏢 Trabaja en otra empresa: No\n\n";
          }

          $mensajeCorreo .= "⚠️ Recuerda aprobar o rechazar la solicitud desde tu panel de administrador.";

          $mail->Subject = "Nueva solicitud de Agente";
          $mail->Body = $mensajeCorreo;
          $mail->send();
      } else {
          $success = false;
      }
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de Agente - PlusvaliaBlk</title>
    <link rel="stylesheet" type="text/css" href="../../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body id="page-top">


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
            <h2>Registro de Agente</h2>
            <a href="http://localhost/knd/" class="btn btn-primary">Volver</a>
        </div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="contenedor">
                    <form method="POST" action="crear_agente.php">
                        <label for="per_nombre">Nombre:</label>
                        <input type="text" id="per_nombre" name="per_nombre" required class="form-control">

                        <label for="per_apellido">Apellido:</label>
                        <input type="text" id="per_apellido" name="per_apellido" required class="form-control">

                        <label for="per_email">Correo Electrónico:</label>
                        <input type="email" id="per_email" name="per_email" required class="form-control">

                        <label for="per_password">Contraseña:</label>
                        <input type="password" id="per_password" name="per_password" required class="form-control">

                        <label for="razon_trabajo">Cuéntanos por qué quieres trabajar con nosotros:</label>
                        <textarea id="razon_trabajo" name="razon_trabajo" rows="4" required class="form-control"></textarea>

                        <label for="trabaja_otro">¿Trabaja en otra empresa?</label>
                        <select id="trabaja_otro" name="trabaja_otro" class="form-control" onchange="mostrarCampoEmpresa()">
                            <option value="No">No</option>
                            <option value="Sí">Sí</option>
                        </select>

                        <div id="empresa_nombre_div" style="display: none;">
                            <label for="empresa_nombre">Nombre de la Empresa:</label>
                            <input type="text" id="empresa_nombre" name="empresa_nombre" class="form-control">
                        </div>


                        <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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

<!-- Scripts -->
<script src="../../js/jquery.1.11.1.js"></script>
<script src="../../js/bootstrap.js"></script>

<?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: '<?= $success ? "✅ Solicitud Enviada" : "❌ Error en el Registro" ?>',
                text: '<?= $success ? "Su solicitud ha sido enviada y está pendiente de aprobación. Le informaremos pronto." : "El correo ya está registrado o hubo un problema. Intente nuevamente." ?>',
                icon: '<?= $success ? "success" : "error" ?>',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '<?= $success ? "#3085d6" : "#d33" ?>'
            }).then(() => {
                if (<?= $success ? "true" : "false" ?>) {
                    window.location.href = "http://localhost/knd/"; 
                }
            });
        });
    </script>
<?php endif; ?>

<script>
function mostrarCampoEmpresa() {
    let select = document.getElementById("trabaja_otro");
    let campo = document.getElementById("empresa_nombre_div");
    campo.style.display = select.value === "Sí" ? "block" : "none";
}
</script>
</body>
</html>
