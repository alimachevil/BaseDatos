<!DOCTYPE html>
<html>
<head>
    <title>Registro de Paquetes</title>
</head>
<body>
    <h1>Registrar Información de Paquetes</h1>
    <form action="procesar_paquetes.php" method="post">
        <h2>Paquete 1</h2>
        Largo: <input type="text" name="largo1" required><br>
        Ancho: <input type="text" name="ancho1" required><br>
        Alto: <input type="text" name="alto1" required><br>
        Peso: <input type="text" name="peso1" required><br><br>

        <h2>Paquete 2</h2>
        Largo: <input type="text" name="largo2" required><br>
        Ancho: <input type="text" name="ancho2" required><br>
        Alto: <input type="text" name="alto2" required><br>
        Peso: <input type="text" name="peso2" required><br><br>

        <h2>Paquete 3</h2>
        Largo: <input type="text" name="largo3" required><br>
        Ancho: <input type="text" name="ancho3" required><br>
        Alto: <input type="text" name="alto3" required><br>
        Peso: <input type="text" name="peso3" required><br><br>

        <input type="submit" value="Calcular y Registrar">
    </form>
</body>
</html>
