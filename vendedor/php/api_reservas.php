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

switch ($method) {
    case 'GET':
        $month = $_GET['month'] ?? null;
        $year = $_GET['year'] ?? null;

        if ($month && $year) {
            $sql = "SELECT r.id, r.cliente_id, c.nombre as nombre_cliente, r.fecha_inicio, r.dias_estancia, r.cantidad_personas FROM reservas r JOIN clientes c ON r.cliente_id = c.id WHERE r.template_id = ? AND MONTH(r.fecha_inicio) = ? AND YEAR(r.fecha_inicio) = ? ORDER BY r.fecha_inicio DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $template_id, $month, $year);
        } else {
            $sql = "SELECT r.id, r.cliente_id, c.nombre as nombre_cliente, r.fecha_inicio, r.dias_estancia, r.cantidad_personas FROM reservas r JOIN clientes c ON r.cliente_id = c.id WHERE r.template_id = ? ORDER BY r.fecha_inicio DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $template_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();

        $reservas = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $reservas[] = $row;
            }
        }
        sendJsonResponse(["success" => true, "reservas" => $reservas]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $reserva_id = $data['id'] ?? null;
        $cliente_id = filter_var($data['cliente_id'] ?? 0, FILTER_VALIDATE_INT);
        $fecha_inicio = $data['fecha_inicio'] ?? '';
        $cantidad_personas = filter_var($data['cantidad_personas'] ?? 0, FILTER_VALIDATE_INT);
        $dias_estancia = filter_var($data['dias_estancia'] ?? 0, FILTER_VALIDATE_INT);

        if ($cliente_id === false || $cliente_id <= 0 || empty($fecha_inicio) || $cantidad_personas === false || $cantidad_personas <= 0 || $dias_estancia === false || $dias_estancia <= 0) {
            sendJsonResponse(["success" => false, "message" => "Datos de reserva inválidos o incompletos."], 400);
        }

        if ($reserva_id) { // ACTUALIZAR
            $sql = "UPDATE reservas SET cliente_id=?, fecha_inicio=?, dias_estancia=?, cantidad_personas=? WHERE id=? AND template_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isiiii", $cliente_id, $fecha_inicio, $dias_estancia, $cantidad_personas, $reserva_id, $template_id);
            $message = "Reserva actualizada con éxito.";
        } else { // CREAR
            $sql = "INSERT INTO reservas (template_id, cliente_id, fecha_inicio, dias_estancia, cantidad_personas) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisii", $template_id, $cliente_id, $fecha_inicio, $dias_estancia, $cantidad_personas);
            $message = "Reserva creada con éxito.";
        }

        if ($stmt->execute()) {
            sendJsonResponse(["success" => true, "message" => $message, "new_id" => $reserva_id ? $reserva_id : $conn->insert_id]);
        } else {
            sendJsonResponse(["success" => false, "message" => "Error en la base de datos al procesar la reserva."], 500);
        }
        $stmt->close();
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        $reserva_id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);

        if ($reserva_id === false || $reserva_id <= 0) {
            sendJsonResponse(["success" => false, "message" => "ID de reserva inválido."], 400);
        }

        $stmt = $conn->prepare("DELETE FROM reservas WHERE id = ? AND template_id = ?");
        $stmt->bind_param("ii", $reserva_id, $template_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                sendJsonResponse(["success" => true, "message" => "Reserva eliminada con éxito."]);
            } else {
                sendJsonResponse(["success" => false, "message" => "No se encontró la reserva o no pertenece a esta plantilla."]);
            }
        } else {
            sendJsonResponse(["success" => false, "message" => "Error en la base de datos al eliminar la reserva."], 500);
        }
        $stmt->close();
        break;

    default:
        sendJsonResponse(["success" => false, "message" => "Método no permitido."], 405);
        break;
}
?>