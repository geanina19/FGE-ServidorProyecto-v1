<hr>
<form method="POST" enctype="multipart/form-data">

    <?php if ($orden == "Nuevo"): ?>
        <!-- Campo para añadir imagen fuera de la tabla -->
        <div style="margin-bottom: 20px;">
            <label for="imagen">Añadir Imagen:</label><br>
            <input type="file" id="imagen" name="imagen" accept="image/png, image/jpg" style="margin-top: 10px;">
        </div>
    <?php endif; ?>

    <table style="min-width: 600px; width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 20px; margin-right: 70px;">
                <label for="id" <?= ($orden == "Nuevo") ? 'style="display:none;"' : "" ?>>Id:</label>
                <input type="text" name="id" readonly value="<?= ($orden == "Modificar") ? $cli->id : '' ?>" <?= ($orden == "Nuevo") ? 'style="display:none;"' : "" ?>>
                <br><br>

                <label for="first_name">Nombre:</label>
                <input type="text" id="first_name" name="first_name" value="<?= ($orden == "Nuevo") ? (isset($_POST['first_name']) ? $_POST['first_name'] : '') : $cli->first_name  ?>">
                <br><br>

                <label for="last_name">Apellido:</label>
                <input type="text" id="last_name" name="last_name" value="<?= ($orden == "Nuevo") ? (isset($_POST['last_name']) ? $_POST['last_name'] : '') : $cli->last_name ?>">
                <br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= ($orden == "Nuevo") ? (isset($_POST['email']) ? $_POST['email'] : '') : $cli->email ?>">
                <br><br>

                <label for="gender">Género:</label>
                <input type="text" id="gender" name="gender" value="<?= ($orden == "Nuevo") ? (isset($_POST['gender']) ? $_POST['gender'] : '') : $cli->gender ?>">
                <br><br>

                <label for="ip_address">Dirección IP:</label>
                <input type="text" id="ip_address" name="ip_address" value="<?= ($orden == "Nuevo") ? (isset($_POST['ip_address']) ? $_POST['ip_address'] : '') : $cli->ip_address ?>">
                <br><br>

                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?= ($orden == "Nuevo") ? (isset($_POST['telefono']) ? $_POST['telefono'] : '') : $cli->telefono ?>">
            </td>

            <?php if (!empty($imageUrl)): ?>
                <!-- Mostrar imagen solo si existe $imageUrl -->
                <td style="width: 50%; vertical-align: top; text-align: center; position: relative;">
                    <img src="<?= $imageUrl ?>" alt="Imagen del cliente" style="width: 150px; height: 150px; border: 1px solid #ccc; border-radius: 10px;">
                    
                    <?php if ($orden != "Nuevo"): ?>
                        <br><br>
                        <label for="imagen">Cambiar Imagen:</label>
                        <input type="file" id="imagenCambio" name="imagenCambio" accept="image/png, image/jpeg" style="margin-top: 10px;">
                    <?php endif; ?>
                </td>
            <?php endif; ?>

        </tr>
    </table>

    <!-- <input type="hidden" name="id" value="<=$cli->id?>"> -->

    <?php if ($orden != "Nuevo"): ?>
        <button type="submit" name="orden" value="Anterior" style="background-color: burlywood;">Anterior</button>
    <?php endif; ?>

    <input type="submit" name="orden" value="<?= $orden ?>" style="background-color: brown;">
    <input type="submit" name="orden" value="Volver">

    <?php if ($orden != "Nuevo"): ?>
        <button type="submit" name="orden" value="Siguiente" style="background-color: burlywood;">Siguiente</button>
    <?php endif; ?>

</form>
