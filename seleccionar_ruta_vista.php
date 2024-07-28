<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Ruta</title>
</head>
<body>
    <h1>Seleccionar Ruta</h1>

    <!-- Formulario de Selección de Ruta -->
    <form action="seleccionar_ruta.php" method="post">
        <label for="agencia_origen">Agencia de Origen:</label>
        <select id="agencia_origen" name="agencia_origen" required>
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "DBU1";

            // Crear conexión
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Verificar conexión
            if ($conn->connect_error) {
                die("Conexión fallida: " . $conn->connect_error);
            }

            // Obtener las agencias
            $sql = "SELECT id_agencia, departamento, provincia, distrito FROM Agencias";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $agencias = $result->fetch_all(MYSQLI_ASSOC);
                foreach ($agencias as $row) {
                    echo "<option value='" . $row["id_agencia"] . "'>" . $row["departamento"] . " - " . $row["provincia"] . " - " . $row["distrito"] . "</option>";
                }
            } else {
                echo "<option value=''>No hay agencias disponibles</option>";
            }
            ?>
        </select><br>

        <label for="agencia_destino">Agencia de Destino:</label>
        <select id="agencia_destino" name="agencia_destino" required>
            <?php
            if (!empty($agencias)) {
                foreach ($agencias as $row) {
                    echo "<option value='" . $row["id_agencia"] . "'>" . $row["departamento"] . " - " . $row["provincia"] . " - " . $row["distrito"] . "</option>";
                }
            }
            $conn->close();
            ?>
        </select><br>

        <button type="submit">Seleccionar Ruta</button>
    </form>
</body>
</html>
