<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    $numero_documento = $_POST['numero_documento'];

    // Consulta para obtener los envíos asociados al número de documento
    $sql = "SELECT numero_orden, codigo, fecha, monto_final FROM Envios WHERE numero_documento = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $numero_documento);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si hay resultados
    if ($result->num_rows > 0) {
        $envios = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $envios = null;
        $error_message = "No se encontraron envíos para el número de documento proporcionado.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Envíos por Usuario</title>
</head>
<body>
    <h1>Visualizar Envíos por Usuario</h1>
    <form action="visualizar_envios_usuarios.php" method="post">
        <label for="numero_documento">Número de Documento:</label>
        <input type="text" id="numero_documento" name="numero_documento" required><br>
        <button type="submit">Buscar Envíos</button>
    </form>

    <?php if (isset($envios) && $envios): ?>
        <h2>Envíos Asociados</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Número de Orden</th>
                    <th>Código</th>
                    <th>Fecha</th>
                    <th>Monto Final (S/.)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($envios as $envio): ?>
                    <tr>
                        <td><?php echo $envio['numero_orden']; ?></td>
                        <td><?php echo $envio['codigo']; ?></td>
                        <td><?php echo $envio['fecha']; ?></td>
                        <td><?php echo $envio['monto_final']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif (isset($error_message)): ?>
        <p><?php echo $error_message; ?></p>
    <?php endif; ?>
</body>
</html>
