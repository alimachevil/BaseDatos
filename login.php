<?php
session_start(); // Iniciar la sesión

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DBU1";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$numero_documento = $_POST['numero_documento'];

$sql = "SELECT * FROM Usuarios WHERE numero_documento = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $numero_documento);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Usuario encontrado, guardar el número de documento en la sesión
    $_SESSION['numero_documento'] = $numero_documento;
    // Redirigir a la pantalla de selección de ruta
    header("Location: seleccionar_rutaa.php");
} else {
    // Usuario no encontrado
    echo "Número de documento no encontrado.";
}

$stmt->close();
$conn->close();
?>
