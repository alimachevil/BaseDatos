<?php
session_start();

if (!isset($_SESSION['codigo'])) {
    header("Location: login.html");
    exit();
}

$codigo = $_SESSION['codigo'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Envío</title>
</head>
<body>
    <h1>Confirmación de Envío</h1>
    <p>Su envío ha sido registrado exitosamente.</p>
    <p>Código de Envío: <?php echo $codigo; ?></p>
</body>
</html>
