    <?php
    session_start();

    // Incluir la configuración de la base de datos
    require_once 'config/database.php';
    $controller = isset($_GET['controller']) ? $_GET['controller'] : 'inicio';
    $action = isset($_GET['action']) ? $_GET['action'] : 'index';
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    

    if (!isset($_SESSION['user_id']) && $controller != 'login' && $controller != 'inicio' && $controller != 'propiedad' && $action != 'show') {
        header('Location: index.php?controller=login&action=index');
        exit();
    }





    spl_autoload_register(function ($class) {
        if (file_exists('controllers/' . $class . '.php')) {
            require_once 'controllers/' . $class . '.php';
        } elseif (file_exists('models/' . $class . '.php')) {
            require_once 'models/' . $class . '.php';
        }
    });

    $controllerName = ucfirst($controller) . 'Controller';

    if (class_exists($controllerName)) {
        $controllerInstance = new $controllerName();
        if (method_exists($controllerInstance, $action)) {
            if ($id) {
                $controllerInstance->$action($id); // Pasar el ID como argumento
            } else {
                $controllerInstance->$action(); // Llamar al método sin argumentos
            }
        } else {
            die("La acción $action no existe en el controlador $controllerName.");
        }
    } else {
        die("El controlador $controllerName no existe.");
    }
    ?>
