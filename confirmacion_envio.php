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
    <p>Dirigirse a Agencia para registrar sus paquetes.</p>
    
    <!-- Formulario con botón para redirigir a menu_usuarios.php -->
    <form action="menu_usuarios.php" method="get">
        <button type="submit">Volver al Menú</button>
    </form>
</body>
</html>

<?php
// Limpiar y destruir la sesión
session_unset();
session_destroy();
?>
