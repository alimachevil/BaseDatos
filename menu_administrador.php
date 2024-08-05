<?php
session_start();

// Destruir la sesión y sus variables
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Administrador</title>
</head>
<body>
    <h1>Menú Administrador</h1>
    <button onclick="location.href='revision_envio.php'">Revisar Envío</button><br>
    <button onclick="location.href='estados_envios.php'">Administrar Estado de Envíos</button><br>
    <button onclick="location.href='administrar_usuarios.php'">Administrar Usuarios</button><br>
    <button onclick="location.href='administrar_agencias.php'">Administrar Agencias</button><br>
    <button onclick="location.href='rastreo_envios.php'">Rastrear Envíos</button><br>
    <button onclick="location.href='imprimir_envios.php'">Visualizar Envíos</button><br>
    <button onclick="location.href='imprimir_comprobantes.php'">Visualizar Comprobantes</button><br>
</body>
</html>
