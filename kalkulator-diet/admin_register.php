<?php
// admin_register.php
session_start();
require 'config.php';

$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username         = trim($_POST['username']);
    $email            = trim($_POST['email']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi input
    if(empty($username)) {
        $errors[] = "Username tidak boleh kosong.";
    }
    if(empty($email)) {
        $errors[] = "Email tidak boleh kosong.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }
    if(empty($password)) {
        $errors[] = "Password tidak boleh kosong.";
    }
    if($password != $confirm_password) {
        $errors[] = "Password tidak cocok.";
    }

    // Cek apakah email sudah terdaftar
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Email sudah terdaftar.";
    }
    $stmt->close();

    // Jika tidak ada error, simpan data dengan role admin
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        if ($stmt->execute()) {
            $_SESSION['user_id']   = $stmt->insert_id;
            $_SESSION['username']  = $username;
            $_SESSION['role']      = 'admin';
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $errors[] = "Registrasi gagal, coba lagi.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrasi Admin - HealthyReminder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <h2>Registrasi Admin</h2>
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach($errors as $error): ?>
          <p><?php echo $error; ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <form action="admin_register.php" method="POST">
      <div class="mb-3">
          <label for="username" class="form-label">Username:</label>
          <input type="text" class="form-control" id="username" name="username" required>
      </div>
      <div class="mb-3">
          <label for="email" class="form-label">Email:</label>
          <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <div class="mb-3">
          <label for="password" class="form-label">Password:</label>
          <input type="password" class="form-control" id="password" name="password" required>
      </div>
      <div class="mb-3">
          <label for="confirm_password" class="form-label">Konfirmasi Password:</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
      </div>
      <button type="submit" class="btn btn-primary">Registrasi Admin</button>
    </form>
    <p class="mt-3">Bukan admin? <a href="login.php">Login sebagai User</a></p>
  </div>
</body>
</html>
