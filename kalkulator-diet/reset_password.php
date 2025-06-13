<?php
// reset_password.php
session_start();
require 'config.php';

$token = $_GET['token'] ?? '';

if(!$token) {
    die("Token tidak valid.");
}

// Periksa token di database dan cek apakah belum kedaluwarsa
$stmt = $conn->prepare("SELECT id, reset_expiry FROM users WHERE reset_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();
if($stmt->num_rows == 1) {
    $stmt->bind_result($user_id, $reset_expiry);
    $stmt->fetch();

    // Cek apakah token sudah kedaluwarsa
    if (strtotime($reset_expiry) < time()) {
        die("Token sudah kadaluarsa. Silakan ulangi proses reset password.");
    }
} else {
    die("Token tidak valid.");
}
$stmt->close();

// Proses form reset password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if($new_password !== $confirm_password) {
        $error = "Password tidak cocok.";
    } else {
        // Hash password baru dan perbarui di database, lalu hapus token reset
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt_update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
        $stmt_update->bind_param("si", $hashed_password, $user_id);
        if($stmt_update->execute()){
            $_SESSION['message'] = "Password berhasil direset. Silakan login.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Gagal mereset password. Silakan coba lagi.";
        }
        $stmt_update->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Reset Password - HealthyReminder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <h2>Reset Password</h2>
    <?php if(isset($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="" method="POST">
      <div class="mb-3">
        <label for="new_password" class="form-label">Password Baru:</label>
        <input type="password" class="form-control" id="new_password" name="new_password" required>
      </div>
      <div class="mb-3">
        <label for="confirm_password" class="form-label">Konfirmasi Password:</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
      </div>
      <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
  </div>
</body>
</html>
