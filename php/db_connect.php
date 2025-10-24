<?php
// api/db_connect.php

$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "lemuspool_db"; 

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    http_response_code(500); // 500 Internal Server Error
    header('Content-Type: application/json');
    die(json_encode(["success" => false, "message" => "Error de conexión a la base de datos: " . $conn->connect_error]));
}

$conn->set_charset("utf8mb4");

// Función universal para enviar respuesta JSON
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>