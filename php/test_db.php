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

// Probar una consulta simple
if ($result = $conn->query("SELECT 1 AS ok")) {
    $row = $result->fetch_assoc();
    echo json_encode(["success" => true, "message" => "Conexión OK", "db_test" => $row]);
    $result->close();
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Query falló: " . $conn->error]);
}

$conn->close();
?>