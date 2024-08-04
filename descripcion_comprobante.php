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

// Obtener la tarifa del pago relacionado con el id_envio
$sql_pago = "SELECT tarifa FROM pagos WHERE id_envio = ?";
$stmt_pago = $conn->prepare($sql_pago);
$stmt_pago->bind_param("i", $id_envio);
$stmt_pago->execute();
$result_pago = $stmt_pago->get_result();

if ($result_pago->num_rows > 0) {
    $pago = $result_pago->fetch_assoc();
    $total = $pago['tarifa'];
    $igv = $total * 0.18;
    $subtotal = $total - $igv;

    // Insertar en la tabla detallecomprobante
    $sql_detalle = "INSERT INTO detallecomprobante (numero_serie, total, subtotal, igv) VALUES (?, ?, ?, ?)";
    $stmt_detalle = $conn->prepare($sql_detalle);
    $stmt_detalle->bind_param("iddd", $numero_serie, $total, $subtotal, $igv);

    if ($stmt_detalle->execute()) {
        // Actualizar la columna monto_final en la tabla envios
        $sql_update_envio = "UPDATE envios SET monto_final = ? WHERE numero_orden = ?";
        $stmt_update_envio = $conn->prepare($sql_update_envio);
        $stmt_update_envio->bind_param("di", $total, $id_envio);
        if ($stmt_update_envio->execute()) {
            header("Location: visualizar_comprobante.php");
            exit();
        } else {
            echo "Error al actualizar el monto final en la tabla envios: " . $conn->error;
        }
        $stmt_update_envio->close();
    } else {
        echo "Error al registrar el detalle del comprobante: " . $conn->error;
    }

    $stmt_detalle->close();
} else {
    echo "Error: No se encontró el pago relacionado con el envío.";
}

$stmt_pago->close();
$conn->close();
?>
