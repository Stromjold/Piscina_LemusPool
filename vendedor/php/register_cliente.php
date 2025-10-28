<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $telefono = $_POST['telefono'] ?? null;

    if (empty($nombre) || empty($email) || empty($password)) {
        // Optional: Add user-facing error message
        header('Location: ../register_cliente.html?error=missing_fields');
        exit;
    }

    // Hash password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO clientes (nombre, email, password, telefono) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $email, $hashed_password, $telefono);

    if ($stmt->execute()) {
        // Redirect to login page with a success message
        header('Location: ../login_cliente.html?success=1');
        exit;
    } else {
        // Handle potential errors, like a duplicate email
        // Optional: Add more specific error handling
        header('Location: ../register_cliente.html?error=registration_failed');
        exit;
    }
}
?>