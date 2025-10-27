<?php
// api/api_reservas.php
require 'db_connect.php'; 

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $month = $_GET['month'] ?? null;
        $year = $_GET['year'] ?? null;

        if ($month && $year) {
            $sql = "SELECT id, cliente_id, nombre_cliente, fecha_inicio, dias_estancia, cantidad_personas FROM reservas WHERE MONTH(fecha_inicio) = ? AND YEAR(fecha_inicio) = ? ORDER BY fecha_inicio DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $month, $year);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $sql = "SELECT id, cliente_id, nombre_cliente, fecha_inicio, dias_estancia, cantidad_personas FROM reservas ORDER BY fecha_inicio DESC";
            $result = $conn->query($sql);
        }

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
        $cliente_id = filter_var($data['cliente_id'] ?? '', FILTER_SANITIZE_STRING);
        $nombre_cliente = filter_var($data['nombre_cliente'] ?? '', FILTER_SANITIZE_STRING);
        $fecha_inicio = $data['fecha_inicio'] ?? '';
        $cantidad_personas = filter_var($data['cantidad_personas'] ?? 0, FILTER_VALIDATE_INT);
        $dias_estancia = filter_var($data['dias_estancia'] ?? 0, FILTER_VALIDATE_INT);

        if (empty($cliente_id) || empty($nombre_cliente) || empty($fecha_inicio) || $cantidad_personas === false || $cantidad_personas <= 0 || $dias_estancia === false || $dias_estancia <= 0) {
            sendJsonResponse(["success" => false, "message" => "Datos de reserva inválidos o incompletos."], 400);
        }

        if ($reserva_id) { // ACTUALIZAR
            $sql = "UPDATE reservas SET cliente_id=?, nombre_cliente=?, fecha_inicio=?, dias_estancia=?, cantidad_personas=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiii", $cliente_id, $nombre_cliente, $fecha_inicio, $dias_estancia, $cantidad_personas, $reserva_id);
            $message = "Reserva actualizada con éxito.";
        } else { // CREAR
            $sql = "INSERT INTO reservas (cliente_id, nombre_cliente, fecha_inicio, dias_estancia, cantidad_personas) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssii", $cliente_id, $nombre_cliente, $fecha_inicio, $dias_estancia, $cantidad_personas);
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

        $stmt = $conn->prepare("DELETE FROM reservas WHERE id = ?");
        $stmt->bind_param("i", $reserva_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                sendJsonResponse(["success" => true, "message" => "Reserva eliminada con éxito."]);
            } else {
                sendJsonResponse(["success" => false, "message" => "No se encontró la reserva o ya fue eliminada."]);
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