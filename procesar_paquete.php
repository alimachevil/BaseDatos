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

// Verificar si ya existe un ID de envío en la sesión
if (!isset($_SESSION['id_envio'])) {
    // Registrar el envío si no existe
    $sql_envio = "INSERT INTO Envios (numero_documento, fecha) VALUES (?, NOW())";
    $stmt_envio = $conn->prepare($sql_envio);
    $stmt_envio->bind_param("s", $numero_documento);
    $stmt_envio->execute();
    $_SESSION['id_envio'] = $stmt_envio->insert_id;
    $stmt_envio->close();

    // Relacionar el envío con las agencias
    $agencia_origen = $_POST['agencia_origen'];
    $agencia_destino = $_POST['agencia_destino'];
    $sql_ruta = "INSERT INTO EnvioPorAgencia (id_envio, id_agencia) VALUES (?, ?), (?, ?)";
    $stmt_ruta = $conn->prepare($sql_ruta);
    $stmt_ruta->bind_param("iiii", $_SESSION['id_envio'], $agencia_origen, $_SESSION['id_envio'], $agencia_destino);
    $stmt_ruta->execute();
    $stmt_ruta->close();
}

$id_envio = $_SESSION['id_envio'];

// Datos del paquete
$tipo_empaque = $_POST['tipo_empaque'];
$largo = isset($_POST['largo']) ? $_POST['largo'] : null;
$ancho = isset($_POST['ancho']) ? $_POST['ancho'] : null;
$alto = isset($_POST['alto']) ? $_POST['alto'] : null;
$peso = isset($_POST['peso']) ? $_POST['peso'] : null;
$precio_unitario = $_POST['precio_unitario'];

// Registrar el paquete
$sql_paquete = "INSERT INTO Paquetes (precio_unitario, largo, ancho, alto, peso, tipo_empaque, numero_documento) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt_paquete = $conn->prepare($sql_paquete);
$stmt_paquete->bind_param("sssssss", $precio_unitario, $largo, $ancho, $alto, $peso, $tipo_empaque, $numero_documento);
$stmt_paquete->execute();
$id_paquete = $stmt_paquete->insert_id;
$stmt_paquete->close();

// Relacionar el paquete con el envío
$sql_paquete_por_envio = "INSERT INTO PaquetesPorEnvio (id_paquete, id_envio) VALUES (?, ?)";
$stmt_paquete_por_envio = $conn->prepare($sql_paquete_por_envio);
$stmt_paquete_por_envio->bind_param("ii", $id_paquete, $id_envio);
$stmt_paquete_por_envio->execute();
$stmt_paquete_por_envio->close();

$conn->close();

// Redirigir a la misma página para resetear el formulario
header("Location: registro_paquete.php");
?>
