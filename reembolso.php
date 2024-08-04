<?php
session_start();

if (!isset($_SESSION['paquetes_eliminados']) || empty($_SESSION['paquetes_eliminados'])) {
    header("Location: modificar_envio.php");
    exit();
}

$paquetes_eliminados = $_SESSION['paquetes_eliminados'];
$total_reembolso = array_sum(array_column($paquetes_eliminados, 'precio_unitario'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $razon = $_POST['razon'];
    $_SESSION['razon_reembolso'] = $razon;
    header("Location: cheque.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reembolso</title>
</head>
<body>
    <h1>Reembolso de Pago por Paquetes</h1>
    <p>Este reembolso elimina los paquetes:</p>

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

    <form method="post">
        <label for="razon">Razón:</label><br>
        <textarea id="razon" name="razon" rows="4" cols="50" required></textarea><br>
        <button type="submit">Continuar</button>
    </form>
</body>
</html>
