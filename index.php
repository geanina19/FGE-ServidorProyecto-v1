<?php
session_start();

define('FPAG', 10); // Número de filas por página
define('MAX_INTENTOS', 3); // Máximo de intentos fallidos


require_once 'app/helpers/util.php';
require_once 'app/config/configDB.php';
require_once 'app/models/Cliente.php';
require_once 'app/models/Usuario.php';
require_once 'app/models/AccesoDatosPDO.php';
require_once 'app/controllers/crudclientes.php';
require_once 'vendor/autoload.php';

if (isset($_SESSION['intentos'])) {
    if ($_SESSION['intentos'] >= MAX_INTENTOS) {
        echo "Has superado el número máximo de intentos. Por favor, reinicia el navegador para intentarlo de nuevo.";
        die();
    }
}

if (!isset($_SESSION['usuario'])) {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($_POST['login']) && isset($_POST['clave'])) {
            $login = $_POST['login'];
            $password = $_POST['clave'];

            $db = AccesoDatosPDO::getModelo();
            $usuario = $db->getUsuarioPorLogin($login);

            // Verificar las credenciales
            if ($usuario && password_verify($password, $usuario->password)) {
                // Si las credenciales son correctas iniciar sesión
                $_SESSION['usuario'] = $usuario;
                $_SESSION['rol'] = $usuario->rol;

                // Restablecer intentos fallidos
                unset($_SESSION['intentos']);

                header('Location: index.php');
                exit();
            } else {
                // Si las credenciales son incorrectas
                if (!isset($_SESSION['intentos'])) {
                    $_SESSION['intentos'] = 0;
                }
                $_SESSION['intentos']++;

                // Si se superan los intentos fallidos
                if ($_SESSION['intentos'] >= MAX_INTENTOS) {
                    echo "Has superado el número máximo de intentos. Por favor, reinicia el navegador para intentarlo de nuevo.";
                    die();
                } else {
                    $msg = "Usuario o contraseña incorrectos. Intentos restantes: " . (MAX_INTENTOS - $_SESSION['intentos']);
                }
            }
        }
    }

    require_once 'app/views/login.php';
    exit();
}

//---- PAGINACIÓN ----------------------------------------------------------------------------------------------------------------------------

//---------------
$midb = AccesoDatosPDO::getModelo();

$totalfilas = $midb->numClientes();
if ($totalfilas % FPAG == 0) {
    $posfin = $totalfilas - FPAG;
} else {
    $posfin = $totalfilas - $totalfilas % FPAG;
}

if (!isset($_SESSION['posini'])) {
    $_SESSION['posini'] = 0;
}
$posAux = $_SESSION['posini'];
//--------------

//--------------
$totalNombres = $midb->nombresClientes();

// Calcular la posición final en función del total
if ($totalNombres % FPAG == 0) {
    $posFinFirstName = $totalNombres - FPAG;
} else {
    $posFinFirstName = $totalNombres - ($totalNombres % FPAG);
}


//--------------

// Borro cualquier mensaje 
$_SESSION['msg'] = " ";


//--------------

if (!isset($_SESSION['filtrar'])) {
    $_SESSION['filtrar'] = "ORDER BY id ASC";
}

// Obtenemos los clientes para la paginación
ob_start();
if ($_SERVER['REQUEST_METHOD'] == "GET") {

    // Proceso las ordenes de navegación
    if (isset($_GET['nav'])) {
        switch ($_GET['nav']) {
            case "Primero":
                $posAux = 0;
                break;
            case "Siguiente":
                $posAux += FPAG;
                if ($posAux > $posfin) {
                    $posAux = $posfin;
                }
                break;
            case "Anterior":
                $posAux -= FPAG;
                if ($posAux < 0) {
                    $posAux = 0;
                }
                break;
            case "Ultimo":
                $posAux = $posfin;
                break;
        }
        $_SESSION['posini'] = $posAux;
    }

    if (isset($_GET['nav-detalles'])) {
        switch ($_GET['nav-detalles']) {
            case "Anterior":
                crudDetallesAnterior($_GET['id']);
                break;
            case "Siguiente":
                crudDetallesSiguiente($_GET['id']);
                break;
            case "pdf":
                generarPdf($_GET['id']);
                break;
        }
    }

    // Proceso de ordenes de CRUD clientes
    if (isset($_GET['orden'])) {
        switch ($_GET['orden']) {
            case "Nuevo":
                if (isset($_SESSION['rol']) && $_SESSION['rol'] == 1) {
                    crudAlta();
                } else {
                    echo "No tienes permisos para realizar la acción nuevo.";
                    exit();
                }
                break;
            case "Borrar":
                if (isset($_SESSION['rol']) && $_SESSION['rol'] == 1) {
                    crudBorrar($_GET['id']);
                } else {
                    echo "No tienes permisos para realizar la acción borrar.";
                    exit();
                }
                break;
            case "Modificar":
                if (isset($_SESSION['rol']) && $_SESSION['rol'] == 1) {
                    crudModificar($_GET['id']);
                } else {
                    echo "No tienes permisos para realizar la acción modificar.";
                    exit();
                }
                break;
            case "Detalles":
                crudDetalles($_GET['id']);
                break;
            case "Terminar":
                crudTerminar();
                break;
        }
    }

    // Procesar el filtro seleccionado
    if (isset($_GET['filtrar'])) {
        switch ($_GET['filtrar']) {
            case 'first_name':
                $_SESSION['filtrar'] = 'ORDER BY first_name ASC';
                break;
            case 'last_name':
                $_SESSION['filtrar'] = 'ORDER BY last_name ASC';
                break;
            case 'email':
                $_SESSION['filtrar'] = 'ORDER BY email ASC';
                break;
            case 'gender':
                $_SESSION['filtrar'] = 'ORDER BY gender ASC';
                break;
            case 'ip':
                $_SESSION['filtrar'] = 'ORDER BY ip_address ASC';
                break;
            case 'id':
                $_SESSION['filtrar'] = 'ORDER BY id ASC';
                break;
            default:
                $_SESSION['filtrar'] = 'ORDER BY id ASC';
                break;
        }
    }
}

// POST Formulario de alta o de modificación
else {
    if (isset($_POST['orden'])) {
        switch ($_POST['orden']) {
            case "Nuevo":
                crudPostAlta();
                break;
            case "Modificar":
                crudPostModificar();
                break;
            case "Detalles":; // No hago nada
            case "Anterior":
                crudModificarAnterior($_POST['id']);
                break;
            case "Siguiente":
                crudModificarSiguiente($_POST['id']);
                break;
        }
    }
}

// Si no hay nada en la buffer 
// Cargo genero la vista con la lista por defecto
if (ob_get_length() == 0) {
    $db = AccesoDatosPDO::getModelo();
    $posini = $_SESSION['posini'];

    if (!isset($_SESSION['rol'])) {
        $_SESSION['rol'] = null;
    }

    $tclientes = $db->getClientes($posini, FPAG, $_SESSION['filtrar']);

    require_once "app/views/list.php";
}

$contenido = ob_get_clean();

$msg = $_SESSION['msg'];
// Muestro la página principal con el contenido generado
require_once "app/views/principal.php";
