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

$sql = "SELECT * FROM Agencias";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Agencias</title>
</head>
<body>
    <h1>Agencias</h1>
    <table border="1">
        <tr>
            <th>ID Agencia</th>
            <th>Departamento</th>
            <th>Provincia</th>
            <th>Distrito</th>
            <th>Referencia</th>
            <th>Teléfono</th>
            <th>Horario</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id_agencia'] ?></td>
                <td><?= $row['departamento'] ?></td>
                <td><?= $row['provincia'] ?></td>
                <td><?= $row['distrito'] ?></td>
                <td><?= $row['referencia'] ?></td>
                <td><?= $row['telefono'] ?></td>
                <td><?= $row['horario'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
