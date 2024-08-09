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

// Consulta que une la tabla Comprobantes con DetalleComprobante
$sql = "SELECT c.numero_serie, c.tipo_comprobante, c.id_pago, d.total, d.subtotal, d.igv
        FROM comprobantes c
        LEFT JOIN detallecomprobante d ON c.numero_serie = d.numero_serie";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Comprobantes</title>
</head>
<body>
    <h1>Comprobantes</h1>
    <table border="1">
        <tr>
            <th>Número de Serie</th>
            <th>Tipo de Comprobante</th>
            <th>ID Pago</th>
            <th>Total</th>
            <th>Subtotal</th>
            <th>IGV</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['numero_serie'] ?></td>
                <td><?= $row['tipo_comprobante'] ?></td>
                <td><?= $row['id_pago'] ?></td>
                <td><?= $row['total'] ?></td>
                <td><?= $row['subtotal'] ?></td>
                <td><?= $row['igv'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
