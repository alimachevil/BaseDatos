<?php
session_start();

if (!isset($_SESSION['numero_documento']) || !isset($_SESSION['id_envio'])) {
    header("Location: login.html");
    exit();
}

$numero_documento = $_SESSION['numero_documento'];
$id_envio = $_SESSION['id_envio'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DBU1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Calcular la tarifa total
$sql_tarifa = "SELECT SUM(Paquetes.precio_unitario) AS tarifa_total
               FROM Paquetes
               INNER JOIN PaquetesPorEnvio ON Paquetes.id_paquete = PaquetesPorEnvio.id_paquete
               WHERE PaquetesPorEnvio.id_envio = ?";
$stmt_tarifa = $conn->prepare($sql_tarifa);
$stmt_tarifa->bind_param("i", $id_envio);
$stmt_tarifa->execute();
$result_tarifa = $stmt_tarifa->get_result();
$row_tarifa = $result_tarifa->fetch_assoc();
$tarifa_total = $row_tarifa['tarifa_total'];
$stmt_tarifa->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $garantia = isset($_POST['garantia']) ? 1 : 0;
    $tarifa_final = $garantia ? $tarifa_total + 15 : $tarifa_total;
    $clave_seguridad = $_POST['clave_seguridad'];
    $codigo = rand(10000000, 99999999);

    // Insertar en la tabla pagos
    $sql_pago = "INSERT INTO Pagos (garantia, tarifa, id_cliente, id_envio) VALUES (?, ?, ?, ?)";
    $stmt_pago = $conn->prepare($sql_pago);
    $stmt_pago->bind_param("idis", $garantia, $tarifa_final, $numero_documento, $id_envio);
    $stmt_pago->execute();
    $stmt_pago->close();

    // Actualizar el registro en la tabla envios
    $sql_actualizar_envio = "UPDATE Envios SET clave_seguridad = ?, codigo = ? WHERE numero_orden = ?";
    $stmt_actualizar_envio = $conn->prepare($sql_actualizar_envio);
    $stmt_actualizar_envio->bind_param("ssi", $clave_seguridad, $codigo, $id_envio);
    $stmt_actualizar_envio->execute();
    $stmt_actualizar_envio->close();

    $conn->close();

    $_SESSION['codigo'] = $codigo;
    header("Location: confirmacion_envio.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago</title>
</head>
<body>
    <h1>Pago</h1>

    <form action="pagina_pago.php" method="post">
        <label for="tarifa">Tarifa:</label>
        <input type="text" id="tarifa" name="tarifa" value="<?php echo $tarifa_total; ?>" readonly><br>

        <label for="garantia">Garantía:</label>
        <input type="checkbox" id="garantia" name="garantia"><br>

        <label for="clave_seguridad">Clave de Seguridad (4 dígitos):</label>
        <input type="password" id="clave_seguridad" name="clave_seguridad" maxlength="4" required><br>

        <button type="submit">Registrar Pago</button>
    </form>
</body>
</html>
