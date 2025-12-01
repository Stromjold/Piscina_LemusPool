<?php
// send_mail.php
// Endpoint que maneja el formulario de contacto de la página principal.
// Guarda en la tabla `solicitudes` y también guarda el POST completo en `form_submissions`.

require_once __DIR__ . '/db.php';

$mysqli = db();

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$template = isset($_POST['template']) ? trim($_POST['template']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Simple validation
if (!$name || !$email || !$message) {
    header('Location: ../pagina_principal.html?sent=0');
    exit;
}

// Insert into solicitudes
$stmt = $mysqli->prepare('INSERT INTO solicitudes (nombre, email, plantilla_interes, mensaje) VALUES (?, ?, ?, ?)');
if ($stmt) {
    $stmt->bind_param('ssss', $name, $email, $template, $message);
    $ok1 = $stmt->execute();
    $stmt->close();
} else {
    $ok1 = false;
}

// Also save full POST as JSON in form_submissions for auditing
$payload = [];
foreach ($_POST as $k => $v) {
    $payload[$k] = $v;
}
$json = json_encode($payload, JSON_UNESCAPED_UNICODE);
$stmt2 = $mysqli->prepare('INSERT INTO form_submissions (page, data, ip) VALUES (?, ?, ?)');
$page = 'pagina_principal_contacto';
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
if ($stmt2) {
    $stmt2->bind_param('sss', $page, $json, $ip);
    $ok2 = $stmt2->execute();
    $stmt2->close();
} else {
    $ok2 = false;
}

// Redirect back to the public page with a query param indicating status
if ($ok1 || $ok2) {
    header('Location: ../pagina_principal.html?sent=1');
    exit;
} else {
    header('Location: ../pagina_principal.html?sent=0');
    exit;
}

?>
