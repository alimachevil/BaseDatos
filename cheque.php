<?php
session_start();

if (!isset($_SESSION['paquetes_eliminados']) || empty($_SESSION['paquetes_eliminados']) || !isset($_SESSION['razon_reembolso'])) {
    header("Location: modificar_envio.php");
    exit();
}

$paquetes_eliminados = $_SESSION['paquetes_eliminados'];
$total_reembolso = array_sum(array_column($paquetes_eliminados, 'precio_unitario'));
$razon_reembolso = $_SESSION['razon_reembolso'];

function generar_codigo() {
    return sprintf('%04d-%04d-%04d', rand(0, 9999), rand(0, 9999), rand(0, 9999));
}

$codigo = generar_codigo();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reembolso de Pago</title>
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
    <script>
        function imprimir() {
            window.print();
        }
    </script>
</head>
<body>
    <h1>Reembolso de Pago</h1>
    <p>Reembolso para los paquetes:</p>

    <table border="1">
        <tr>
            <th>ID Paquete</th>
            <th>Precio Unitario</th>
            <th>Largo</th>
            <th>Ancho</th>
            <th>Alto</th>
            <th>Peso</th>
            <th>Tipo de Empaque</th>
        </tr>
        <?php foreach ($paquetes_eliminados as $paquete): ?>
            <tr>
                <td><?php echo $paquete['id_paquete']; ?></td>
                <td><?php echo $paquete['precio_unitario']; ?></td>
                <td><?php echo $paquete['largo']; ?></td>
                <td><?php echo $paquete['ancho']; ?></td>
                <td><?php echo $paquete['alto']; ?></td>
                <td><?php echo $paquete['peso']; ?></td>
                <td><?php echo $paquete['tipo_empaque']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p>Total: <?php echo $total_reembolso; ?></p>
    <p>Razón: <?php echo $razon_reembolso; ?></p>

    <h2>Código de Reembolso</h2>
    <p><?php echo $codigo; ?></p>

    <div class="no-print">
        <button onclick="imprimir()">Imprimir PDF</button>
        <form method="post" action="continuar_revision.php">
            <button type="submit">Continuar</button>
        </form>
    </div>
</body>
</html>
