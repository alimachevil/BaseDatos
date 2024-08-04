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

// Manejar la eliminación del paquete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_paquete'])) {
    $id_paquete = $_POST['id_paquete'];

    // Paso 1: Eliminar de la tabla 'paquetesporenvio'
    $sql_eliminar_paquetesporenvio = "DELETE FROM paquetesporenvio WHERE id_paquete = ?";
    $stmt_eliminar_paquetesporenvio = $conn->prepare($sql_eliminar_paquetesporenvio);
    $stmt_eliminar_paquetesporenvio->bind_param("i", $id_paquete);
    $stmt_eliminar_paquetesporenvio->execute();
    $stmt_eliminar_paquetesporenvio->close();

    // Paso 2: Eliminar de la tabla 'paquetes'
    $sql_eliminar_paquetes = "DELETE FROM paquetes WHERE id_paquete = ?";
    $stmt_eliminar_paquetes = $conn->prepare($sql_eliminar_paquetes);
    $stmt_eliminar_paquetes->bind_param("i", $id_paquete);
    $stmt_eliminar_paquetes->execute();
    $stmt_eliminar_paquetes->close();
}

// Obtener todos los paquetes relacionados al id_envio
$sql_paquetes = "SELECT p.id_paquete, p.precio_unitario, p.largo, p.ancho, p.alto, p.peso, p.tipo_empaque, p.numero_documento 
                 FROM paquetes p
                 INNER JOIN paquetesporenvio pe ON p.id_paquete = pe.id_paquete
                 WHERE pe.id_envio = ?";
$stmt_paquetes = $conn->prepare($sql_paquetes);
$stmt_paquetes->bind_param("i", $id_envio);
$stmt_paquetes->execute();
$result_paquetes = $stmt_paquetes->get_result();
$paquetes = $result_paquetes->fetch_all(MYSQLI_ASSOC);
$stmt_paquetes->close();

// Calcular el monto total
$monto_total = 0;
foreach ($paquetes as $paquete) {
    $monto_total += $paquete['precio_unitario'];
}
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

    <form method="post" action="">
        <label for="monto_total">Monto Total:</label>
        <input type="text" id="monto_total" name="monto_total" value="<?php echo $monto_total; ?>" readonly><br><br>

        <label for="id_paquete">Seleccionar Paquete para Eliminar:</label>
        <select id="id_paquete" name="id_paquete" required>
            <?php foreach ($paquetes as $paquete): ?>
            <option value="<?php echo $paquete['id_paquete']; ?>"><?php echo $paquete['id_paquete']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit" name="eliminar_paquete">Eliminar Paquete</button>
    </form>

    <form method="post" action="continuar_revision.php">
        <button type="submit" name="continuar_revision">Continuar</button>
    </form>

    <?php
    // Manejar la actualización de la tarifa al continuar
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['continuar_revision'])) {
        $nuevo_monto_total = $monto_total;

        // Actualizar la tarifa en la tabla pagos
        $sql_actualizar_tarifa = "UPDATE pagos SET tarifa = ? WHERE id_envio = ?";
        $stmt_actualizar_tarifa = $conn->prepare($sql_actualizar_tarifa);
        $stmt_actualizar_tarifa->bind_param("di", $nuevo_monto_total, $id_envio);
        $stmt_actualizar_tarifa->execute();
        $stmt_actualizar_tarifa->close();

        // Redirigir a continuar_revision.php
        header("Location: continuar_revision.php");
        exit();
    }

    $conn->close();
    ?>
</body>
</html>
