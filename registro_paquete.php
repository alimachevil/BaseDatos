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
    const preciosPorCombinacion = {
        "Chachapoyas": {
            "Chachapoyas": 26, "Huaraz": 15, "Abancay": 25, "Arequipa": 35, "Huamanga": 30, "Cajamarca": 20, "Callao": 40, "Cusco": 45, "Huancavelica": 25, "Huánuco": 30, "Ica": 35, "Huancayo": 30, "El Tambo": 30, "Chilca": 30, "Trujillo": 20, "Chiclayo": 25, "Cercado de Lima": 40, "Iquitos": 50, "Puerto Maldonado": 55, "Moquegua": 40, "Cerro de Pasco": 35, "Piura": 25, "Puno": 50, "Moyobamba": 20, "Tacna": 55, "Gregorio Albarracín": 55, "Tumbes": 25, "Pucallpa": 50
        },
        "Huaraz": {
            "Chachapoyas": 15, "Huaraz": 5, "Abancay": 20, "Arequipa": 30, "Huamanga": 25, "Cajamarca": 15, "Callao": 35, "Cusco": 40, "Huancavelica": 20, "Huánuco": 25, "Ica": 30, "Huancayo": 25, "El Tambo": 25, "Chilca": 25, "Trujillo": 15, "Chiclayo": 20, "Cercado de Lima": 35, "Iquitos": 45, "Puerto Maldonado": 50, "Moquegua": 35, "Cerro de Pasco": 30, "Piura": 20, "Puno": 45, "Moyobamba": 15, "Tacna": 50, "Gregorio Albarracín": 50, "Tumbes": 20, "Pucallpa": 45
        },
        "Abancay": {
            "Chachapoyas": 25, "Huaraz": 20, "Abancay": 5, "Arequipa": 15, "Huamanga": 10, "Cajamarca": 25, "Callao": 20, "Cusco": 10, "Huancavelica": 15, "Huánuco": 20, "Ica": 25, "Huancayo": 20, "El Tambo": 20, "Chilca": 20, "Trujillo": 25, "Chiclayo": 25, "Cercado de Lima": 20, "Iquitos": 35, "Puerto Maldonado": 40, "Moquegua": 10, "Cerro de Pasco": 25, "Piura": 30, "Puno": 20, "Moyobamba": 30, "Tacna": 25, "Gregorio Albarracín": 25, "Tumbes": 35, "Pucallpa": 35
        },
        "Arequipa": {
            "Chachapoyas": 35, "Huaraz": 30, "Abancay": 15, "Arequipa": 5, "Huamanga": 10, "Cajamarca": 35, "Callao": 25, "Cusco": 15, "Huancavelica": 20, "Huánuco": 25, "Ica": 20, "Huancayo": 25, "El Tambo": 25, "Chilca": 25, "Trujillo": 35, "Chiclayo": 35, "Cercado de Lima": 25, "Iquitos": 45, "Puerto Maldonado": 20, "Moquegua": 10, "Cerro de Pasco": 35, "Piura": 40, "Puno": 20, "Moyobamba": 40, "Tacna": 15, "Gregorio Albarracín": 15, "Tumbes": 45, "Pucallpa": 45
        },
        "Huamanga": {
            "Chachapoyas": 30, "Huaraz": 25, "Abancay": 10, "Arequipa": 10, "Huamanga": 5, "Cajamarca": 30, "Callao": 15, "Cusco": 10, "Huancavelica": 10, "Huánuco": 15, "Ica": 20, "Huancayo": 15, "El Tambo": 15, "Chilca": 15, "Trujillo": 30, "Chiclayo": 30, "Cercado de Lima": 15, "Iquitos": 40, "Puerto Maldonado": 25, "Moquegua": 10, "Cerro de Pasco": 30, "Piura": 35, "Puno": 25, "Moyobamba": 35, "Tacna": 25, "Gregorio Albarracín": 25, "Tumbes": 40, "Pucallpa": 40
        },
        "Cajamarca": {
            "Chachapoyas": 20, "Huaraz": 15, "Abancay": 25, "Arequipa": 35, "Huamanga": 30, "Cajamarca": 5, "Callao": 20, "Cusco": 35, "Huancavelica": 25, "Huánuco": 15, "Ica": 30, "Huancayo": 25, "El Tambo": 25, "Chilca": 25, "Trujillo": 10, "Chiclayo": 15, "Cercado de Lima": 20, "Iquitos": 40, "Puerto Maldonado": 45, "Moquegua": 40, "Cerro de Pasco": 25, "Piura": 10, "Puno": 45, "Moyobamba": 10, "Tacna": 50, "Gregorio Albarracín": 50, "Tumbes": 10, "Pucallpa": 40
        },
        "Callao": {
            "Chachapoyas": 40, "Huaraz": 35, "Abancay": 20, "Arequipa": 25, "Huamanga": 15, "Cajamarca": 20, "Callao": 5, "Cusco": 25, "Huancavelica": 20, "Huánuco": 15, "Ica": 10, "Huancayo": 20, "El Tambo": 20, "Chilca": 20, "Trujillo": 20, "Chiclayo": 25, "Cercado de Lima": 5, "Iquitos": 35, "Puerto Maldonado": 40, "Moquegua": 35, "Cerro de Pasco": 15, "Piura": 25, "Puno": 40, "Moyobamba": 35, "Tacna": 45, "Gregorio Albarracín": 45, "Tumbes": 25, "Pucallpa": 35
        },
        "Cusco": {
            "Chachapoyas": 45, "Huaraz": 40, "Abancay": 10, "Arequipa": 15, "Huamanga": 10, "Cajamarca": 35, "Callao": 25, "Cusco": 5, "Huancavelica": 15, "Huánuco": 20, "Ica": 25, "Huancayo": 20, "El Tambo": 20, "Chilca": 20, "Trujillo": 35, "Chiclayo": 35, "Cercado de Lima": 25, "Iquitos": 45, "Puerto Maldonado": 20, "Moquegua": 15, "Cerro de Pasco": 30, "Piura": 40, "Puno": 10, "Moyobamba": 40, "Tacna": 25, "Gregorio Albarracín": 25, "Tumbes": 45, "Pucallpa": 45
        },
        "Huancavelica": {
            "Chachapoyas": 25, "Huaraz": 20, "Abancay": 15, "Arequipa": 20, "Huamanga": 10, "Cajamarca": 25, "Callao": 20, "Cusco": 15, "Huancavelica": 5, "Huánuco": 20, "Ica": 25, "Huancayo": 20, "El Tambo": 20, "Chilca": 20, "Trujillo": 25, "Chiclayo": 25, "Cercado de Lima": 20, "Iquitos": 35, "Puerto Maldonado": 30, "Moquegua": 25, "Cerro de Pasco": 15, "Piura": 25, "Puno": 30, "Moyobamba": 25, "Tacna": 30, "Gregorio Albarracín": 30, "Tumbes": 35, "Pucallpa": 35
        },
        "Huánuco": {
            "Chachapoyas": 30, "Huaraz": 25, "Abancay": 20, "Arequipa": 25, "Huamanga": 15, "Cajamarca": 15, "Callao": 15, "Cusco": 20, "Huancavelica": 20, "Huánuco": 5, "Ica": 25, "Huancayo": 20, "El Tambo": 20, "Chilca": 20, "Trujillo": 30, "Chiclayo": 25, "Cercado de Lima": 15, "Iquitos": 35, "Puerto Maldonado": 30, "Moquegua": 25, "Cerro de Pasco": 25, "Piura": 30, "Puno": 35, "Moyobamba": 30, "Tacna": 35, "Gregorio Albarracín": 35, "Tumbes": 35, "Pucallpa": 35
        },
        "Ica": {
            "Chachapoyas": 35, "Huaraz": 30, "Abancay": 25, "Arequipa": 20, "Huamanga": 20, "Cajamarca": 30, "Callao": 10, "Cusco": 25, "Huancavelica": 25, "Huánuco": 25, "Ica": 5, "Huancayo": 25, "El Tambo": 25, "Chilca": 25, "Trujillo": 35, "Chiclayo": 30, "Cercado de Lima": 10, "Iquitos": 40, "Puerto Maldonado": 35, "Moquegua": 25, "Cerro de Pasco": 25, "Piura": 30, "Puno": 35, "Moyobamba": 35, "Tacna": 35, "Gregorio Albarracín": 35, "Tumbes": 40, "Pucallpa": 40
        },
        "Huancayo": {
            "Chachapoyas": 30, "Huaraz": 25, "Abancay": 20, "Arequipa": 25, "Huamanga": 15, "Cajamarca": 25, "Callao": 20, "Cusco": 20, "Huancavelica": 20, "Huánuco": 20, "Ica": 25, "Huancayo": 5, "El Tambo": 5, "Chilca": 5, "Trujillo": 25, "Chiclayo": 25, "Cercado de Lima": 20, "Iquitos": 35, "Puerto Maldonado": 25, "Moquegua": 25, "Cerro de Pasco": 20, "Piura": 30, "Puno": 25, "Moyobamba": 25, "Tacna": 35, "Gregorio Albarracín": 35, "Tumbes": 35, "Pucallpa": 35
        },
        "El Tambo": {
            "Chachapoyas": 30, "Huaraz": 25, "Abancay": 20, "Arequipa": 25, "Huamanga": 15, "Cajamarca": 25, "Callao": 20, "Cusco": 20, "Huancavelica": 20, "Huánuco": 20, "Ica": 25, "Huancayo": 5, "El Tambo": 5, "Chilca": 5, "Trujillo": 25, "Chiclayo": 25, "Cercado de Lima": 20, "Iquitos": 35, "Puerto Maldonado": 25, "Moquegua": 25, "Cerro de Pasco": 20, "Piura": 30, "Puno": 25, "Moyobamba": 25, "Tacna": 35, "Gregorio Albarracín": 35, "Tumbes": 35, "Pucallpa": 35
        },
        "Chilca": {
            "Chachapoyas": 30, "Huaraz": 25, "Abancay": 20, "Arequipa": 25, "Huamanga": 15, "Cajamarca": 25, "Callao": 20, "Cusco": 20, "Huancavelica": 20, "Huánuco": 20, "Ica": 25, "Huancayo": 5, "El Tambo": 5, "Chilca": 5, "Trujillo": 25, "Chiclayo": 25, "Cercado de Lima": 20, "Iquitos": 35, "Puerto Maldonado": 25, "Moquegua": 25, "Cerro de Pasco": 20, "Piura": 30, "Puno": 25, "Moyobamba": 25, "Tacna": 35, "Gregorio Albarracín": 35, "Tumbes": 35, "Pucallpa": 35
        },
        "Trujillo": {
            "Chachapoyas": 20, "Huaraz": 15, "Abancay": 25, "Arequipa": 35, "Huamanga": 30, "Cajamarca": 10, "Callao": 20, "Cusco": 35, "Huancavelica": 25, "Huánuco": 30, "Ica": 35, "Huancayo": 25, "El Tambo": 25, "Chilca": 25, "Trujillo": 5, "Chiclayo": 10, "Cercado de Lima": 20, "Iquitos": 45, "Puerto Maldonado": 40, "Moquegua": 35, "Cerro de Pasco": 30, "Piura": 25, "Puno": 40, "Moyobamba": 25, "Tacna": 45, "Gregorio Albarracín": 45, "Tumbes": 40, "Pucallpa": 45
        },
        "Chiclayo": {
            "Chachapoyas": 25, "Huaraz": 20, "Abancay": 25, "Arequipa": 35, "Huamanga": 30, "Cajamarca": 15, "Callao": 25, "Cusco": 35, "Huancavelica": 25, "Huánuco": 25, "Ica": 30, "Huancayo": 25, "El Tambo": 25, "Chilca": 25, "Trujillo": 10, "Chiclayo": 5, "Cercado de Lima": 25, "Iquitos": 40, "Puerto Maldonado": 45, "Moquegua": 30, "Cerro de Pasco": 25, "Piura": 15, "Puno": 45, "Moyobamba": 15, "Tacna": 50, "Gregorio Albarracín": 50, "Tumbes": 45, "Pucallpa": 45
        },
        "Cercado de Lima": {
            "Chachapoyas": 40, "Huaraz": 35, "Abancay": 20, "Arequipa": 25, "Huamanga": 15, "Cajamarca": 20, "Callao": 5, "Cusco": 25, "Huancavelica": 20, "Huánuco": 15, "Ica": 10, "Huancayo": 20, "El Tambo": 20, "Chilca": 20, "Trujillo": 20, "Chiclayo": 25, "Cercado de Lima": 5, "Iquitos": 35, "Puerto Maldonado": 40, "Moquegua": 35, "Cerro de Pasco": 15, "Piura": 25, "Puno": 40, "Moyobamba": 35, "Tacna": 45, "Gregorio Albarracín": 45, "Tumbes": 40, "Pucallpa": 40
        },
        "Iquitos": {
            "Chachapoyas": 35, "Huaraz": 30, "Abancay": 30, "Arequipa": 30, "Huamanga": 25, "Cajamarca": 25, "Callao": 25, "Cusco": 35, "Huancavelica": 30, "Huánuco": 35, "Ica": 40, "Huancayo": 35, "El Tambo": 35, "Chilca": 35, "Trujillo": 45, "Chiclayo": 40, "Cercado de Lima": 35, "Iquitos": 5, "Puerto Maldonado": 35, "Moquegua": 30, "Cerro de Pasco": 30, "Piura": 40, "Puno": 35, "Moyobamba": 40, "Tacna": 45, "Gregorio Albarracín": 45, "Tumbes": 45, "Pucallpa": 45
        },
        "Puerto Maldonado": {
            "Chachapoyas": 30, "Huaraz": 30, "Abancay": 25, "Arequipa": 25, "Huamanga": 20, "Cajamarca": 20, "Callao": 20, "Cusco": 30, "Huancavelica": 30, "Huánuco": 30, "Ica": 35, "Huancayo": 25, "El Tambo": 25, "Chilca": 25, "Trujillo": 40, "Chiclayo": 45, "Cercado de Lima": 40, "Iquitos": 35, "Puerto Maldonado": 5, "Moquegua": 30, "Cerro de Pasco": 25, "Piura": 35, "Puno": 30, "Moyobamba": 35, "Tacna": 40, "Gregorio Albarracín": 40, "Tumbes": 45, "Pucallpa": 40
        },
        "Moquegua": {
            "Chachapoyas": 30, "Huaraz": 25, "Abancay": 20, "Arequipa": 20, "Huamanga": 20, "Cajamarca": 20, "Callao": 20, "Cusco": 25, "Huancavelica": 25, "Huánuco": 25, "Ica": 25, "Huancayo": 25, "El Tambo": 25, "Chilca": 25, "Trujillo": 35, "Chiclayo": 30, "Cercado de Lima": 35, "Iquitos": 30, "Puerto Maldonado": 30, "Moquegua": 5, "Cerro de Pasco": 20, "Piura": 30, "Puno": 30, "Moyobamba": 25, "Tacna": 35, "Gregorio Albarracín": 35, "Tumbes": 35, "Pucallpa": 35
        },
        "Cerro de Pasco": {
            "Chachapoyas": 15, "Huaraz": 15, "Abancay": 15, "Arequipa": 15, "Huamanga": 10, "Cajamarca": 10, "Callao": 15, "Cusco": 15, "Huancavelica": 10, "Huánuco": 25, "Ica": 25, "Huancayo": 20, "El Tambo": 20, "Chilca": 20, "Trujillo": 30, "Chiclayo": 25, "Cercado de Lima": 15, "Iquitos": 30, "Puerto Maldonado": 25, "Moquegua": 20, "Cerro de Pasco": 5, "Piura": 20, "Puno": 25, "Moyobamba": 20, "Tacna": 30, "Gregorio Albarracín": 30, "Tumbes": 30, "Pucallpa": 30
        },
        "Piura": {
            "Chachapoyas": 25, "Huaraz": 20, "Abancay": 25, "Arequipa": 30, "Huamanga": 25, "Cajamarca": 15, "Callao": 25, "Cusco": 25, "Huancavelica": 25, "Huánuco": 30, "Ica": 30, "Huancayo": 30, "El Tambo": 30, "Chilca": 30, "Trujillo": 25, "Chiclayo": 15, "Cercado de Lima": 25, "Iquitos": 40, "Puerto Maldonado": 35, "Moquegua": 30, "Cerro de Pasco": 20, "Piura": 5, "Puno": 35, "Moyobamba": 25, "Tacna": 40, "Gregorio Albarracín": 40, "Tumbes": 40, "Pucallpa": 40
        },
        "Puno": {
            "Chachapoyas": 30, "Huaraz": 25, "Abancay": 25, "Arequipa": 30, "Huamanga": 25, "Cajamarca": 20, "Callao": 25, "Cusco": 30, "Huancavelica": 25, "Huánuco": 35, "Ica": 35, "Huancayo": 25, "El Tambo": 25, "Chilca": 25, "Trujillo": 40, "Chiclayo": 45, "Cercado de Lima": 40, "Iquitos": 35, "Puerto Maldonado": 30, "Moquegua": 30, "Cerro de Pasco": 25, "Piura": 35, "Puno": 5, "Moyobamba": 30, "Tacna": 40, "Gregorio Albarracín": 40, "Tumbes": 45, "Pucallpa": 40
        },
        "Moyobamba": {
            "Chachapoyas": 30, "Huaraz": 25, "Abancay": 20, "Arequipa": 25, "Huamanga": 15, "Cajamarca": 20, "Callao": 20, "Cusco": 25, "Huancavelica": 20, "Huánuco": 30, "Ica": 35, "Huancayo": 25, "El Tambo": 25, "Chilca": 25, "Trujillo": 25, "Chiclayo": 15, "Cercado de Lima": 35, "Iquitos": 40, "Puerto Maldonado": 35, "Moquegua": 25, "Cerro de Pasco": 20, "Piura": 25, "Puno": 30, "Moyobamba": 5, "Tacna": 40, "Gregorio Albarracín": 40, "Tumbes": 40, "Pucallpa": 40
        },
        "Tacna": {
            "Chachapoyas": 35, "Huaraz": 30, "Abancay": 30, "Arequipa": 35, "Huamanga": 30, "Cajamarca": 20, "Callao": 30, "Cusco": 30, "Huancavelica": 30, "Huánuco": 35, "Ica": 35, "Huancayo": 35, "El Tambo": 35, "Chilca": 35, "Trujillo": 45, "Chiclayo": 50, "Cercado de Lima": 45, "Iquitos": 45, "Puerto Maldonado": 40, "Moquegua": 35, "Cerro de Pasco": 30, "Piura": 40, "Puno": 40, "Moyobamba": 40, "Tacna": 5, "Gregorio Albarracín": 5, "Tumbes": 45, "Pucallpa": 45
        },
        "Gregorio Albarracín": {
            "Chachapoyas": 35, "Huaraz": 30, "Abancay": 30, "Arequipa": 35, "Huamanga": 30, "Cajamarca": 20, "Callao": 30, "Cusco": 30, "Huancavelica": 30, "Huánuco": 35, "Ica": 35, "Huancayo": 35, "El Tambo": 35, "Chilca": 35, "Trujillo": 45, "Chiclayo": 50, "Cercado de Lima": 45, "Iquitos": 45, "Puerto Maldonado": 40, "Moquegua": 35, "Cerro de Pasco": 30, "Piura": 40, "Puno": 40, "Moyobamba": 40, "Tacna": 5, "Gregorio Albarracín": 5, "Tumbes": 45, "Pucallpa": 45
        },
        "Tumbes": {
            "Chachapoyas": 40, "Huaraz": 35, "Abancay": 35, "Arequipa": 40, "Huamanga": 35, "Cajamarca": 30, "Callao": 35, "Cusco": 35, "Huancavelica": 35, "Huánuco": 40, "Ica": 40, "Huancayo": 35, "El Tambo": 35, "Chilca": 35, "Trujillo": 45, "Chiclayo": 50, "Cercado de Lima": 45, "Iquitos": 45, "Puerto Maldonado": 45, "Moquegua": 35, "Cerro de Pasco": 30, "Piura": 40, "Puno": 45, "Moyobamba": 40, "Tacna": 45, "Gregorio Albarracín": 45, "Tumbes": 5, "Pucallpa": 45
        },
        "Pucallpa": {
            "Chachapoyas": 40, "Huaraz": 35, "Abancay": 35, "Arequipa": 40, "Huamanga": 35, "Cajamarca": 25, "Callao": 40, "Cusco": 35, "Huancavelica": 35, "Huánuco": 40, "Ica": 40, "Huancayo": 40, "El Tambo": 40, "Chilca": 40, "Trujillo": 45, "Chiclayo": 50, "Cercado de Lima": 45, "Iquitos": 45, "Puerto Maldonado": 40, "Moquegua": 35, "Cerro de Pasco": 30, "Piura": 40, "Puno": 40, "Moyobamba": 40, "Tacna": 45, "Gregorio Albarracín": 45, "Tumbes": 45, "Pucallpa": 5
        }
    }

        function calcularPrecio() {
            var tipoEmpaque = document.getElementById("tipo_empaque").value;
            var agenciaOrigen = document.getElementById("agencia_origen").selectedOptions[0].text.split(" - ")[2];
            var agenciaDestino = document.getElementById("agencia_destino").selectedOptions[0].text.split(" - ")[2];
            var precioBase = preciosPorCombinacion[agenciaOrigen][agenciaDestino] || 0;
            var precioFinal = precioBase;

            if (tipoEmpaque === "Sobre") {
                precioFinal = precioBase / 2;
            } else if (tipoEmpaque === "Paquete mediano") {
                precioFinal = precioBase - 5;
            } else if (tipoEmpaque === "Paquete") {
                precioFinal = precioBase;
            } else if (tipoEmpaque === "Personalizado") {
                var largo = parseFloat(document.getElementById("largo").value) || 0;
                var ancho = parseFloat(document.getElementById("ancho").value) || 0;
                var alto = parseFloat(document.getElementById("alto").value) || 0;
                var peso = parseFloat(document.getElementById("peso").value) || 0;

                precioFinal += 0.1 * (largo + ancho + alto) + 0.05 * peso;
            }

            document.getElementById("precio_unitario").value = precioFinal.toFixed(2);
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("tipo_empaque").addEventListener("change", function() {
                var isPersonalizado = this.value === "Personalizado";
                document.getElementById("largo").disabled = !isPersonalizado;
                document.getElementById("ancho").disabled = !isPersonalizado;
                document.getElementById("alto").disabled = !isPersonalizado;
                document.getElementById("peso").disabled = !isPersonalizado;
                calcularPrecio();
            });

            var elementosCambioPrecio = ["tipo_empaque", "agencia_origen", "agencia_destino", "largo", "ancho", "alto", "peso"];
            elementosCambioPrecio.forEach(function(id) {
                document.getElementById(id).addEventListener("change", calcularPrecio);
            });

            calcularPrecio(); // Calcular precio inicial
        });

        function redirigirPago() {
            window.location.href = 'documentacion.php';
        }
    </script>
</head>
<body>
    <h1>Registro de Paquete</h1>
    <form action="procesar_paquete.php" method="post">
        <label for="agencia_origen">Agencia de Origen:</label>
        <select id="agencia_origen" name="agencia_origen" required>
            <?php foreach ($agencias as $id => $nombre): ?>
                <option value="<?php echo $id; ?>"><?php echo $nombre; ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="agencia_destino">Agencia de Destino:</label>
        <select id="agencia_destino" name="agencia_destino" required>
            <?php foreach ($agencias as $id => $nombre): ?>
                <option value="<?php echo $id; ?>"><?php echo $nombre; ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="tipo_empaque">Tipo de Empaque:</label>
        <select id="tipo_empaque" name="tipo_empaque" required>
            <option value="Sobre">Sobre</option>
            <option value="Paquete mediano">Paquete mediano</option>
            <option value="Paquete">Paquete</option>
            <option value="Personalizado">Personalizado</option>
        </select><br>

        <label for="largo">Largo (cm):</label>
        <input type="number" id="largo" name="largo" step="1" disabled><br>

        <label for="ancho">Ancho (cm):</label>
        <input type="number" id="ancho" name="ancho" step="1" disabled><br>

        <label for="alto">Alto (cm):</label>
        <input type="number" id="alto" name="alto" step="1" disabled><br>

        <label for="peso">Peso (kg):</label>
        <input type="number" id="peso" name="peso" step="1" disabled><br>

        <label for="precio_unitario">Precio Unitario (S/.):</label>
        <input type="text" id="precio_unitario" name="precio_unitario" readonly><br>

        <button type="submit" onclick="registrarYContinuar()">Registrar y Continuar</button>
        <button type="button" onclick="redirigirPago()">Pasar a Pago</button>
    </form>
</body>
</html>
