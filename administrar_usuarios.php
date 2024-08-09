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

// Eliminar usuario si se recibe un número de documento para eliminar
if (isset($_GET['eliminar'])) {
    $numero_documento = $_GET['eliminar'];
    $sql = "DELETE FROM Usuarios WHERE numero_documento = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $numero_documento);
    $stmt->execute();
    $stmt->close();
    header("Location: administrar_usuarios.php");
    exit();
}

// Obtener todos los registros de la tabla Usuarios
$sql = "SELECT * FROM Usuarios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios</title>
</head>
<body>
    <h1>Administrar Usuarios</h1>
    <table border="1">
        <tr>
            <th>Número de Documento</th>
            <th>Tipo de Documento</th>
            <th>Correo Electrónico</th>
            <th>Nombre</th>
            <th>Apellido Paterno</th>
            <th>Apellido Materno</th>
            <th>Celular</th>
            <th>Departamento</th>
            <th>Provincia</th>
            <th>Distrito</th>
            <th>Dirección</th>
            <th>Acciones</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['numero_documento'] ?></td>
                <td><?= $row['tipo_documento'] ?></td>
                <td><?= $row['correo_electronico'] ?></td>
                <td><?= $row['nombre'] ?></td>
                <td><?= $row['apellido_paterno'] ?></td>
                <td><?= $row['apellido_materno'] ?></td>
                <td><?= $row['celular'] ?></td>
                <td><?= $row['departamento'] ?></td>
                <td><?= $row['provincia'] ?></td>
                <td><?= $row['distrito'] ?></td>
                <td><?= $row['direccion'] ?></td>
                <td>
                    <a href="modificar_usuario.php?numero_documento=<?= $row['numero_documento'] ?>">Modificar</a> | 
                    <a href="administrar_usuarios.php?eliminar=<?= $row['numero_documento'] ?>" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
