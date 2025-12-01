<?php
// save_submission.php
// Guarda cualquier envío de formulario en `form_submissions` como JSON
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

$page = isset($_POST['page']) ? trim($_POST['page']) : (isset($_SERVER['HTTP_REFERER']) ? basename($_SERVER['HTTP_REFERER']) : 'unknown');
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

$payload = [];
foreach ($_POST as $k => $v) {
    if ($k === 'page') continue;
    $payload[$k] = $v;
}

$json = json_encode($payload, JSON_UNESCAPED_UNICODE);

$mysqli = db();
$stmt = $mysqli->prepare('INSERT INTO form_submissions (page, data, ip) VALUES (?, ?, ?)');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error interno al preparar.']);
    exit;
}
$stmt->bind_param('sss', $page, $json, $ip);
$ok = $stmt->execute();
$stmt->close();

if ($ok) echo json_encode(['success' => true]); else echo json_encode(['success' => false, 'message' => 'Fallo al guardar.']);

?>
