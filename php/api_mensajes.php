<?php
// api/api_mensajes.php
require 'db_connect.php'; 

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // OBTENER todos los mensajes
        $sql = "SELECT id, nombre, email, telefono, mensaje, leido, fecha_envio FROM mensajes ORDER BY fecha_envio DESC";
        $result = $conn->query($sql);
        $mensajes = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $mensajes[] = $row;
            }
        }
        sendJsonResponse(["success" => true, "mensajes" => $mensajes]);
        break;

    case 'POST':
        // GUARDAR un nuevo mensaje (desde Home_Page.html)
        $data = json_decode(file_get_contents("php://input"), true);
        
        $nombre = filter_var($data['name'] ?? '', FILTER_SANITIZE_STRING);
        $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $telefono = filter_var($data['telefono'] ?? 'No proporcionado', FILTER_SANITIZE_STRING);
        $mensaje_texto = filter_var($data['message'] ?? '', FILTER_SANITIZE_STRING);

        if (empty($nombre) || empty($email) || $email === false || empty($mensaje_texto)) {
            sendJsonResponse(["success" => false, "message" => "Datos de mensaje inválidos o incompletos."], 400);
        }

        $sql = "INSERT INTO mensajes (nombre, email, telefono, mensaje) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $email, $telefono, $mensaje_texto);

        if ($stmt->execute()) {
            sendJsonResponse(["success" => true, "message" => "Mensaje enviado con éxito."]);
        } else {
            sendJsonResponse(["success" => false, "message" => "Error en la base de datos al enviar el mensaje."], 500);
        }
        $stmt->close();
        break;

    case 'DELETE':
        // ELIMINAR un mensaje
        $data = json_decode(file_get_contents("php://input"), true);
        $mensaje_id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);

        if ($mensaje_id === false || $mensaje_id <= 0) {
            sendJsonResponse(["success" => false, "message" => "ID de mensaje inválido."], 400);
        }

        $stmt = $conn->prepare("DELETE FROM mensajes WHERE id = ?");
        $stmt->bind_param("i", $mensaje_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                sendJsonResponse(["success" => true, "message" => "Mensaje eliminado con éxito."]);
            } else {
                sendJsonResponse(["success" => false, "message" => "No se encontró el mensaje o ya fue eliminado."]);
            }
        } else {
            sendJsonResponse(["success" => false, "message" => "Error en la base de datos al eliminar el mensaje."], 500);
        }
        $stmt->close();
        break;

    default:
        sendJsonResponse(["success" => false, "message" => "Método no permitido."], 405);
        break;
}
?>