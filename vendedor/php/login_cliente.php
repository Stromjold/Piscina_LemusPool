<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header('Location: ../login_cliente.html?error=missing_fields');
        exit;
    }

    $stmt = $conn->prepare("SELECT id, nombre, password FROM clientes WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $cliente = $result->fetch_assoc();
        if (password_verify($password, $cliente['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            $_SESSION['cliente_id'] = $cliente['id'];
            $_SESSION['cliente_nombre'] = $cliente['nombre'];
            
            // Redirect to the client's dashboard
            header('Location: ../cliente_dashboard.html');
            exit;
        }
    }

    // If login fails for any reason (user not found, wrong password)
    header('Location: ../login_cliente.html?error=invalid_credentials');
    exit;
}
?>