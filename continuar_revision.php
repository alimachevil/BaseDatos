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
$estado = "En Agencia de Origen";
$fecha_actualizacion = date("Y-m-d");

// Insertar en la tabla estadoenvio
$sql_estado = "INSERT INTO estadoenvio (estado, fecha_actualizacion, id_envio) VALUES (?, ?, ?)";
$stmt_estado = $conn->prepare($sql_estado);
$stmt_estado->bind_param("ssi", $estado, $fecha_actualizacion, $id_envio);

if ($stmt_estado->execute()) {
    // Redirigir a la página de registro del comprobante
    header("Location: registro_comprobante.php");
} else {
    echo "Error al actualizar el estado del envío: " . $conn->error;
}

$stmt_estado->close();
$conn->close();
?>
