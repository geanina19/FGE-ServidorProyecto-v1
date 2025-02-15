<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>CRUD CLIENTES</title>
    <link href="web/css/default.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="web/js/funciones.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/ol@v10.4.0/dist/ol.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v10.4.0/ol.css">

</head>

<body>
    <div id="container" style="width: 950px;">
        <div id="header">
            <h1>MIS CLIENTES CRUD versión 1.0</h1>
        </div>
        <div id="aviso" style="color:red;">
            <?= $msg ?>
        </div>
        <div id="content" style="margin-left:-25px;">
            <?= $contenido ?>
        </div>
    </div>
</body>

</html>