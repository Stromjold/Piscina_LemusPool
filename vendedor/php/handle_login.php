<?php
// handle_login.php
// Recibe POST { email, password } y autentica contra la tabla `usuarios`.
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$site = isset($_POST['site']) ? trim($_POST['site']) : null;

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Email y password requeridos.']);
    exit;
}

$mysqli = db();

// Buscar usuario por username
$stmt = $mysqli->prepare('SELECT id, username, password, template_id, is_admin FROM usuarios WHERE username = ? LIMIT 1');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error interno (prep).']);
    exit;
}
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

$success = false;
$adminRedirect = null;

if ($user) {
    $stored = $user['password'];
    // Soportar contraseñas hasheadas (bcrypt) y también plaintext por compatibilidad
    if (is_string($stored) && (strpos($stored, '$2y$') === 0 || strpos($stored, '$2b$') === 0)) {
        $match = password_verify($password, $stored);
    } else {
        $match = ($password === $stored);
    }

    if ($match) {
        $success = true;
        // Guardar sesión
        $_SESSION['al_logged_user'] = $user['username'];
        $_SESSION['al_site'] = $user['template_id'];
        $adminRedirect = 'administracion/admin_' . $user['template_id'] . '.html';
    }
}

// Registrar intento
$payload = json_encode(['posted_email' => $email, 'posted_password' => '***']);
$ins = $mysqli->prepare('INSERT INTO login_attempts (email, ip, user_agent, success, payload) VALUES (?, ?, ?, ?, ?)');
if ($ins) {
    $ins->bind_param('sssis', $email, $ip, $ua, $success_flag, $payload);
    $success_flag = $success ? 1 : 0;
    $ins->execute();
    $ins->close();
}

if ($success) {
    echo json_encode(['success' => true, 'redirect' => $adminRedirect, 'site' => $user['template_id']]);
    exit;
}

// Si no autenticó, devolver false
echo json_encode(['success' => false, 'message' => 'Credenciales inválidas.']);
exit;

?>
