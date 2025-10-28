<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $template_name = $_POST['template'];

    if (empty($username) || empty($password) || empty($template_name)) {
        // Redirect back with an error if any field is empty
        $redirect_url = "../login.html?error=1" . (!empty($template_name) ? "&template=" . urlencode($template_name) : "");
        header("Location: " . $redirect_url);
        exit();
    }

    // 1. Find the template_id from the template name
    $stmt_template = $conn->prepare("SELECT id FROM templates WHERE nombre = ?");
    $stmt_template->bind_param("s", $template_name);
    $stmt_template->execute();
    $result_template = $stmt_template->get_result();

    if ($result_template->num_rows == 1) {
        $template = $result_template->fetch_assoc();
        $template_id = $template['id'];

        // 2. Authenticate the user against the usuarios table using template_id and username
        $stmt_user = $conn->prepare("SELECT id, username, password FROM usuarios WHERE username = ? AND template_id = ?");
        $stmt_user->bind_param("si", $username, $template_id);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();

        if ($result_user->num_rows == 1) {
            $user = $result_user->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // 3. Store user_id and template_id in the session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['template_id'] = $template_id;
                $_SESSION['template_name'] = $template_name; // Also store name for convenience
                
                header("Location: ../admin.php");
                exit();
            }
        }
    }

    // If anything fails, redirect back to the login page with an error
    $redirect_url = "../login.html?error=1" . (!empty($template_name) ? "&template=" . urlencode($template_name) : "");
    header("Location: " . $redirect_url);
    exit();
}
?>