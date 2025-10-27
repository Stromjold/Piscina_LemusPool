<?php
// api/api_finanzas.php
require 'db_connect.php'; 

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        if ($action === 'reportes') {
            $sql_totales = "SELECT 
                COALESCE(SUM(CASE WHEN tipo = 'ingreso' THEN total ELSE 0 END), 0) AS total_ingresos,
                COALESCE(SUM(CASE WHEN tipo = 'gasto' THEN total ELSE 0 END), 0) AS total_gastos
                FROM transacciones";
            $result_totales = $conn->query($sql_totales);
            $totales = $result_totales->fetch_assoc();
            // Asegurarse de que sean valores numéricos
            $totales['total_ingresos'] = isset($totales['total_ingresos']) ? (float)$totales['total_ingresos'] : 0.0;
            $totales['total_gastos'] = isset($totales['total_gastos']) ? (float)$totales['total_gastos'] : 0.0;

            $sql_categorias = "SELECT categoria, tipo, SUM(total) as monto_total 
                            FROM transacciones 
                            GROUP BY categoria, tipo 
                            ORDER BY tipo, categoria";
            $result_categorias = $conn->query($sql_categorias);
            $categorias = [];
            while($row = $result_categorias->fetch_assoc()) {
                $row['monto_total'] = isset($row['monto_total']) ? (float)$row['monto_total'] : 0.0;
                $categorias[] = $row;
            }

            $sql_historial = "SELECT id, fecha, descripcion, cantidad, precio, categoria, tipo, total FROM transacciones ORDER BY fecha DESC, id DESC";
            $result_historial = $conn->query($sql_historial);
            $historial = [];
            while($row = $result_historial->fetch_assoc()) {
                $row['cantidad'] = isset($row['cantidad']) ? (int)$row['cantidad'] : 0;
                $row['precio'] = isset($row['precio']) ? (float)$row['precio'] : 0.0;
                $row['total'] = isset($row['total']) ? (float)$row['total'] : 0.0;
                $historial[] = $row;
            }

            $balance = $totales['total_ingresos'] - $totales['total_gastos'];

            sendJsonResponse([
                "success" => true,
                "totales" => $totales,
                "categorias" => $categorias,
                "historial" => $historial,
                "balance" => $balance
            ]);
        } else {
            sendJsonResponse(["success" => false, "message" => "Acción GET no válida."], 400);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        
        $fecha = $data['fecha'] ?? '';
        $descripcion = filter_var($data['descripcion'] ?? '', FILTER_SANITIZE_STRING);
        $cantidad = filter_var($data['cantidad'] ?? 1, FILTER_VALIDATE_INT);
        $precio = filter_var($data['precio'] ?? 0.00, FILTER_VALIDATE_FLOAT);
        $categoria = filter_var($data['categoria'] ?? '', FILTER_SANITIZE_STRING);
        $tipo = $data['tipo'] ?? '';
        
        if (empty($fecha) || empty($descripcion) || empty($categoria) || !in_array($tipo, ['ingreso', 'gasto']) || $cantidad === false || $cantidad <= 0 || $precio === false || $precio <= 0) {
            sendJsonResponse(["success" => false, "message" => "Datos de transacción inválidos o incompletos."], 400);
        }

        $total = ($cantidad * $precio);

        $sql = "INSERT INTO transacciones (fecha, descripcion, cantidad, precio, categoria, tipo, total) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssd", $fecha, $descripcion, $cantidad, $precio, $categoria, $tipo, $total);

        if ($stmt->execute()) {
            sendJsonResponse(["success" => true, "message" => "Transacción registrada con éxito."]);
        } else {
            sendJsonResponse(["success" => false, "message" => "Error en la base de datos al registrar la transacción."], 500);
        }
        $stmt->close();
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        $transaccion_id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
        $action_delete = $data['action'] ?? null;

        if ($action_delete === 'limpiar_historial') {
            $sql = "TRUNCATE TABLE transacciones"; 
            if ($conn->query($sql)) {
                sendJsonResponse(["success" => true, "message" => "Historial de transacciones limpiado con éxito."]);
            } else {
                sendJsonResponse(["success" => false, "message" => "Error en la base de datos al limpiar el historial."], 500);
            }
        } else if ($transaccion_id !== false && $transaccion_id > 0) {
            $stmt = $conn->prepare("DELETE FROM transacciones WHERE id = ?");
            $stmt->bind_param("i", $transaccion_id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    sendJsonResponse(["success" => true, "message" => "Transacción eliminada con éxito."]);
                } else {
                    sendJsonResponse(["success" => false, "message" => "No se encontró la transacción o ya fue eliminada."]);
                }
            } else {
                sendJsonResponse(["success" => false, "message" => "Error en la base de datos al eliminar la transacción."], 500);
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