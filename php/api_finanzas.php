<?php
// api/api_finanzas.php
require 'db_connect.php'; 

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        if ($action === 'reportes') {
            $sql_totales = "SELECT 
                SUM(CASE WHEN tipo = 'ingreso' THEN total ELSE 0 END) AS total_ingresos,
                SUM(CASE WHEN tipo = 'gasto' THEN total ELSE 0 END) AS total_gastos
                FROM transacciones";
            $result_totales = $conn->query($sql_totales);
            $totales = $result_totales->fetch_assoc();
            
            $sql_categorias = "SELECT categoria, tipo, SUM(total) as monto_total 
                               FROM transacciones 
                               GROUP BY categoria, tipo 
                               ORDER BY tipo, categoria";
            $result_categorias = $conn->query($sql_categorias);
            $categorias = [];
            while($row = $result_categorias->fetch_assoc()) {
                $categorias[] = $row;
            }
            
            $sql_historial = "SELECT id, fecha, descripcion, cantidad, precio, categoria, tipo, total FROM transacciones ORDER BY fecha DESC, id DESC";
            $result_historial = $conn->query($sql_historial);
            $historial = [];
            while($row = $result_historial->fetch_assoc()) {
                $historial[] = $row;
            }

            sendJsonResponse([
                "success" => true,
                "totales" => $totales,
                "categorias" => $categorias,
                "historial" => $historial
            ]);
        } else {
            sendJsonResponse(["success" => false, "message" => "Acción GET no válida."], 400);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        
        $fecha = $data['fecha'] ?? '';
        $descripcion = $data['descripcion'] ?? '';
        $cantidad = $data['cantidad'] ?? 1;
        $precio = $data['precio'] ?? 0.00;
        $categoria = $data['categoria'] ?? '';
        $tipo = $data['tipo'] ?? '';
        $total = ($cantidad * $precio);

        if (empty($fecha) || empty($descripcion) || empty($categoria) || !in_array($tipo, ['ingreso', 'gasto'])) {
            sendJsonResponse(["success" => false, "message" => "Faltan datos obligatorios para la transacción."], 400);
        }

        $sql = "INSERT INTO transacciones (fecha, descripcion, cantidad, precio, categoria, tipo, total) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssidsd", $fecha, $descripcion, $cantidad, $precio, $categoria, $tipo, $total);

        if ($stmt->execute()) {
            sendJsonResponse(["success" => true, "message" => "Transacción registrada con éxito."]);
        } else {
            sendJsonResponse(["success" => false, "message" => "Error al registrar transacción: " . $stmt->error], 500);
        }
        $stmt->close();
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        $transaccion_id = $data['id'] ?? null;
        $action_delete = $data['action'] ?? null;

        if ($action_delete === 'limpiar_historial') {
            $sql = "TRUNCATE TABLE transacciones"; 
            if ($conn->query($sql)) {
                sendJsonResponse(["success" => true, "message" => "Historial de transacciones limpiado con éxito."]);
            } else {
                sendJsonResponse(["success" => false, "message" => "Error al limpiar historial: " . $conn->error], 500);
            }
        } else if ($transaccion_id) {
            $stmt = $conn->prepare("DELETE FROM transacciones WHERE id = ?");
            $stmt->bind_param("i", $transaccion_id);
            if ($stmt->execute()) {
                sendJsonResponse(["success" => true, "message" => "Transacción eliminada con éxito."]);
            } else {
                sendJsonResponse(["success" => false, "message" => "Error al eliminar transacción: " . $stmt->error], 500);
            }
            $stmt->close();
        } else {
            sendJsonResponse(["success" => false, "message" => "Acción de eliminación no válida."], 400);
        }
        break;
    
    default:
        sendJsonResponse(["success" => false, "message" => "Método no permitido."], 405);
        break;
}
?>