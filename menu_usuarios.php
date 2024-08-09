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
    <title>Menú Usuarios</title>
</head>
<body>
    <h1>Menú Usuarios</h1>
    <button onclick="location.href='login.html'">Registrar Paquete</button><br>
    <button onclick="location.href='rastreo_envio.php'">Rastrear Envío</button>
    <button onclick="location.href='visualizar_envios_usuarios.php'">Visualizar Envios</button>
</body>
</html>
