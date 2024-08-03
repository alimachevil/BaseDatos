<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DBU1";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = $_POST['codigo'];

    // Buscar el envío por el código
    $sql_envio = "SELECT numero_orden FROM Envios WHERE codigo = ?";
    $stmt_envio = $conn->prepare($sql_envio);
    $stmt_envio->bind_param("s", $codigo);
    $stmt_envio->execute();
    $result_envio = $stmt_envio->get_result();
    $row_envio = $result_envio->fetch_assoc();
    
    if ($row_envio) {
        $id_envio = $row_envio['numero_orden'];

        // Guardar el id_envio en la sesión para usarlo en las próximas páginas
        $_SESSION['id_envio_admin'] = $id_envio;

        // Buscar los paquetes asociados al id_envio
        $sql_paquetes = "SELECT Paquetes.* FROM Paquetes
                         INNER JOIN PaquetesPorEnvio ON Paquetes.id_paquete = PaquetesPorEnvio.id_paquete
                         WHERE PaquetesPorEnvio.id_envio = ?";
        $stmt_paquetes = $conn->prepare($sql_paquetes);
        $stmt_paquetes->bind_param("i", $id_envio);
        $stmt_paquetes->execute();
        $result_paquetes = $stmt_paquetes->get_result();
    } else {
        $error = "Código no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisión de Envío</title>
</head>
<body>
    <h1>Revisión de Envío</h1>

    <form action="revision_envio.php" method="post">
        <label for="codigo">Código de Envío:</label>
        <input type="text" id="codigo" name="codigo" required>
        <button type="submit">Buscar</button>
    </form>

    <?php if (isset($result_paquetes) && $result_paquetes->num_rows > 0): ?>
        <h2>Paquetes Asociados</h2>
        <table>
            <tr>
                <th>ID Paquete</th>
                <th>Tipo de Empaque</th>
                <th>Largo</th>
                <th>Ancho</th>
                <th>Alto</th>
                <th>Peso</th>
                <th>Precio Unitario</th>
            </tr>
            <?php while ($row_paquete = $result_paquetes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row_paquete['id_paquete']; ?></td>
                    <td><?php echo $row_paquete['tipo_empaque']; ?></td>
                    <td><?php echo $row_paquete['largo']; ?></td>
                    <td><?php echo $row_paquete['ancho']; ?></td>
                    <td><?php echo $row_paquete['alto']; ?></td>
                    <td><?php echo $row_paquete['peso']; ?></td>
                    <td><?php echo $row_paquete['precio_unitario']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <form action="continuar_revision.php" method="post" style="display: inline;">
            <button type="submit">Continuar</button>
        </form>
        <form action="modificar_envio.php" method="post" style="display: inline;">
            <button type="submit">Modificar Envío</button>
        </form>
    <?php elseif (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <?php
    if (isset($stmt_envio)) $stmt_envio->close();
    if (isset($stmt_paquetes)) $stmt_paquetes->close();
    $conn->close();
    ?>
</body>
</html>
