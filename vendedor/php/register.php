<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($username) < 20) {
        header("Location: ../register.html?error=username_length");
        exit();
    }

    if ($password !== $confirm_password) {
        header("Location: ../register.html?error=password");
        exit();
    }

    $template_name = $_POST['template'];

    // 1. Obtener el ID de la plantilla a partir del nombre
    $sql_template = "SELECT id FROM templates WHERE nombre = ?";
    $stmt_template = $conn->prepare($sql_template);
    $stmt_template->bind_param("s", $template_name);
    $stmt_template->execute();
    $result_template = $stmt_template->get_result();

    if ($result_template->num_rows === 0) {
        // Si la plantilla no existe, es un error.
        header("Location: ../register.html?error=dberror");
        exit();
    }
    $template_id = $result_template->fetch_assoc()['id'];

    // 2. Verificar si el usuario ya existe para esa plantilla
    $sql = "SELECT id FROM usuarios WHERE username = ? AND template_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $username, $template_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: ../register.html?error=username&template=" . urlencode($template_name));
        exit();
    }

    // 3. Insertar el nuevo usuario con el template_id
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (template_id, username, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $template_id, $username, $hashed_password);

    if ($stmt->execute()) {
        header("Location: ../login.html?success=1");
        exit();
    } else {
        header("Location: ../register.html?error=dberror");
        exit();
    }
}
?>