<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recoger y sanear los datos del formulario
    $name = filter_var(trim($_POST["name"]), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $template = filter_var(trim($_POST["template"]), FILTER_SANITIZE_STRING);
    $message = filter_var(trim($_POST["message"]), FILTER_SANITIZE_STRING);

    // 2. Validar que los campos no estén vacíos y que el email sea válido
    if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($message)) {
        http_response_code(400);
        echo "Por favor, completa todos los campos del formulario.";
        exit;
    }

    // 3. Construir el correo electrónico
    $recipient = "tu_correo@ejemplo.com"; // <-- IMPORTANTE: Reemplaza esto con tu correo real
    $subject = "Nueva solicitud de sitio web para '$template' de $name";

    $email_content = "Has recibido una nueva solicitud de contacto:\n\n";
    $email_content .= "Nombre: $name\n";
    $email_content .= "Correo Electrónico: $email\n";
    $email_content .= "Plantilla de Interés: $template\n\n";
    $email_content .= "Mensaje:\n$message\n";

    $email_headers = "From: $name <$email>";

    // 4. Enviar el correo
    // Nota: Para que mail() funcione, tu servidor (XAMPP) debe tener un servidor de correo (SMTP) configurado.
    // Abre tu archivo 'php.ini' y busca [mail function] para configurarlo.
    if (mail($recipient, $subject, $email_content, $email_headers)) {
        http_response_code(200);
        echo "¡Gracias! Tu mensaje ha sido enviado. Nos pondremos en contacto contigo pronto.";
    } else {
        http_response_code(500);
        echo "Hubo un problema al enviar tu mensaje. Por favor, inténtalo de nuevo más tarde.";
    }

} else {
    http_response_code(403);
    echo "Hubo un problema con tu solicitud, por favor intenta de nuevo.";
}
?>