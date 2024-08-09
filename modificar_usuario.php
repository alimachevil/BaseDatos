<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DBU1";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del usuario si se ha proporcionado un número de documento
if (isset($_GET['numero_documento'])) {
    $numero_documento = $_GET['numero_documento'];
    $sql = "SELECT * FROM Usuarios WHERE numero_documento = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $numero_documento);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    $stmt->close();
}

// Actualizar usuario si se han enviado datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    $sql = "UPDATE Usuarios SET tipo_documento = ?, correo_electronico = ?, nombre = ?, apellido_paterno = ?, apellido_materno = ?, celular = ?, departamento = ?, provincia = ?, distrito = ?, direccion = ? WHERE numero_documento = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssss", $tipo_documento, $correo_electronico, $nombre, $apellido_paterno, $apellido_materno, $celular, $departamento, $provincia, $distrito, $direccion, $numero_documento);
    $stmt->execute();
    $stmt->close();
    header("Location: administrar_usuarios.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario</title>
</head>
<body>
    <h1>Modificar Usuario</h1>
    <form method="post">
        <input type="hidden" name="numero_documento" value="<?= $usuario['numero_documento'] ?>">
        <label for="tipo_documento">Tipo de Documento:</label>
        <input type="text" id="tipo_documento" name="tipo_documento" value="<?= $usuario['tipo_documento'] ?>" required><br>

        <label for="correo_electronico">Correo Electrónico:</label>
        <input type="email" id="correo_electronico" name="correo_electronico" value="<?= $usuario['correo_electronico'] ?>" required><br>

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?= $usuario['nombre'] ?>" required><br>

        <label for="apellido_paterno">Apellido Paterno:</label>
        <input type="text" id="apellido_paterno" name="apellido_paterno" value="<?= $usuario['apellido_paterno'] ?>" required><br>

        <label for="apellido_materno">Apellido Materno:</label>
        <input type="text" id="apellido_materno" name="apellido_materno" value="<?= $usuario['apellido_materno'] ?>" required><br>

        <label for="celular">Celular:</label>
        <input type="text" id="celular" name="celular" value="<?= $usuario['celular'] ?>" required><br>

        <label for="departamento">Departamento:</label>
        <input type="text" id="departamento" name="departamento" value="<?= $usuario['departamento'] ?>" required><br>

        <label for="provincia">Provincia:</label>
        <input type="text" id="provincia" name="provincia" value="<?= $usuario['provincia'] ?>" required><br>

        <label for="distrito">Distrito:</label>
        <input type="text" id="distrito" name="distrito" value="<?= $usuario['distrito'] ?>" required><br>

        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion" value="<?= $usuario['direccion'] ?>" required><br>

        <button type="submit">Guardar Cambios</button>
    </form>
</body>
</html>

<?php
$conn->close();
?>
