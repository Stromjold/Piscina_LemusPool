<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $telefono = $_POST['telefono'] ?? null;

    if (strlen($nombre) < 20) {
        header('Location: ../register_cliente.html?error=nombre_length');
        exit;
    }

    if (empty($nombre) || empty($email) || empty($password)) {
        // Optional: Add user-facing error message
        header('Location: ../register_cliente.html?error=missing_fields');
        exit;
    }

    // For client registration, we'll assume a default template_id, e.g., 1 for the main site.
    // This is a critical fix as the `template_id` is a NOT NULL foreign key.
    $template_id = 1;

    // Check if email already exists for this template
    $stmt_check = $conn->prepare("SELECT id FROM clientes WHERE email = ? AND template_id = ?");
    $stmt_check->bind_param("si", $email, $template_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        header('Location: ../register_cliente.html?error=email_exists');
        exit;
    }

    // Hash password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO clientes (template_id, nombre, email, password, telefono) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $template_id, $nombre, $email, $hashed_password, $telefono);

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