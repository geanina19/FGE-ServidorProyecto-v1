<?php

// -------------------------------------------

// LAS FUNCIONES QUE NO FORMAN PARTE COMO TAL DEL CRUD , PERO QUE SON NECESARIAS PARA EL FUNCIONAMIENTO DEL CRUD, SE 
// ENCUENTRAN EN : app/helpers/util.php

// -------------------------------------------

function crudBorrar($id)
{
    $db = AccesoDatosPDO::getModelo();
    $resu = $db->borrarCliente($id);
    if ($resu) {
        $_SESSION['msg'] = " El usuario " . $id . " ha sido eliminado.";
    } else {
        $_SESSION['msg'] = " Error al eliminar el usuario " . $id . ".";
    }
}

function crudTerminar()
{
    AccesoDatosPDO::closeModelo();
    session_destroy();
    include_once "app/views/login.php";
}

function crudAlta()
{
    $cli = new Cliente();
    $orden = "Nuevo";
    include_once "app/views/formulario.php";
}

function crudDetalles($id)
{
    $db = AccesoDatosPDO::getModelo();
    $cli = $db->getCliente($id);
    
    // en la carpeta util están los métodos
    $imageUrl = imagenCliente($cli->id);
    
    $imageBandera = apiBandera($cli->ip_address);

    // Obtener la localización geográfica de la IP
    $localizacion = coordenadasLocalizacionIP($cli->ip_address);

    include_once "app/views/detalles.php";
}


function crudDetallesSiguiente($id)
{
    $db = AccesoDatosPDO::getModelo();
    $cli = $db->getClienteSiguiente($id);
    
    if ($cli == false) {
    }

    // en la carpeta util están los métodos
    $imageUrl = imagenCliente($cli->id);

    $imageBandera = apiBandera($cli->ip_address);

    // Obtener la localización geográfica de la IP
    $localizacion = coordenadasLocalizacionIP($cli->ip_address);

    include_once "app/views/detalles.php";
}


function crudDetallesAnterior($id)
{
    $db = AccesoDatosPDO::getModelo();
    $cli = $db->getClienteAnterior($id);

    // en la carpeta util están los métodos
    $imageUrl = imagenCliente($cli->id);

    $imageBandera = apiBandera($cli->ip_address);

    // Obtener la localización geográfica de la IP
    $localizacion = coordenadasLocalizacionIP($cli->ip_address);

    include_once "app/views/detalles.php";
}

function crudModificarSiguiente($id)
{
    $db = AccesoDatosPDO::getModelo();
    $cli = $db->getClienteSiguiente($id); // Obtenemos el cliente
    $orden = "Modificar";

    // En la carpeta util están los métodos
    $imageUrl = imagenCliente($cli->id);

    // Incluimos el formulario, asegurándonos de que $cli esté disponible
    include_once "app/views/formulario.php";
}

function crudModificarAnterior($id)
{
    $db = AccesoDatosPDO::getModelo();
    $cli = $db->getClienteAnterior($id);
    $orden = "Modificar";

    // en la carpeta util están los métodos
    $imageUrl = imagenCliente($cli->id);

    include_once "app/views/formulario.php";
}



function crudModificar($id)
{
    $db = AccesoDatosPDO::getModelo();
    $cli = $db->getCliente($id);
    $orden = "Modificar";

    // en la carpeta util están los métodos
    $imageUrl = imagenCliente($cli->id);

    include_once "app/views/formulario.php";
}

function crudPostAlta()
{
    limpiarArrayEntrada($_POST); // Evitar la posible inyección de código

    // Comprobar que todos los campos están rellenos
    if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['gender']) || empty($_POST['ip_address']) || empty($_POST['telefono'])) {
        $_SESSION['msg'] = "Todos los campos son obligatorios.";
        $orden = "Nuevo";
        include_once "app/views/formulario.php";
        return;
    }

    // Comprobar formato del correo
    if (!verificarCorreo($_POST['email'])) {
        $_SESSION['msg'] = "El correo no tiene un formato correcto.";
        $orden = "Nuevo";
        include_once "app/views/formulario.php";
        return;
    }

    // Comprobar formato de IP
    if (!verificarIP($_POST['ip_address'])) {
        $_SESSION['msg'] = "La dirección IP no tiene un formato correcto.";
        $orden = "Nuevo";
        include_once "app/views/formulario.php";
        return;
    }

    // Comprobar formato de teléfono
    if (!verificarTelefono($_POST['telefono'])) {
        $_SESSION['msg'] = "El teléfono no tiene un formato correcto (999-999-9999).";
        $orden = "Nuevo";
        include_once "app/views/formulario.php";
        return;
    }

    // Comprobar que no existe el correo
    if (verificarExistenciaCorreo($_POST['email'])) {
        $_SESSION['msg'] = "El correo electrónico ya existe.";
        $orden = "Nuevo";
        include_once "app/views/formulario.php";
        return;
    }

    // Comprobar que no existe la IP
    if (verificarExistenciaIp($_POST['ip_address'])) {
        $_SESSION['msg'] = "La dirección IP ya existe.";
        $orden = "Nuevo";
        include_once "app/views/formulario.php";
        return;
    }

    // Comprobar que no existe el teléfono
    if (verificarExistenciaTelefono($_POST['telefono'])) {
        $_SESSION['msg'] = "El teléfono ya existe.";
        $orden = "Nuevo";
        include_once "app/views/formulario.php";
        return;
    }

    // Verificar imagen si se ha subido
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        if (!verificarImagen($_FILES['imagen'])) {
            $_SESSION['msg'] = "La imagen debe ser .jpg o .png y tener un tamaño inferior a 500 KB.";
            $orden = "Nuevo";
            include_once "app/views/formulario.php";
            return;
        }

        // Para comprobar que se carga la imagen
        // var_dump($_FILES['imagen']);
        // die();
    }

    // Si todas las validaciones pasan, proceder a guardar el cliente
    $cli = new Cliente();
    $cli->id            = $_POST['id'];
    $cli->first_name    = $_POST['first_name'];
    $cli->last_name     = $_POST['last_name'];
    $cli->email         = $_POST['email'];
    $cli->gender        = $_POST['gender'];
    $cli->ip_address    = $_POST['ip_address'];
    $cli->telefono      = $_POST['telefono'];

    $db = AccesoDatosPDO::getModelo();



    if ($db->addCliente($cli)) {
        // Obtener el último ID asignado
        $ultimoId = $db->getUltimoId();

        // para ver si esta cogiendo el ultimo id añadido
        // echo $ultimoId;
        // die();

        if ($ultimoId === null) {
            $_SESSION['msg'] = "El usuario se creó, pero no se pudo obtener el ID del cliente.";
            include_once "app/views/formulario.php";
            return;
        }

        // Si se subió una imagen, cambiar el nombre y moverla
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            // Cambiar el nombre de la imagen
            $nuevoNombreImagen = cambiarNombreImagen($ultimoId, $_FILES['imagen']);
            $rutaConImagen = "app/uploads/" . $nuevoNombreImagen;

            // Mostrar las rutas para depuración
            // echo "Archivo temporal: " . $_FILES['imagen']['tmp_name'] . "<br>";
            // echo "Ruta destino: " . $rutaConImagen . "<br>";

            // Mover la imagen a la carpeta uploads
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaConImagen)) {
                $_SESSION['msg'] = "El usuario " . $cli->first_name . " se ha dado de alta correctamente y se ha guardado su imagen.";
            } else {
                $_SESSION['msg'] = "El usuario se creó, pero hubo un problema al guardar la imagen. Error: " . error_get_last()['message'];
            }
        } else {
            $_SESSION['msg'] = "El usuario " . $cli->first_name . " se ha dado de alta correctamente.";
        }
    } else {
        $_SESSION['msg'] = "Error al dar de alta al usuario " . $cli->first_name . ".";
    }
}



function crudPostModificar()
{
    limpiarArrayEntrada($_POST); // Evito la posible inyección de código
    $cli = new Cliente();

    $db = AccesoDatosPDO::getModelo();
    $cli = $db->getCliente($_POST['id']);

    // Comprobamos si hubo algún cambio antes de intentar modificar
    $hayCambios = false;

    // Verificar si hubo cambios en los campos (sin modificar los valores antes de la comparación)
    if ($_POST['first_name'] != $cli->first_name) {
        $hayCambios = true;
    }

    if ($_POST['last_name'] != $cli->last_name) {
        $hayCambios = true;
    }

    if ($_POST['email'] != $cli->email) {
        // Si el correo ha cambiado, verificar si ya existe otro cliente con el mismo correo
        if (verificarExistenciaCorreo($_POST['email'])) {
            $_SESSION['msg'] = "El correo ya existe.";
            $orden = "Modificar";
            $imageUrl = imagenCliente($cli->id);
            include_once "app/views/formulario.php";
            return;
        }

        if (!verificarCorreo($_POST['email'])) {
            $_SESSION['msg'] = "El correo no tiene un formato correcto.";
            $orden = "Modificar";
            $imageUrl = imagenCliente($cli->id);
            include_once "app/views/formulario.php";
            return;
        }

        $hayCambios = true;
    }

    if ($_POST['gender'] != $cli->gender) {
        $hayCambios = true;
    }

    // Verificar si la IP ha cambiado
    if ($_POST['ip_address'] != $cli->ip_address) {
        // Verificar si la IP ya existe
        if (verificarExistenciaIp($_POST['ip_address'])) {
            $_SESSION['msg'] = "La IP ya existe.";
            $orden = "Modificar";
            $imageUrl = imagenCliente($cli->id);
            include_once "app/views/formulario.php";
            return;
        }

        // Verificar si la IP tiene un formato correcto
        if (!verificarIP($_POST['ip_address'])) {
            $_SESSION['msg'] = "La IP no es correcta.";
            $orden = "Modificar";
            $imageUrl = imagenCliente($cli->id);
            include_once "app/views/formulario.php";
            return;
        }
        $hayCambios = true;
    }

    // Verificar si el teléfono ha cambiado
    if ($_POST['telefono'] != $cli->telefono) {
        // Verificar si el teléfono ya existe
        if (verificarExistenciaTelefono($_POST['telefono'])) {
            $_SESSION['msg'] = "El teléfono ya existe.";
            $imageUrl = imagenCliente($cli->id);
            $orden = "Modificar";
            include_once "app/views/formulario.php";
            return;
        }

        // Verificar si el teléfono tiene un formato correcto
        if (!verificarTelefono($_POST['telefono'])) {
            $_SESSION['msg'] = "El teléfono no tiene el formato correcto (999-999-9999).";
            $orden = "Modificar";
            $imageUrl = imagenCliente($cli->id);
            include_once "app/views/formulario.php";
            return;
        }
        $hayCambios = true;
    }

    // Verificar imagen si se ha subido
    if (isset($_FILES['imagenCambio']) && $_FILES['imagenCambio']['error'] === UPLOAD_ERR_OK) {
        if (!verificarImagen($_FILES['imagenCambio'])) {
            $_SESSION['msg'] = "La imagen debe ser .jpg o .png y tener un tamaño inferior a 500 KB.";
            $orden = "Modificar";
            $imageUrl = imagenCliente($cli->id);
            include_once "app/views/formulario.php";
            return;
        }
    }

    // Si hubo cambios, proceder con la actualización
    if ($hayCambios) {
        // Actualizamos los valores del cliente solo después de comprobar los cambios
        $cli->first_name = $_POST['first_name'];
        $cli->last_name = $_POST['last_name'];
        $cli->email = $_POST['email'];
        $cli->gender = $_POST['gender'];
        $cli->ip_address = $_POST['ip_address'];
        $cli->telefono = $_POST['telefono'];

        // Ejecutar la modificación
        if ($db->modCliente($cli)) {

            // Si se subió una imagen, cambiar el nombre y moverla
            if (isset($_FILES['imagenCambio']) && $_FILES['imagenCambio']['error'] === UPLOAD_ERR_OK) {

                // Eliminar imágenes previas del cliente
                eliminarImagenAnterior($_POST['id']);

                // Cambiar el nombre de la imagen
                $nuevoNombreImagen = cambiarNombreImagen($_POST['id'], $_FILES['imagenCambio']);
                $rutaConImagen = "app/uploads/" . $nuevoNombreImagen;

                // Mover la imagen a la carpeta uploads
                if (move_uploaded_file($_FILES['imagenCambio']['tmp_name'], $rutaConImagen)) {
                    $_SESSION['msg'] = "El usuario " . $cli->id . " se ha dado de modificado correctamente y se ha guardado su imagen nueva.";
                } else {
                    $_SESSION['msg'] = "El usuario " . $cli->id . " ha sido modificado, pero hubo un problema al guardar la imagen.";
                }
            } else {
                $_SESSION['msg'] = "El usuario " . $cli->id . " ha sido modificado.";
            }
        } else {
            $_SESSION['msg'] = "Error al modificar el usuario.";
        }
    } else {
        // Si se subió una imagen, cambiar el nombre y moverla
        if (isset($_FILES['imagenCambio']) && $_FILES['imagenCambio']['error'] === UPLOAD_ERR_OK) {

            // Eliminar imágenes previas del cliente
            eliminarImagenAnterior($_POST['id']);

            // Cambiar el nombre de la imagen
            $nuevoNombreImagen = cambiarNombreImagen($_POST['id'], $_FILES['imagenCambio']);
            $rutaConImagen = "app/uploads/" . $nuevoNombreImagen;

            // Mover la imagen a la carpeta uploads
            if (move_uploaded_file($_FILES['imagenCambio']['tmp_name'], $rutaConImagen)) {
                $_SESSION['msg'] = "El usuario " . $cli->id . " se ha dado de modificado correctamente y se ha guardado su imagen nueva.";
            } else {
                $_SESSION['msg'] = "El usuario " . $cli->id . " ha sido modificado, pero hubo un problema al guardar la imagen.";
            }
        } else {
            $_SESSION['msg'] = "No hubo cambios en los datos del usuario.";
        }
    }
}

function filtrarPorNombre($posicion, $cuantos) {}


//-----------------------------COMPROBACIONES

// if (!is_dir("app/uploads")) {
    //     echo "NO existe esa carpeta";
    //     die();
    // } else {
    //     echo "Si existe esa carpeta";
    //     die();
    // }

    // if (!is_writable("app/uploads")) {
    //     echo "NO se puede escribir";
    //     die();
    // } else {
    //     echo "Si se puede escribir";
    //     die();
    // }