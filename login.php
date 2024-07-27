<?php
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
    // Usuario encontrado
    echo "Login exitoso.";
} else {
    // Usuario no encontrado
    echo "Número de documento no encontrado.";
}

$stmt->close();
$conn->close();
?>
