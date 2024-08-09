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
        "Chachapoyas": 25, "Huaraz": 20, "Abancay": 15, "Arequipa": 20, "Huamanga": 10, "Cajamarca": 25, "Callao": 20, "Cusco": 15, "Huancavelica": 5, "Huánuco": 20, "Ica": 10, "Huancayo": 10, "El Tambo": 10, "Chilca": 10, "Trujillo": 25, "Chiclayo": 25, "Cercado de Lima": 20, "Iquitos": 35, "Puerto Maldonado": 40, "Moquegua": 25, "Cerro de Pasco": 20, "Piura": 30, "Puno": 20, "Moyobamba": 30, "Tacna": 25, "Gregorio Albarracín": 25, "Tumbes": 35, "Pucallpa": 35
    },
    "Huánuco": {
        "Chachapoyas": 30, "Huaraz": 25, "Abancay": 20, "Arequipa": 25, "Huamanga": 15, "Cajamarca": 15, "Callao": 15, "Cusco": 20, "Huancavelica": 20, "Huánuco": 5, "Ica": 15, "Huancayo": 10, "El Tambo": 10, "Chilca": 10, "Trujillo": 20, "Chiclayo": 20, "Cercado de Lima": 15, "Iquitos": 35, "Puerto Maldonado": 40, "Moquegua": 30, "Cerro de Pasco": 10, "Piura": 20, "Puno": 40, "Moyobamba": 20, "Tacna": 45, "Gregorio Albarracín": 45, "Tumbes": 20, "Pucallpa": 35
    },
    "Ica": {
        "Chachapoyas": 35, "Huaraz": 30, "Abancay": 25, "Arequipa": 20, "Huamanga": 20, "Cajamarca": 30, "Callao": 10, "Cusco": 25, "Huancavelica": 10, "Huánuco": 15, "Ica": 5, "Huancayo": 10, "El Tambo": 10, "Chilca": 10, "Trujillo": 25, "Chiclayo": 25, "Cercado de Lima": 10, "Iquitos": 35, "Puerto Maldonado": 30, "Moquegua": 15, "Cerro de Pasco": 15, "Piura": 30, "Puno": 30, "Moyobamba": 30, "Tacna": 35, "Gregorio Albarracín": 35, "Tumbes": 35, "Pucallpa": 35
    },
    "Huancayo": {
        "Chachapoyas": 30, "Huaraz": 25, "Abancay": 20, "Arequipa": 25, "Huamanga": 15, "Cajamarca": 25, "Callao": 20, "Cusco": 20, "Huancavelica": 10, "Huánuco": 10, "Ica": 10, "Huancayo": 5, "El Tambo": 5, "Chilca": 5, "Trujillo": 25, "Chiclayo": 25, "Cercado de Lima": 20, "Iquitos": 35, "Puerto Maldonado": 40, "Moquegua": 30, "Cerro de Pasco": 10, "Piura": 30, "Puno": 30, "Moyobamba": 30, "Tacna": 35, "Gregorio Albarracín": 35, "Tumbes": 35, "Pucallpa": 35
    },
    "El Tambo": {
        "Chachapoyas": 30, "Huaraz": 25, "Abancay": 20, "Arequipa": 25, "Huamanga": 15, "Cajamarca": 25, "Callao": 20, "Cusco": 20, "Huancavelica": 10, "Huánuco": 10, "Ica": 10, "Huancayo": 5, "El Tambo": 5, "Chilca": 5, "Trujillo": 25, "Chiclayo": 25, "Cercado de Lima": 20, "Iquitos": 35, "Puerto Maldonado": 40, "Moquegua": 30, "Cerro de Pasco": 10, "Piura": 30, "Puno": 30, "Moyobamba": 30, "Tacna": 35, "Gregorio Albarracín": 35, "Tumbes": 35, "Pucallpa": 35
    },
    "Chilca": {
        "Chachapoyas": 30, "Huaraz": 25, "Abancay": 20, "Arequipa": 25, "Huamanga": 15, "Cajamarca": 25, "Callao": 20, "Cusco": 20, "Huancavelica": 10, "Huánuco": 10, "Ica": 10, "Huancayo": 5, "El Tambo": 5, "Chilca": 5, "Trujillo": 25, "Chiclayo": 25, "Cercado de Lima": 20, "Iquitos": 35, "Puerto Maldonado": 40, "Moquegua": 30, "Cerro de Pasco": 10, "Piura": 30, "Puno": 30, "Moyobamba": 30, "Tacna": 35, "Gregorio Albarracín": 35, "Tumbes": 35, "Pucallpa": 35
    },
    "Trujillo": {
        "Chachapoyas": 20, "Huaraz": 15, "Abancay": 25, "Arequipa": 35, "Huamanga": 30, "Cajamarca": 10, "Callao": 20, "Cusco": 35, "Huancavelica": 25, "Huánuco": 20, "Ica": 25, "Huancayo": 25, "El Tambo": 25, "Chilca": 25, "Trujillo": 5, "Chiclayo": 10, "Cercado de Lima": 20, "Iquitos": 40, "Puerto Maldonado": 45, "Moquegua": 40, "Cerro de Pasco": 25, "Piura": 15, "Puno": 45, "Moyobamba": 15, "Tacna": 50, "Gregorio Albarracín": 50, "Tumbes": 15, "Pucallpa": 40
    },
    "Chiclayo": {
        "Chachapoyas": 25, "Huaraz": 20, "Abancay": 25, "Arequipa": 35, "Huamanga": 30, "Cajamarca": 15, "Callao": 25, "Cusco": 35, "Huancavelica": 25, "Huánuco": 20, "Ica": 25, "Huancayo": 25, "El Tambo": 25, "Chilca": 25, "Trujillo": 10, "Chiclayo": 5, "Cercado de Lima": 25, "Iquitos": 40, "Puerto Maldonado": 45, "Moquegua": 40, "Cerro de Pasco": 25, "Piura": 10, "Puno": 45, "Moyobamba": 10, "Tacna": 50, "Gregorio Albarracín": 50, "Tumbes": 10, "Pucallpa": 40
    },
    "Cercado de Lima": {
        "Chachapoyas": 40, "Huaraz": 35, "Abancay": 20, "Arequipa": 25, "Huamanga": 15, "Cajamarca": 20, "Callao": 5, "Cusco": 25, "Huancavelica": 20, "Huánuco": 15, "Ica": 10, "Huancayo": 20, "El Tambo": 20, "Chilca": 20, "Trujillo": 20, "Chiclayo": 25, "Cercado de Lima": 5, "Iquitos": 35, "Puerto Maldonado": 40, "Moquegua": 35, "Cerro de Pasco": 15, "Piura": 25, "Puno": 40, "Moyobamba": 35, "Tacna": 45, "Gregorio Albarracín": 45, "Tumbes": 25, "Pucallpa": 35
    },
    "Iquitos": {
        "Chachapoyas": 50, "Huaraz": 45, "Abancay": 35, "Arequipa": 45, "Huamanga": 40, "Cajamarca": 40, "Callao": 35, "Cusco": 45, "Huancavelica": 35, "Huánuco": 35, "Ica": 35, "Huancayo": 35, "El Tambo": 35, "Chilca": 35, "Trujillo": 40, "Chiclayo": 40, "Cercado de Lima": 35, "Iquitos": 5, "Puerto Maldonado": 50, "Moquegua": 45, "Cerro de Pasco": 35, "Piura": 45, "Puno": 50, "Moyobamba": 45, "Tacna": 55, "Gregorio Albarracín": 55, "Tumbes": 45, "Pucallpa": 5
    },
    "Puerto Maldonado": {
        "Chachapoyas": 55, "Huaraz": 50, "Abancay": 40, "Arequipa": 20, "Huamanga": 25, "Cajamarca": 45, "Callao": 40, "Cusco": 20, "Huancavelica": 40, "Huánuco": 40, "Ica": 30, "Huancayo": 40, "El Tambo": 40, "Chilca": 40, "Trujillo": 45, "Chiclayo": 45, "Cercado de Lima": 40, "Iquitos": 50, "Puerto Maldonado": 5, "Moquegua": 15, "Cerro de Pasco": 40, "Piura": 50, "Puno": 10, "Moyobamba": 50, "Tacna": 15, "Gregorio Albarracín": 15, "Tumbes": 55, "Pucallpa": 50
    },
    "Moquegua": {
        "Chachapoyas": 40, "Huaraz": 35, "Abancay": 10, "Arequipa": 10, "Huamanga": 10, "Cajamarca": 40, "Callao": 35, "Cusco": 15, "Huancavelica": 25, "Huánuco": 30, "Ica": 15, "Huancayo": 30, "El Tambo": 30, "Chilca": 30, "Trujillo": 40, "Chiclayo": 40, "Cercado de Lima": 35, "Iquitos": 45, "Puerto Maldonado": 15, "Moquegua": 5, "Cerro de Pasco": 30, "Piura": 45, "Puno": 20, "Moyobamba": 45, "Tacna": 10, "Gregorio Albarracín": 10, "Tumbes": 45, "Pucallpa": 45
    },
    "Cerro de Pasco": {
        "Chachapoyas": 35, "Huaraz": 30, "Abancay": 25, "Arequipa": 35, "Huamanga": 30, "Cajamarca": 25, "Callao": 15, "Cusco": 30, "Huancavelica": 20, "Huánuco": 10, "Ica": 15, "Huancayo": 10, "El Tambo": 10, "Chilca": 10, "Trujillo": 25, "Chiclayo": 25, "Cercado de Lima": 15, "Iquitos": 35, "Puerto Maldonado": 40, "Moquegua": 30, "Cerro de Pasco": 5, "Piura": 30, "Puno": 40, "Moyobamba": 30, "Tacna": 45, "Gregorio Albarracín": 45, "Tumbes": 35, "Pucallpa": 35
    },
    "Piura": {
        "Chachapoyas": 25, "Huaraz": 20, "Abancay": 30, "Arequipa": 40, "Huamanga": 35, "Cajamarca": 10, "Callao": 25, "Cusco": 40, "Huancavelica": 30, "Huánuco": 20, "Ica": 30, "Huancayo": 30, "El Tambo": 30, "Chilca": 30, "Trujillo": 15, "Chiclayo": 10, "Cercado de Lima": 25, "Iquitos": 45, "Puerto Maldonado": 50, "Moquegua": 45, "Cerro de Pasco": 30, "Piura": 5, "Puno": 50, "Moyobamba": 5, "Tacna": 55, "Gregorio Albarracín": 55, "Tumbes": 5, "Pucallpa": 45
    },
    "Puno": {
        "Chachapoyas": 50, "Huaraz": 45, "Abancay": 20, "Arequipa": 20, "Huamanga": 25, "Cajamarca": 45, "Callao": 40, "Cusco": 10, "Huancavelica": 20, "Huánuco": 40, "Ica": 30, "Huancayo": 30, "El Tambo": 30, "Chilca": 30, "Trujillo": 45, "Chiclayo": 45, "Cercado de Lima": 40, "Iquitos": 50, "Puerto Maldonado": 10, "Moquegua": 20, "Cerro de Pasco": 40, "Piura": 50, "Puno": 5, "Moyobamba": 50, "Tacna": 20, "Gregorio Albarracín": 20, "Tumbes": 50, "Pucallpa": 50
    },
    "Moyobamba": {
        "Chachapoyas": 20, "Huaraz": 15, "Abancay": 30, "Arequipa": 40, "Huamanga": 35, "Cajamarca": 10, "Callao": 35, "Cusco": 40, "Huancavelica": 30, "Huánuco": 20, "Ica": 30, "Huancayo": 30, "El Tambo": 30, "Chilca": 30, "Trujillo": 15, "Chiclayo": 10, "Cercado de Lima": 35, "Iquitos": 45, "Puerto Maldonado": 50, "Moquegua": 45, "Cerro de Pasco": 30, "Piura": 5, "Puno": 50, "Moyobamba": 5, "Tacna": 55, "Gregorio Albarracín": 55, "Tumbes": 10, "Pucallpa": 45
    },
    "Tacna": {
        "Chachapoyas": 55, "Huaraz": 50, "Abancay": 25, "Arequipa": 15, "Huamanga": 25, "Cajamarca": 50, "Callao": 45, "Cusco": 25, "Huancavelica": 25, "Huánuco": 45, "Ica": 35, "Huancayo": 35, "El Tambo": 35, "Chilca": 35, "Trujillo": 50, "Chiclayo": 50, "Cercado de Lima": 45, "Iquitos": 55, "Puerto Maldonado": 15, "Moquegua": 10, "Cerro de Pasco": 45, "Piura": 55, "Puno": 20, "Moyobamba": 55, "Tacna": 5, "Gregorio Albarracín": 5, "Tumbes": 55, "Pucallpa": 55
    },
    "Gregorio Albarracín": {
        "Chachapoyas": 55, "Huaraz": 50, "Abancay": 25, "Arequipa": 15, "Huamanga": 25, "Cajamarca": 50, "Callao": 45, "Cusco": 25, "Huancavelica": 25, "Huánuco": 45, "Ica": 35, "Huancayo": 35, "El Tambo": 35, "Chilca": 35, "Trujillo": 50, "Chiclayo": 50, "Cercado de Lima": 45, "Iquitos": 55, "Puerto Maldonado": 15, "Moquegua": 10, "Cerro de Pasco": 45, "Piura": 55, "Puno": 20, "Moyobamba": 55, "Tacna": 5, "Gregorio Albarracín": 5, "Tumbes": 55, "Pucallpa": 55
    },
    "Tumbes": {
        "Chachapoyas": 25, "Huaraz": 20, "Abancay": 30, "Arequipa": 40, "Huamanga": 35, "Cajamarca": 15, "Callao": 25, "Cusco": 40, "Huancavelica": 30, "Huánuco": 20, "Ica": 30, "Huancayo": 30, "El Tambo": 30, "Chilca": 30, "Trujillo": 15, "Chiclayo": 10, "Cercado de Lima": 25, "Iquitos": 45, "Puerto Maldonado": 50, "Moquegua": 45, "Cerro de Pasco": 30, "Piura": 5, "Puno": 50, "Moyobamba": 5, "Tacna": 55, "Gregorio Albarracín": 55, "Tumbes": 5, "Pucallpa": 45
    },
    "Pucallpa": {
        "Chachapoyas": 50, "Huaraz": 45, "Abancay": 35, "Arequipa": 45, "Huamanga": 40, "Cajamarca": 40, "Callao": 35, "Cusco": 45, "Huancavelica": 35, "Huánuco": 35, "Ica": 35, "Huancayo": 35, "El Tambo": 35, "Chilca": 35, "Trujillo": 40, "Chiclayo": 40, "Cercado de Lima": 35, "Iquitos": 5, "Puerto Maldonado": 50, "Moquegua": 45, "Cerro de Pasco": 35, "Piura": 45, "Puno": 50, "Moyobamba": 45, "Tacna": 55, "Gregorio Albarracín": 55, "Tumbes": 45, "Pucallpa": 5
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

    </script>
</head>
<style>
        .card-container {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 20px;
            margin: 20px 0;
        }

        .card {
            flex: 0 0 auto;
            width: 250px;
            margin-right: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card img {
            width: 100%;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .card h3 {
            font-size: 18px;
            margin: 10px 0;
        }

        .card p {
            font-size: 14px;
            padding: 0 10px 10px;
        }

        .carousel-controls {
            text-align: center;
            margin-top: 10px;
        }

        .carousel-controls button {
            padding: 5px 10px;
            font-size: 16px;
            margin: 0 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Registro de Paquete</h1>
    <form action="procesar_paquete.php" method="post">
        <!-- Formulario del registro de paquetes -->
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

        <label for="largo">Largo (cm) Extra:</label>
        <input type="number" id="largo" name="largo" step="1" disabled><br>

        <label for="ancho">Ancho (cm) Extra:</label>
        <input type="number" id="ancho" name="ancho" step="1" disabled><br>

        <label for="alto">Alto (cm) Extra:</label>
        <input type="number" id="alto" name="alto" step="1" disabled><br>

        <label for="peso">Peso (kg) Extra:</label>
        <input type="number" id="peso" name="peso" step="1" disabled><br>

        <label for="precio_unitario">Precio Unitario (S/.):</label>
        <input type="text" id="precio_unitario" name="precio_unitario" readonly><br>

        <button type="submit" onclick="registrarYContinuar()">Registrar y Continuar</button>
        <button type="button" onclick="window.location.href='documentacion.php'">Continuar</button>
    </form>

    <div class="card-container" id="cardContainer">
        <div class="card">
            <img src="sobre.png" alt="Imagen 1">
            <h3>Sobre</h3>
            <p>Documentos simples en sobre manila / Tamaño A4</p>
        </div>
        <div class="card">
            <img src="paquete.png" alt="Imagen 2">
            <h3>Paquete Mediano</h3>
            <p>30 x 24 x 20 cm | Peso máx. 5 kg</p>
        </div>
        <div class="card">
            <img src="seguro.png" alt="Imagen 3">
            <h3>Paquete</h3>
            <p>42 x 30 x 23 cm | Peso máx. 10 kg</p>
        </div>
        <!-- Añade más tarjetas aquí según sea necesario -->
    </div>
</body>
</html>