<?php
// process_forgot.php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    // Cek apakah email ada di database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows == 1) {
        $stmt->bind_result($user_id);
        $stmt->fetch();

        // Generate token unik dan waktu kedaluwarsa (misalnya, 1 jam)
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Simpan token dan expiry di database
        $stmt_update = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE id = ?");
        $stmt_update->bind_param("ssi", $token, $expiry, $user_id);
        $stmt_update->execute();
        $stmt_update->close();

        // Buat link reset password
        $resetLink = "http://localhost/healthy_reminder/reset_password.php?token=".$token;
        $subject = "Reset Password HealthyReminder";
        $message = "Halo,\n\nAnda telah meminta untuk mereset password Anda. Silakan klik link berikut untuk mereset password:\n\n$resetLink\n\nLink ini akan kadaluarsa dalam 1 jam.\n\nJika Anda tidak melakukan permintaan ini, abaikan email ini.";
        $headers = "From: no-reply@healthyreminder.com";

        // Kirim email (pastikan konfigurasi mail server sudah benar)
        if(mail($email, $subject, $message, $headers)) {
            $_SESSION['message'] = "Link reset password telah dikirim ke email Anda.";
        } else {
            $_SESSION['message'] = "Gagal mengirim email. Silakan coba lagi.";
        }
    } else {
        $_SESSION['message'] = "Email tidak ditemukan.";
    }
    $stmt->close();
    header("Location: forgot_password.php");
    exit();
} else {
    header("Location: forgot_password.php");
    exit();
}
