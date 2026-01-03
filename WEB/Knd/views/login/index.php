<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    
    <!-- Custom styles -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilosJava/login.css">
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image" style="background-image: url('estilosJava/imagenes/Plusva.png');"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Iniciar Sesión</h1>
                                    </div>
                                    <form id="loginForm" action="index.php?controller=login&action=authenticate" method="post">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user" id="email" name="email" required placeholder="Correo Electrónico">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" id="password" name="password" required placeholder="Contraseña">
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Recuerdame</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">Ingresar</button>
                                        <a href="index.html" class="btn btn-secondary btn-user btn-block">
                                            <i class="fas fa-arrow-left fa-fw"></i> Volver
                                        </a>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot_password.php">¿Olvidé mi contraseña?</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ventana emergente para errores -->
    <div id="errorModal" class="modal-error">
        <div class="modal-content-error">
            <span class="close-error" onclick="closeErrorModal()">&times;</span>
            <h2>Error</h2>
            <p id="errorMessage">Credenciales incorrectas.</p>
            <button onclick="closeErrorModal()">Cerrar</button>
        </div>
    </div>
    
    <!-- Ventana emergente para lockout -->
    <div id="lockoutModal" class="modal-lockout">
        <div class="modal-content-lockout">
            <span class="close-lockout" onclick="closeLockoutModal()">&times;</span>
            <h2>Cuenta Bloqueada</h2>
            <p>Intentaste ingresar 3 veces sin éxito.<br>Espera <span id="lockoutCountdown"></span> segundos para volver a intentar.</p>
        </div>
    </div>
    
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    
    <!-- Custom scripts -->
    <script src="js/sb-admin-2.min.js"></script>
    <script src="estilosJava/login.js"></script>
</body>
</html>
