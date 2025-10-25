<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";

try {
    // Intentar conectar sin seleccionar base de datos
    $conn = new mysqli($servername, $username, $password);
    
    if ($conn->connect_error) {
        throw new Exception("Conexión fallida: " . $conn->connect_error);
    }
    
    // Verificar si existe la base de datos
    $result = $conn->query("SHOW DATABASES LIKE 'lemuspool_db'");
    $dbExists = $result->num_rows > 0;
    
    if (!$dbExists) {
        // Crear la base de datos y las tablas desde schema.sql
        $sql = file_get_contents(__DIR__ . '/schema.sql');
        
        if ($conn->multi_query($sql)) {
            do {
                // Consumir todos los resultados
                if ($result = $conn->store_result()) {
                    $result->free();
                }
            } while ($conn->more_results() && $conn->next_result());
        }
        
        if ($conn->error) {
            throw new Exception("Error creando la base de datos: " . $conn->error);
        }
    }
    
    // Conectar a la base de datos
    $conn->select_db('lemuspool_db');
    
    // Verificar las tablas
    $tables = [];
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
        
        // Verificar estructura de cada tabla
        $structure = [];
        $columns = $conn->query("SHOW COLUMNS FROM " . $row[0]);
        while ($col = $columns->fetch_assoc()) {
            $structure[] = $col;
        }
        $tables_info[$row[0]] = $structure;
    }
    
    // Verificar si hay datos en la tabla reservas
    $reservas = [];
    $result = $conn->query("SELECT * FROM reservas");
    while ($row = $result->fetch_assoc()) {
        $reservas[] = $row;
    }
    
    echo json_encode([
        "success" => true,
        "database_exists" => $dbExists,
        "tables" => $tables,
        "tables_info" => $tables_info,
        "reservas" => $reservas
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>