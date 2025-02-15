<form>
    <table>
        <tr>
            <td style="border: none !important;">
                <button type="submit" name="orden" value="Nuevo" <?= $_SESSION['rol'] === 0 ? 'style="display: none;"' : '' ?>> Cliente Nuevo </button><br>
            </td>
            <td style="border: none !important;">
                <button type="submit" name="orden" value="Terminar"> Terminar </button>
            </td>
        </tr>
    </table>

</form>
<br>
<table>
    <thead>
        <tr>
            <th><a href="?filtrar=id">ID</a></th>
            <th><a href="?filtrar=first_name">First Name</a></th>
            <th><a href="?filtrar=last_name">Last Name</a></th>
            <th><a href="?filtrar=email">Email</a></th>
            <th><a href="?filtrar=gender">Gender</a></th>
            <th><a href="?filtrar=ip">IP Address</a></th>
            <th>Teléfono</th>
            <th colspan="3"></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tclientes as $cli): ?>
            <tr>
                <td><?= $cli->id ?> </td>
                <td><?= $cli->first_name ?> </td>
                <td><?= $cli->last_name ?> </td>
                <td><?= $cli->email ?> </td>
                <td><?= $cli->gender ?> </td>
                <td><?= $cli->ip_address ?> </td>
                <td><?= $cli->telefono ?> </td>
                <td <?= $_SESSION['rol'] === 0 ? 'style="display: none;"' : '' ?>><a href="#" onclick="confirmarBorrar('<?= $cli->first_name ?>','<?= $cli->id ?>');"  >Borrar</a></td>
                <td <?= $_SESSION['rol'] === 0 ? 'style="display: none;"' : '' ?>><a href="?orden=Modificar&id=<?= $cli->id ?>"  >Modificar</a></td>
                <td><a href="?orden=Detalles&id=<?= $cli->id ?>">Detalles</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<form>
    <button name="nav" value="Primero"><< Primero </button>
    <button name="nav" value="Anterior">< Anterior </button>
    <button name="nav" value="Siguiente"> > Siguiente </button>
    <button name="nav" value="Ultimo"> >> Último </button>
</form>