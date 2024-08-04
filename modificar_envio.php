<?php
session_start();

if (!isset($_SESSION['id_envio_admin'])) {
    header("Location: revision_envio.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DBU1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id_envio = $_SESSION['id_envio_admin'];

// Obtener los paquetes relacionados al id_envio
$sql_paquetes = "SELECT p.id_paquete, p.precio_unitario, p.largo, p.ancho, p.alto, p.peso, p.tipo_empaque, p.numero_documento 
                 FROM paquetes p
                 JOIN paquetesporenvio pe ON p.id_paquete = pe.id_paquete
                 WHERE pe.id_envio = ?";
$stmt_paquetes = $conn->prepare($sql_paquetes);
$stmt_paquetes->bind_param("i", $id_envio);
$stmt_paquetes->execute();
$result_paquetes = $stmt_paquetes->get_result();

$paquetes = $result_paquetes->fetch_all(MYSQLI_ASSOC);

// Obtener la tarifa del pago relacionado con el id_envio
$sql_tarifa = "SELECT tarifa FROM pagos WHERE id_envio = ?";
$stmt_tarifa = $conn->prepare($sql_tarifa);
$stmt_tarifa->bind_param("i", $id_envio);
$stmt_tarifa->execute();
$result_tarifa = $stmt_tarifa->get_result();
$tarifa = 0;
if ($result_tarifa->num_rows > 0) {
    $tarifa = $result_tarifa->fetch_assoc()['tarifa'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['eliminar_paquete'])) {
        $id_paquete = $_POST['id_paquete'];
        
        // Obtener el precio unitario del paquete a eliminar
        $sql_precio = "SELECT precio_unitario FROM paquetes WHERE id_paquete = ?";
        $stmt_precio = $conn->prepare($sql_precio);
        $stmt_precio->bind_param("i", $id_paquete);
        $stmt_precio->execute();
        $result_precio = $stmt_precio->get_result();
        if ($result_precio->num_rows > 0) {
            $precio_unitario = $result_precio->fetch_assoc()['precio_unitario'];
            $tarifa -= $precio_unitario;

            // Eliminar de la tabla paquetesporenvio
            $sql_delete_pe = "DELETE FROM paquetesporenvio WHERE id_paquete = ?";
            $stmt_delete_pe = $conn->prepare($sql_delete_pe);
            $stmt_delete_pe->bind_param("i", $id_paquete);
            $stmt_delete_pe->execute();

            // Eliminar de la tabla paquetes
            $sql_delete_p = "DELETE FROM paquetes WHERE id_paquete = ?";
            $stmt_delete_p = $conn->prepare($sql_delete_p);
            $stmt_delete_p->bind_param("i", $id_paquete);
            $stmt_delete_p->execute();

            // Actualizar la tarifa en la tabla pagos
            $sql_update_tarifa = "UPDATE pagos SET tarifa = ? WHERE id_envio = ?";
            $stmt_update_tarifa = $conn->prepare($sql_update_tarifa);
            $stmt_update_tarifa->bind_param("di", $tarifa, $id_envio);
            $stmt_update_tarifa->execute();
        }

        // Recargar la página
        header("Location: modificar_envio.php");
        exit();
    } elseif (isset($_POST['continuar'])) {
        // Actualizar la tarifa en la tabla pagos
        $sql_update_tarifa = "UPDATE pagos SET tarifa = ? WHERE id_envio = ?";
        $stmt_update_tarifa = $conn->prepare($sql_update_tarifa);
        $stmt_update_tarifa->bind_param("di", $tarifa, $id_envio);
        $stmt_update_tarifa->execute();

        // Redirigir a continuar_revision.php
        header("Location: continuar_revision.php");
        exit();
    }
}

$stmt_paquetes->close();
$stmt_tarifa->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Envío</title>
</head>
<body>
    <h1>Modificar Envío</h1>

    <table border="1">
        <tr>
            <th>ID Paquete</th>
            <th>Precio Unitario</th>
            <th>Largo</th>
            <th>Ancho</th>
            <th>Alto</th>
            <th>Peso</th>
            <th>Tipo de Empaque</th>
            <th>Número de Documento</th>
        </tr>
        <?php foreach ($paquetes as $paquete): ?>
            <tr>
                <td><?php echo $paquete['id_paquete']; ?></td>
                <td><?php echo $paquete['precio_unitario']; ?></td>
                <td><?php echo $paquete['largo']; ?></td>
                <td><?php echo $paquete['ancho']; ?></td>
                <td><?php echo $paquete['alto']; ?></td>
                <td><?php echo $paquete['peso']; ?></td>
                <td><?php echo $paquete['tipo_empaque']; ?></td>
                <td><?php echo $paquete['numero_documento']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Modificar Paquete</h2>
    <form method="post">
        <label for="tarifa">Monto Total:</label>
        <input type="text" id="tarifa" name="tarifa" value="<?php echo $tarifa; ?>" readonly><br>

        <label for="id_paquete">Seleccionar Paquete a Eliminar:</label>
        <select id="id_paquete" name="id_paquete" required>
            <?php foreach ($paquetes as $paquete): ?>
                <option value="<?php echo $paquete['id_paquete']; ?>"><?php echo $paquete['id_paquete']; ?></option>
            <?php endforeach; ?>
        </select><br>

        <button type="submit" name="eliminar_paquete">Eliminar Paquete</button>
        <button type="submit" name="continuar">Continuar</button>
    </form>
</body>
</html>
