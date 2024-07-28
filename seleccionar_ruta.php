<?php
session_start(); // Iniciar la sesión

if (!isset($_SESSION['numero_documento'])) {
    // Si el número de documento no está en la sesión, redirigir al login
    header("Location: login.html");
    exit();
}

$numero_documento = $_SESSION['numero_documento'];

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

$agencia_origen = $_POST['agencia_origen'];
$agencia_destino = $_POST['agencia_destino'];
$fecha = date("Y-m-d");

// Insertar nuevo envío
$sql_envio = "INSERT INTO Envios (numero_documento, fecha) VALUES (?, ?)";
$stmt_envio = $conn->prepare($sql_envio);
$stmt_envio->bind_param("ss", $numero_documento, $fecha);
$stmt_envio->execute();
$id_envio = $stmt_envio->insert_id; // Obtener el ID del nuevo envío

// Insertar en EnvioPorAgencia
$sql_ruta = "INSERT INTO EnvioPorAgencia (id_envio, id_agencia) VALUES (?, ?), (?, ?)";
$stmt_ruta = $conn->prepare($sql_ruta);
$stmt_ruta->bind_param("iiii", $id_envio, $agencia_origen, $id_envio, $agencia_destino);
$stmt_ruta->execute();

$stmt_envio->close();
$stmt_ruta->close();
$conn->close();

// Redirigir a la siguiente pantalla o mostrar mensaje de éxito
header("Location: siguiente_pantalla.php");
?>
