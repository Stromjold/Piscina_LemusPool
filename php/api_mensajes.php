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
        
        $nombre = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $telefono = $data['telefono'] ?? 'No proporcionado';
        $mensaje_texto = $data['message'] ?? '';

        if (empty($nombre) || empty($email) || empty($mensaje_texto)) {
            sendJsonResponse(["success" => false, "message" => "Faltan datos obligatorios para el mensaje."], 400);
        }

        $sql = "INSERT INTO mensajes (nombre, email, telefono, mensaje) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $email, $telefono, $mensaje_texto);

        if ($stmt->execute()) {
            sendJsonResponse(["success" => true, "message" => "Mensaje enviado con éxito."]);
        } else {
            sendJsonResponse(["success" => false, "message" => "Error al enviar mensaje: " . $stmt->error], 500);
        }
        $stmt->close();
        break;

    case 'DELETE':
        // ELIMINAR un mensaje
        $data = json_decode(file_get_contents("php://input"), true);
        $mensaje_id = $data['id'] ?? null;

        if (!$mensaje_id) {
            sendJsonResponse(["success" => false, "message" => "ID de mensaje no proporcionado."], 400);
        }

        $stmt = $conn->prepare("DELETE FROM mensajes WHERE id = ?");
        $stmt->bind_param("i", $mensaje_id);

        if ($stmt->execute()) {
            sendJsonResponse(["success" => true, "message" => "Mensaje eliminado con éxito."]);
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