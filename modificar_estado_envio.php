<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DBU1";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$estado_actual = "";
$id_estado = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
    // Buscar el registro en la tabla EstadoEnvio usando el id_envio
    $id_envio = $_POST['id_envio'];

    $sql = "SELECT id_estado, estado, fecha_actualizacion FROM EstadoEnvio WHERE id_envio = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_envio);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $registro = $result->fetch_assoc();
        $id_estado = $registro['id_estado'];
        $estado_actual = $registro['estado'];
        $fecha_actualizacion = $registro['fecha_actualizacion'];
    } else {
        echo "<p>No se encontró un estado asociado con ese ID de Envío.</p>";
    }

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    // Actualizar el estado en la tabla EstadoEnvio
    $nuevo_estado = $_POST['nuevo_estado'];
    $id_estado = $_POST['id_estado'];

    $sql_update = "UPDATE EstadoEnvio SET estado = ?, fecha_actualizacion = NOW() WHERE id_estado = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $nuevo_estado, $id_estado);

    if ($stmt_update->execute()) {
        echo "<p>Estado actualizado exitosamente.</p>";
        $estado_actual = $nuevo_estado;
        $fecha_actualizacion = date("Y-m-d H:i:s");
    } else {
        echo "<p>Error al actualizar el estado: " . $conn->error . "</p>";
    }

    $stmt_update->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Estado de Envío</title>
</head>
<body>
    <h1>Modificar Estado de Envío</h1>
    
    <!-- Formulario para buscar el estado por ID de envío -->
    <form method="post">
        <label for="id_envio">ID de Envío:</label><br>
        <input type="text" id="id_envio" name="id_envio" required><br><br>
        <button type="submit" name="buscar">Buscar Estado</button>
    </form>

    <?php if (!empty($estado_actual)): ?>
        <h3>Estado Actual:</h3>
        <p>ID Estado: <?= $id_estado ?></p>
        <p>Estado: <?= $estado_actual ?></p>
        <p>Fecha de Actualización: <?= $fecha_actualizacion ?></p>
        
        <!-- Formulario para actualizar el estado -->
        <form method="post">
            <label for="nuevo_estado">Nuevo Estado:</label><br>
            <input type="text" id="nuevo_estado" name="nuevo_estado" required><br><br>
            <input type="hidden" name="id_estado" value="<?= $id_estado ?>">
            <button type="submit" name="actualizar">Actualizar Estado</button>
        </form>
    <?php endif; ?>
</body>
</html>
