<?php
session_start();
require 'db_connect.php'; 

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // This is for the admin panel, so it requires authentication
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['template_id'])) {
            sendJsonResponse(["success" => false, "message" => "No autorizado."], 401);
            exit;
        }
        $template_id = $_SESSION['template_id'];

        $sql = "SELECT id, nombre, email, telefono, mensaje, leido, fecha_envio FROM mensajes WHERE template_id = ? ORDER BY fecha_envio DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $template_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $mensajes = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $mensajes[] = $row;
            }
        }
        sendJsonResponse(["success" => true, "mensajes" => $mensajes]);
        break;

    case 'POST':
        // This is for the public contact form, no auth needed
        $data = json_decode(file_get_contents("php://input"), true);
        
        $nombre = filter_var($data['name'] ?? '', FILTER_SANITIZE_STRING);
        $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $telefono = filter_var($data['telefono'] ?? 'No proporcionado', FILTER_SANITIZE_STRING);
        $mensaje_texto = filter_var($data['message'] ?? '', FILTER_SANITIZE_STRING);
        $template_id = filter_var($data['template_id'] ?? 0, FILTER_VALIDATE_INT); // Expecting template_id from the form

        if (empty($nombre) || empty($email) || $email === false || empty($mensaje_texto) || $template_id <= 0) {
            sendJsonResponse(["success" => false, "message" => "Datos de mensaje inválidos o incompletos."], 400);
        }

        $sql = "INSERT INTO mensajes (template_id, nombre, email, telefono, mensaje) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $template_id, $nombre, $email, $telefono, $mensaje_texto);

        if ($stmt->execute()) {
            sendJsonResponse(["success" => true, "message" => "Mensaje enviado con éxito."]);
        } else {
            sendJsonResponse(["success" => false, "message" => "Error en la base de datos al enviar el mensaje."], 500);
        }
        $stmt->close();
        break;

    case 'DELETE':
        // This is for the admin panel, requires authentication
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['template_id'])) {
            sendJsonResponse(["success" => false, "message" => "No autorizado."], 401);
            exit;
        }
        $template_id = $_SESSION['template_id'];

        $data = json_decode(file_get_contents("php://input"), true);
        $mensaje_id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);

        if ($mensaje_id === false || $mensaje_id <= 0) {
            sendJsonResponse(["success" => false, "message" => "ID de mensaje inválido."], 400);
        }

        $stmt = $conn->prepare("DELETE FROM mensajes WHERE id = ? AND template_id = ?");
        $stmt->bind_param("ii", $mensaje_id, $template_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                sendJsonResponse(["success" => true, "message" => "Mensaje eliminado con éxito."]);
            } else {
                sendJsonResponse(["success" => false, "message" => "No se encontró el mensaje o no pertenece a esta plantilla."]);
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