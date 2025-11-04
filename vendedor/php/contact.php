<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    $to = "owner@example.com";
    $subject = "New Contact Form Submission";
    $body = "Name: $name\nEmail: $email\nPhone: $phone\nMessage: $message";
    $headers = "From: $email";

    if (mail($to, $subject, $body, $headers)) {
        header("Location: ../Home_Page.html?success=1#contacto");
    } else {
        header("Location: ../Home_Page.html?error=1#contacto");
    }
}
?>