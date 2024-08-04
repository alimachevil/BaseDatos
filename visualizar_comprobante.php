<?php
session_start();

if (!isset($_SESSION['id_envio_admin']) || !isset($_SESSION['numero_serie'])) {
    header("Location: revision_envio.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DBU1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id_envio = $_SESSION['id_envio_admin'];
$numero_serie = $_SESSION['numero_serie'];

$sql_eliminar = "DELETE FROM paquetes 
                 WHERE id_paquete NOT IN (SELECT id_paquete FROM paquetesporenvio)";
if ($conn->query($sql_eliminar) === FALSE) {
    echo "Error al eliminar registros: " . $conn->error;
}

// Obtener datos de la empresa y la agencia
$sql_agencia = "SELECT a.referencia, a.provincia, a.distrito 
                FROM envioporagencia e
                JOIN agencias a ON e.id_agencia = a.id_agencia
                WHERE e.id_envio = ? 
                ORDER BY e.id_agencia DESC LIMIT 1";
$stmt_agencia = $conn->prepare($sql_agencia);
$stmt_agencia->bind_param("i", $id_envio);
$stmt_agencia->execute();
$result_agencia = $stmt_agencia->get_result();
$agencia = $result_agencia->fetch_assoc();

// Obtener tipo de comprobante
$sql_comprobante = "SELECT tipo_comprobante FROM comprobantes WHERE numero_serie = ?";
$stmt_comprobante = $conn->prepare($sql_comprobante);
$stmt_comprobante->bind_param("i", $numero_serie);
$stmt_comprobante->execute();
$result_comprobante = $stmt_comprobante->get_result();
$comprobante = $result_comprobante->fetch_assoc();

// Obtener datos del cliente
$sql_cliente = "SELECT u.nombre, u.apellido_paterno, u.apellido_materno, u.tipo_documento, u.numero_documento 
                FROM envios e
                JOIN usuarios u ON e.numero_documento = u.numero_documento
                WHERE e.numero_orden = ?";
$stmt_cliente = $conn->prepare($sql_cliente);
$stmt_cliente->bind_param("i", $id_envio);
$stmt_cliente->execute();
$result_cliente = $stmt_cliente->get_result();
$cliente = $result_cliente->fetch_assoc();

// Obtener fecha de emisión
$sql_fecha = "SELECT fecha_actualizacion FROM estadoenvio WHERE id_envio = ? ORDER BY fecha_actualizacion DESC LIMIT 1";
$stmt_fecha = $conn->prepare($sql_fecha);
$stmt_fecha->bind_param("i", $id_envio);
$stmt_fecha->execute();
$result_fecha = $stmt_fecha->get_result();
$fecha = $result_fecha->fetch_assoc()['fecha_actualizacion'];

// Obtener paquetes relacionados al id_envio
$sql_paquetes = "SELECT p.id_paquete, p.precio_unitario, p.largo, p.ancho, p.alto, p.peso, p.tipo_empaque, d.descripcion 
                 FROM paquetes p
                 JOIN paquetesporenvio pe ON p.id_paquete = pe.id_paquete
                 JOIN descripcionporpaquete d ON p.id_paquete = d.id_paquete
                 WHERE pe.id_envio = ?";
$stmt_paquetes = $conn->prepare($sql_paquetes);
$stmt_paquetes->bind_param("i", $id_envio);
$stmt_paquetes->execute();
$result_paquetes = $stmt_paquetes->get_result();
$paquetes = $result_paquetes->fetch_all(MYSQLI_ASSOC);

// Obtener detalles del comprobante
$sql_detalle = "SELECT subtotal, igv, total FROM detallecomprobante WHERE numero_serie = ?";
$stmt_detalle = $conn->prepare($sql_detalle);
$stmt_detalle->bind_param("i", $numero_serie);
$stmt_detalle->execute();
$result_detalle = $stmt_detalle->get_result();
$detalle = $result_detalle->fetch_assoc();

$stmt_agencia->close();
$stmt_comprobante->close();
$stmt_cliente->close();
$stmt_fecha->close();
$stmt_paquetes->close();
$stmt_detalle->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Comprobante</title>
    <style>
        .top-left {
            position: absolute;
            top: 10px;
            left: 10px;
        }
        .top-right {
            position: absolute;
            top: 10px;
            right: 10px;
            border: 1px solid black;
            padding: 10px;
        }
        .center {
            margin: 150px auto;
            text-align: left;
        }
        .bottom {
            margin-top: 20px;
            text-align: right;
        }
        .no-print {
            display: block;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="top-left">
        <p>NXA Courier</p>
        <p><?php echo $agencia['referencia']; ?></p>
        <p><?php echo $agencia['provincia'] . ' - ' . $agencia['distrito']; ?></p>
    </div>

    <div class="top-right">
        <p>RUC: 20600123456</p>
        <p><?php echo $comprobante['tipo_comprobante']; ?></p>
        <p>001-000<?php echo str_pad($numero_serie, 3, '0', STR_PAD_LEFT); ?></p>
    </div>

    <div class="center">
        <p>Señor: <?php echo $cliente['nombre'] . ' ' . $cliente['apellido_paterno'] . ' ' . $cliente['apellido_materno']; ?></p>
        <p><?php echo $cliente['tipo_documento'] . ': ' . $cliente['numero_documento']; ?></p>
        <p>Fecha de emisión: <?php echo $fecha; ?></p>

        <table border="1">
            <tr>
                <th>Cantidad</th>
                <th>Largo</th>
                <th>Ancho</th>
                <th>Alto</th>
                <th>Peso</th>
                <th>Tipo de Empaque</th>
                <th>Descripción</th>
                <th>Precio Unitario</th>
            </tr>
            <?php foreach ($paquetes as $paquete): ?>
                <tr>
                    <td>1</td>
                    <td><?php echo $paquete['largo']; ?></td>
                    <td><?php echo $paquete['ancho']; ?></td>
                    <td><?php echo $paquete['alto']; ?></td>
                    <td><?php echo $paquete['peso']; ?></td>
                    <td><?php echo $paquete['tipo_empaque']; ?></td>
                    <td><?php echo $paquete['descripcion']; ?></td>
                    <td><?php echo $paquete['precio_unitario']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="bottom">
            <p>Subtotal: <?php echo $detalle['subtotal']; ?></p>
            <p>IGV: <?php echo $detalle['igv']; ?></p>
            <p>Total: <?php echo $detalle['total']; ?></p>
        </div>
    </div>

    <div class="no-print">
        <button onclick="window.location.href='menu_administrador.php'">Menu Administrador</button>
        <button onclick="window.print()">Imprimir en PDF</button>
    </div>
</body>
</html>
