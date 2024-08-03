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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo_comprobante = $_POST['tipo_comprobante'];
    $id_envio = $_SESSION['id_envio_admin'];

    // Obtener id_pago desde la tabla pagos
    $sql_pago = "SELECT id_pago FROM Pagos WHERE id_envio = ?";
    $stmt_pago = $conn->prepare($sql_pago);
    $stmt_pago->bind_param("i", $id_envio);
    $stmt_pago->execute();
    $result_pago = $stmt_pago->get_result();

    if ($result_pago->num_rows > 0) {
        $row_pago = $result_pago->fetch_assoc();
        $id_pago = $row_pago['id_pago'];

        // Insertar en la tabla comprobantes
        $sql_comprobante = "INSERT INTO Comprobantes (tipo_comprobante, id_pago) VALUES (?, ?)";
        $stmt_comprobante = $conn->prepare($sql_comprobante);
        $stmt_comprobante->bind_param("si", $tipo_comprobante, $id_pago);
        
        if ($stmt_comprobante->execute()) {
            $numero_serie = $stmt_comprobante->insert_id;
            $_SESSION['numero_serie'] = $numero_serie; // Guardar en la sesión
            // Redirigir a la página de descripción del paquete
            header("Location: descripcion_paquete.php");
        } else {
            echo "Error al registrar el comprobante: " . $conn->error;
        }

        $stmt_comprobante->close();
    } else {
        echo "Error: No se encontró el pago correspondiente.";
    }

    $stmt_pago->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Comprobante</title>
</head>
<body>
    <h1>Registro de Comprobante</h1>
    <form method="post">
        <label for="tipo_comprobante">Tipo de Comprobante:</label>
        <select id="tipo_comprobante" name="tipo_comprobante" required>
            <option value="Guia de Remision">Guia de Remision</option>
            <option value="Factura">Factura</option>
        </select><br>
        <button type="submit">Continuar</button>
    </form>
</body>
</html>
