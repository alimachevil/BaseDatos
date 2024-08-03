<?php
session_start();

if (!isset($_SESSION['id_envio_admin']) || !isset($_SESSION['numero_serie'])) {
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
$numero_serie = $_SESSION['numero_serie'];

// Obtener los paquetes relacionados con el id_envio
$sql_paquetes = "SELECT p.id_paquete, p.tipo_empaque, p.precio_unitario, p.largo, p.ancho, p.alto, p.peso 
                 FROM Paquetes p
                 JOIN PaquetesPorEnvio pe ON p.id_paquete = pe.id_paquete
                 WHERE pe.id_envio = ?";
$stmt_paquetes = $conn->prepare($sql_paquetes);
$stmt_paquetes->bind_param("i", $id_envio);
$stmt_paquetes->execute();
$result_paquetes = $stmt_paquetes->get_result();

$paquetes = [];
while ($row = $result_paquetes->fetch_assoc()) {
    $paquetes[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_paquete = $_POST['id_paquete'];
    $descripcion = $_POST['descripcion'];

    if (!empty($descripcion)) {
        // Insertar en la tabla descripcionporpaquete
        $sql_descripcion = "INSERT INTO DescripcionPorPaquete (numero_serie, descripcion, id_paquete) VALUES (?, ?, ?)";
        $stmt_descripcion = $conn->prepare($sql_descripcion);
        $stmt_descripcion->bind_param("ssi", $numero_serie, $descripcion, $id_paquete);

        if ($stmt_descripcion->execute()) {
            if (isset($_POST['continuar'])) {
                header("Location: descripcion_comprobante.php");
                exit();
            } else {
                header("Location: descripcion_paquete.php");
                exit();
            }
        } else {
            echo "Error al registrar la descripción: " . $conn->error;
        }

        $stmt_descripcion->close();
    } elseif (isset($_POST['continuar'])) {
        header("Location: descripcion_comprobante.php");
        exit();
    }
}

$stmt_paquetes->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descripción de Paquete</title>
</head>
<body>
    <h1>Descripción de Paquete</h1>
    <table border="1">
        <tr>
            <th>ID Paquete</th>
            <th>Tipo de Empaque</th>
            <th>Precio Unitario</th>
            <th>Largo</th>
            <th>Ancho</th>
            <th>Alto</th>
            <th>Peso</th>
        </tr>
        <?php foreach ($paquetes as $paquete): ?>
            <tr>
                <td><?php echo htmlspecialchars($paquete['id_paquete']); ?></td>
                <td><?php echo htmlspecialchars($paquete['tipo_empaque']); ?></td>
                <td><?php echo htmlspecialchars($paquete['precio_unitario']); ?></td>
                <td><?php echo htmlspecialchars($paquete['largo']); ?></td>
                <td><?php echo htmlspecialchars($paquete['ancho']); ?></td>
                <td><?php echo htmlspecialchars($paquete['alto']); ?></td>
                <td><?php echo htmlspecialchars($paquete['peso']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <form method="post">
        <label for="id_paquete">ID Paquete:</label>
        <select id="id_paquete" name="id_paquete" required>
            <?php foreach ($paquetes as $paquete): ?>
                <option value="<?php echo htmlspecialchars($paquete['id_paquete']); ?>">
                    <?php echo htmlspecialchars($paquete['id_paquete']); ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion"></textarea><br>

        <button type="submit" name="continuar">Registrar y Continuar</button>
        <button type="submit" name="añadir">Añadir Registro</button>
    </form>
</body>
</html>
