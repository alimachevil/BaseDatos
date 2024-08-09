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

$sql = "SELECT * FROM Envios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Envios</title>
</head>
<body>
    <h1>Envios</h1>
    <table border="1">
        <tr>
            <th>ID Envio</th>
            <th>Número Documento</th>
            <th>Fecha</th>
            <th>Código</th>
            <th>Clave de Seguridad</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['numero_orden'] ?></td>
                <td><?= $row['numero_documento'] ?></td>
                <td><?= $row['fecha'] ?></td>
                <td><?= $row['codigo'] ?></td>
                <td><?= $row['clave_seguridad'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
