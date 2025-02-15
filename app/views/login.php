<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>CRUD DE USUARIOS</title>
    <link href="web/css/default.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div id="container" style="width: 600px;">
        <div id="header">
            <h1>Login User Proyecto Tema9</h1>
        </div>
        <div id="content">
            <hr>
            <form method="post">
                <table style="max-width: 600px;">
                    <tr>
                        <td style="border: none !important;"><?= isset($msg) ? $msg : '' ?></td>
                    </tr>
                    <tr>
                        <td>Login </td>
                        <td>
                            <input type="text" name="login" value="<?= isset($_POST['login']) ? $_POST['login'] : '' ?>" size="8">
                        </td>
                    </tr>
                    <tr>
                        <td>Contraseña </td>
                        <td>
                            <input type="password" name="clave" value="" style="width: 90%;padding: 10px;margin-bottom: 15px;border: 1px solid #ccc;border-radius: 4px;">
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none !important;"><input type="submit" name="enviar" value="Enviar"></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</body>

</html>