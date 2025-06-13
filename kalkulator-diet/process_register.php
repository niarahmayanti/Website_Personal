<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    // ... validasi ...

    // Cek apakah email sudah terdaftar
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['error_register'] = "Email sudah terdaftar. Gunakan email berbeda atau login.";
        header("Location: register.php");
        exit();
    }
    $stmt->close();

    // Jika lolos, INSERT ...
}

?>
