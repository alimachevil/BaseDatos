<?php
session_start();

if (!isset($_SESSION['numero_documento'])) {
    header("Location: login.html");
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

// Obtener las agencias para el formulario
$sql = "SELECT id_agencia, departamento, provincia, distrito FROM Agencias";
$result = $conn->query($sql);

$agencias = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $agencias[$row["id_agencia"]] = $row["departamento"] . " - " . $row["provincia"] . " - " . $row["distrito"];
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Paquete</title>
    <script>
        function calcularPrecio() {
            var tipoEmpaque = document.getElementById("tipo_empaque").value;
            var agenciaOrigen = document.getElementById("agencia_origen").value;
            var agenciaDestino = document.getElementById("agencia_destino").value;
            var precioUnitario = 0;

            if (agenciaOrigen && agenciaDestino) {
                var xhttp = new XMLHttpRequest();
                xhttp.open("POST", "calcular_precio.php", true);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhttp.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        var response = JSON.parse(this.responseText);
                        precioUnitario = response.precio_unitario;

                        if (tipoEmpaque === "Personalizado") {
                            var largo = parseFloat(document.getElementById("largo").value) || 0;
                            var ancho = parseFloat(document.getElementById("ancho").value) || 0;
                            var alto = parseFloat(document.getElementById("alto").value) || 0;
                            var peso = parseFloat(document.getElementById("peso").value) || 0;

                            if (largo && ancho && alto && peso) {
                                precioUnitario += (peso - 1) * 2; // Ejemplo de cálculo adicional por peso
                            }
                        }
                        document.getElementById("precio_unitario").value = precioUnitario.toFixed(2);
                    }
                };
                xhttp.send("agencia_origen=" + agenciaOrigen + "&agencia_destino=" + agenciaDestino);
            }
        }

        function toggleCustomFields() {
            var tipoEmpaque = document.getElementById("tipo_empaque").value;
            var fields = document.querySelectorAll(".custom-fields");
            fields.forEach(function(field) {
                field.style.display = tipoEmpaque === "Personalizado" ? "block" : "none";
            });
        }
    </script>
</head>
<body>
    <h1>Registro de Paquete</h1>
    <form action="procesar_paquete.php" method="post">
        <label for="tipo_empaque">Tipo de Empaque:</label>
        <select id="tipo_empaque" name="tipo_empaque" onchange="toggleCustomFields(); calcularPrecio();" required>
            <option value="Sobre">Sobre</option>
            <option value="Paquete mediano">Paquete mediano</option>
            <option value="Paquete">Paquete</option>
            <option value="Personalizado">Personalizado</option>
        </select><br>

        <label for="agencia_origen">Agencia de Origen:</label>
        <select id="agencia_origen" name="agencia_origen" onchange="calcularPrecio();" required>
            <?php foreach ($agencias as $id => $nombre): ?>
                <option value="<?= $id ?>"><?= $nombre ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="agencia_destino">Agencia de Destino:</label>
        <select id="agencia_destino" name="agencia_destino" onchange="calcularPrecio();" required>
            <?php foreach ($agencias as $id => $nombre): ?>
                <option value="<?= $id ?>"><?= $nombre ?></option>
            <?php endforeach; ?>
        </select><br>

        <div class="custom-fields" style="display: none;">
            <label for="largo">Largo (cm):</label>
            <input type="number" id="largo" name="largo" step="0.1"><br>
            
            <label for="ancho">Ancho (cm):</label>
            <input type="number" id="ancho" name="ancho" step="0.1"><br>
            
            <label for="alto">Alto (cm):</label>
            <input type="number" id="alto" name="alto" step="0.1"><br>
            
            <label for="peso">Peso (kg):</label>
            <input type="number" id="peso" name="peso" step="0.1"><br>
        </div>

        <label for="precio_unitario">Precio Unitario (S/):</label>
        <input type="text" id="precio_unitario" name="precio_unitario" readonly><br>

        <button type="submit">Registrar Paquete</button>
    </form>
</body>
</html>
