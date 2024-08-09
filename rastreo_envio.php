<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Obtener datos del formulario
    $codigo_envio = $_POST['codigo_envio'];
    $clave_seguridad = $_POST['clave_seguridad'];

    // Buscar el envío en la tabla Envios
    $sql = "SELECT numero_orden FROM Envios WHERE codigo = ? AND clave_seguridad = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $codigo_envio, $clave_seguridad);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Envío encontrado
        $envio = $result->fetch_assoc();
        $id_envio = $envio['numero_orden'];

        // Obtener estado del envío
        $sql_estado = "SELECT estado, fecha_actualizacion FROM EstadoEnvio WHERE id_envio = ?";
        $stmt_estado = $conn->prepare($sql_estado);
        $stmt_estado->bind_param("i", $id_envio);
        $stmt_estado->execute();
        $estado_result = $stmt_estado->get_result();

        $estado_envio = $estado_result->fetch_assoc();

        // Obtener detalles de los paquetes
        $sql_paquetes = "SELECT P.largo, P.ancho, P.alto, P.peso, P.tipo_empaque, DP.descripcion, P.precio_unitario 
                         FROM PaquetesPorEnvio PE
                         JOIN Paquetes P ON PE.id_paquete = P.id_paquete
                         LEFT JOIN DescripcionPorPaquete DP ON P.id_paquete = DP.id_paquete
                         WHERE PE.id_envio = ?";
        $stmt_paquetes = $conn->prepare($sql_paquetes);
        $stmt_paquetes->bind_param("i", $id_envio);
        $stmt_paquetes->execute();
        $paquetes_result = $stmt_paquetes->get_result();

        // Obtener agencias de origen y destino
        $sql_agencias = "SELECT A.distrito
                         FROM EnvioPorAgencia EA
                         JOIN Agencias A ON EA.id_agencia = A.id_agencia
                         WHERE EA.id_envio = ?";
        $stmt_agencias = $conn->prepare($sql_agencias);
        $stmt_agencias->bind_param("i", $id_envio);
        $stmt_agencias->execute();
        $agencias_result = $stmt_agencias->get_result();

        // Mostrar la información
        echo "<h2>Información del Envío</h2>";

        echo "<h3>Estado de Envío</h3>";
        echo "<p>Estado: " . $estado_envio['estado'] . "</p>";
        echo "<p>Fecha de Actualización: " . $estado_envio['fecha_actualizacion'] . "</p>";

        echo "<h3>Paquetes</h3>";
        echo "<table border='1'>
                <tr>
                    <th>Largo</th>
                    <th>Ancho</th>
                    <th>Alto</th>
                    <th>Peso</th>
                    <th>Tipo de Empaque</th>
                    <th>Descripción</th>
                    <th>Precio Unitario</th>
                </tr>";
        while ($paquete = $paquetes_result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $paquete['largo'] . "</td>
                    <td>" . $paquete['ancho'] . "</td>
                    <td>" . $paquete['alto'] . "</td>
                    <td>" . $paquete['peso'] . "</td>
                    <td>" . $paquete['tipo_empaque'] . "</td>
                    <td>" . $paquete['descripcion'] . "</td>
                    <td>" . $paquete['precio_unitario'] . "</td>
                  </tr>";
        }
        echo "</table>";

        echo "<h3>Agencias</h3>";
        echo "<ul>";
        while ($agencia = $agencias_result->fetch_assoc()) {
            echo "<li>" . $agencia['distrito'] . "</li>";
        }
        echo "</ul>";

    } else {
        // Envío no encontrado
        echo "<p>Código de Envío o Clave de Seguridad incorrectos.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rastrear Envío</title>
</head>
<body>
    <h1>Rastrear Envío</h1>
    <form method="post">
        <label for="codigo_envio">Código del Envío:</label><br>
        <input type="text" id="codigo_envio" name="codigo_envio" required><br><br>

        <label for="clave_seguridad">Clave de Seguridad:</label><br>
        <input type="text" id="clave_seguridad" name="clave_seguridad" required><br><br>

        <button type="submit">Rastrear</button>
    </form>
</body>
</html>
