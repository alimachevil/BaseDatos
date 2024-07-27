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
$tipo_documento = $_POST['tipo_documento'];
$correo_electronico = $_POST['correo_electronico'];
$nombre = $_POST['nombre'];
$apellido_paterno = $_POST['apellido_paterno'];
$apellido_materno = $_POST['apellido_materno'];
$celular = $_POST['celular'];
$departamento = $_POST['departamento'];
$provincia = $_POST['provincia'];
$distrito = $_POST['distrito'];
$direccion = $_POST['direccion'];

$sql = "INSERT INTO Usuarios (numero_documento, tipo_documento, correo_electronico, nombre, apellido_paterno, apellido_materno, celular, departamento, provincia, distrito, direccion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssss", $numero_documento, $tipo_documento, $correo_electronico, $nombre, $apellido_paterno, $apellido_materno, $celular, $departamento, $provincia, $distrito, $direccion);

if ($stmt->execute()) {
    echo "Registro exitoso.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
