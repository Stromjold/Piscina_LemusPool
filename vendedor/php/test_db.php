<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lemuspool_db";

$conn = @new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit;
}

// Verificar si existe la tabla reservas
if ($result = $conn->query("SHOW TABLES LIKE 'reservas'")) {
    if ($result->num_rows == 0) {
        echo json_encode(["success" => false, "message" => "La tabla 'reservas' no existe"]);
        exit;
    }
}

// Mostrar todas las reservas
// Intentar insertar una reserva de prueba
$fecha_hoy = date('Y-m-d');
$sql_insert = "INSERT INTO reservas (cliente_id, nombre_cliente, fecha_inicio, dias_estancia, cantidad_personas) 
               VALUES ('TEST123', 'Cliente Prueba', '$fecha_hoy', 3, 2)";

if ($conn->query($sql_insert)) {
    echo json_encode([
        "success" => true,
        "message" => "Reserva de prueba insertada",
        "fecha" => $fecha_hoy
    ]);
    $result->close();
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Query falló: " . $conn->error]);
}

$conn->close();
?>