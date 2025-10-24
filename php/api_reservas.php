<?php
// api/api_reservas.php
require 'db_connect.php'; 

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $sql = "SELECT id, cliente_id, nombre_cliente, fecha_inicio, dias_estancia, cantidad_personas FROM reservas ORDER BY fecha_inicio DESC";
        $result = $conn->query($sql);
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
        $cliente_id = $data['cliente_id'] ?? $data['id'] ?? '';
        $nombre_cliente = $data['nombre'] ?? $data['nombre_cliente'] ?? '';
        $fecha_inicio = $data['fecha_inicio'] ?? '';
        $cantidad_personas = $data['personas'] ?? $data['cantidad_personas'] ?? 0;
        $dias_estancia = $data['dias'] ?? $data['dias_estancia'] ?? 0;

        if (empty($cliente_id) || empty($nombre_cliente) || empty($fecha_inicio) || $cantidad_personas <= 0 || $dias_estancia <= 0) {
            sendJsonResponse(["success" => false, "message" => "Faltan datos obligatorios para la reserva."], 400);
        }

        if ($reserva_id) { // ACTUALIZAR
            $sql = "UPDATE reservas SET cliente_id=?, nombre_cliente=?, fecha_inicio=?, dias_estancia=?, cantidad_personas=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiii", $cliente_id, $nombre_cliente, $fecha_inicio, $dias_estancia, $cantidad_personas, $reserva_id);
            $message = "Reserva actualizada con éxito.";
        } else { // CREAR
            $sql = "INSERT INTO reservas (cliente_id, nombre_cliente, fecha_inicio, dias_estancia, cantidad_personas) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $cliente_id, $nombre_cliente, $fecha_inicio, $dias_estancia, $cantidad_personas);
            $message = "Reserva creada con éxito.";
        }

        if ($stmt->execute()) {
            sendJsonResponse(["success" => true, "message" => $message, "new_id" => $reserva_id ? $reserva_id : $conn->insert_id]);
        } else {
            sendJsonResponse(["success" => false, "message" => "Error en la operación de reserva: " . $stmt->error], 500);
        }
        $stmt->close();
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        $reserva_id = $data['id'] ?? null;

        if (!$reserva_id) {
            sendJsonResponse(["success" => false, "message" => "ID de reserva no proporcionado."], 400);
        }

        $stmt = $conn->prepare("DELETE FROM reservas WHERE id = ?");
        $stmt->bind_param("i", $reserva_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                sendJsonResponse(["success" => true, "message" => "Reserva eliminada con éxito."]);
            } else {
                sendJsonResponse(["success" => false, "message" => "No se encontró la reserva."]);
            }
        } else {
            sendJsonResponse(["success" => false, "message" => "Error al eliminar: " . $stmt->error], 500);
        }
        $stmt->close();
        break;

    default:
        sendJsonResponse(["success" => false, "message" => "Método no permitido."], 405);
        break;
}
?>