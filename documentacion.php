<?php
session_start();

if (!isset($_SESSION['numero_documento'])) {
    header("Location: login.html");
    exit();
}

$numero_documento = $_SESSION['numero_documento'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DBU1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar_comprobante'])) {
    $numero_comprobante = $_POST['numero_comprobante'];
    $ruc_emisor = $_POST['ruc_emisor'];
    $tipo_comprobante = $_POST['tipo_comprobante'];

    $sql_documentacion = "INSERT INTO documentacion (Numero_comprobante, Ruc_emisor, Tipo_comprobante, Id_Usuario) 
                          VALUES (?, ?, ?, ?)";
    $stmt_documentacion = $conn->prepare($sql_documentacion);
    $stmt_documentacion->bind_param("ssss", $numero_comprobante, $ruc_emisor, $tipo_comprobante, $numero_documento);
    $stmt_documentacion->execute();
    $stmt_documentacion->close();

    // Redirigir a la misma página para resetear el formulario
    header("Location: documentacion.php");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Documentación</title>
</head>
<body>
    <h1>Registrar Documentación</h1>

    <form action="documentacion.php" method="post">
        <label for="numero_comprobante">Número de Comprobante:</label>
        <input type="text" id="numero_comprobante" name="numero_comprobante" required><br>

        <label for="ruc_emisor">RUC Emisor:</label>
        <input type="text" id="ruc_emisor" name="ruc_emisor" required><br>

        <label for="tipo_comprobante">Tipo de Comprobante:</label>
        <select id="tipo_comprobante" name="tipo_comprobante" required>
            <option value="Factura Electrónica">Factura Electrónica</option>
            <option value="Boleta de Venta Electrónica">Boleta de Venta Electrónica</option>
            <option value="Nota de Crédito Electrónica">Nota de Crédito Electrónica</option>
            <option value="Nota de Débito Electrónica">Nota de Débito Electrónica</option>
            <option value="Recibo por Honorarios Electrónico">Recibo por Honorarios Electrónico</option>
            <option value="Comprobante de Retención Electrónico">Comprobante de Retención Electrónico</option>
            <option value="Otros">Otros</option>
        </select><br>

        <button type="submit" name="registrar_comprobante">Registrar Comprobante</button>
        <button type="button" onclick="window.location.href='pagina_pago.php'">Pasar a Pago</button>
    </form>
</body>
</html>

<?php
$conn->close();
?>
