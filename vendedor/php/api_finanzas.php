<?php
session_start();
require 'db_connect.php'; 

// Ensure the user is logged in and has a template assigned
if (!isset($_SESSION['user_id']) || !isset($_SESSION['template_id'])) {
    sendJsonResponse(["success" => false, "message" => "No autorizado."], 401);
    exit;
}
$template_id = $_SESSION['template_id'];

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        if ($action === 'reportes') {
            $sql_totales = "SELECT 
                COALESCE(SUM(CASE WHEN tipo = 'ingreso' THEN total ELSE 0 END), 0) AS total_ingresos,
                COALESCE(SUM(CASE WHEN tipo = 'gasto' THEN total ELSE 0 END), 0) AS total_gastos
                FROM transacciones WHERE template_id = ?";
            $stmt_totales = $conn->prepare($sql_totales);
            $stmt_totales->bind_param("i", $template_id);
            $stmt_totales->execute();
            $result_totales = $stmt_totales->get_result();
            $totales = $result_totales->fetch_assoc();
            $totales['total_ingresos'] = (float)($totales['total_ingresos'] ?? 0.0);
            $totales['total_gastos'] = (float)($totales['total_gastos'] ?? 0.0);

            $sql_categorias = "SELECT categoria, tipo, SUM(total) as monto_total 
                            FROM transacciones 
                            WHERE template_id = ?
                            GROUP BY categoria, tipo 
                            ORDER BY tipo, categoria";
            $stmt_categorias = $conn->prepare($sql_categorias);
            $stmt_categorias->bind_param("i", $template_id);
            $stmt_categorias->execute();
            $result_categorias = $stmt_categorias->get_result();
            $categorias = [];
            while($row = $result_categorias->fetch_assoc()) {
                $row['monto_total'] = (float)($row['monto_total'] ?? 0.0);
                $categorias[] = $row;
            }

            $sql_historial = "SELECT id, fecha, descripcion, cantidad, precio, categoria, tipo, total FROM transacciones WHERE template_id = ? ORDER BY fecha DESC, id DESC";
            $stmt_historial = $conn->prepare($sql_historial);
            $stmt_historial->bind_param("i", $template_id);
            $stmt_historial->execute();
            $result_historial = $stmt_historial->get_result();
            $historial = [];
            while($row = $result_historial->fetch_assoc()) {
                $row['cantidad'] = (int)($row['cantidad'] ?? 0);
                $row['precio'] = (float)($row['precio'] ?? 0.0);
                $row['total'] = (float)($row['total'] ?? 0.0);
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

        $sql = "INSERT INTO transacciones (template_id, fecha, descripcion, cantidad, precio, categoria, tipo, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ississsd", $template_id, $fecha, $descripcion, $cantidad, $precio, $categoria, $tipo, $total);

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
            $stmt = $conn->prepare("DELETE FROM transacciones WHERE template_id = ?");
            $stmt->bind_param("i", $template_id);
            if ($stmt->execute()) {
                sendJsonResponse(["success" => true, "message" => "Historial de transacciones limpiado con éxito."]);
            } else {
                sendJsonResponse(["success" => false, "message" => "Error en la base de datos al limpiar el historial."], 500);
            }
        } else if ($transaccion_id !== false && $transaccion_id > 0) {
            $stmt = $conn->prepare("DELETE FROM transacciones WHERE id = ? AND template_id = ?");
            $stmt->bind_param("ii", $transaccion_id, $template_id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    sendJsonResponse(["success" => true, "message" => "Transacción eliminada con éxito."]);
                } else {
                    sendJsonResponse(["success" => false, "message" => "No se encontró la transacción o no pertenece a esta plantilla."]);
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