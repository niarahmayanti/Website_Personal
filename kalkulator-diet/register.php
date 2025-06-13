<?php
session_start();
require 'config.php';

$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // validasi ...
  if ($adaMasalah) {
    $errors[] = "Pesan error";
}
    // Ambil dan sanitasi data dari form
    $username           = trim($_POST['username']);
    $email              = trim($_POST['email']);
    $password           = $_POST['password'];
    $confirm_password   = $_POST['confirm_password'];
    $security_question  = $_POST['security_question'];
    $security_answer    = $_POST['security_answer'];

    // Validasi input
    if (empty($username)) {
        $errors[] = "Username tidak boleh kosong.";
    }
    if (empty($email)) {
        $errors[] = "Email tidak boleh kosong.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }
    if (empty($password)) {
        $errors[] = "Password tidak boleh kosong.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Password tidak cocok.";
    }
    if (empty($security_question) || empty($security_answer)) {
        $errors[] = "Pertanyaan dan jawaban keamanan harus diisi.";
    }

    // Cek apakah email sudah terdaftar
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Email sudah terdaftar. Silahkan ke halaman login atau gunakan email yang berbeda.";
        header("Location: register.php");
    }
    $stmt->close();

    // Jika ada error, tampilkan pesan dan hentikan proses
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
        exit();
    }

    // Jika tidak ada error, lanjutkan dengan penyimpanan data
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $hashed_security_answer = password_hash($security_answer, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, security_question, security_answer) VALUES (?, ?, ?, 'user', ?, ?)");
    $stmt->bind_param("sssss", $username, $email, $hashed_password, $security_question, $hashed_security_answer);
    if ($stmt->execute()) {
        // Akun berhasil dibuat
        $_SESSION['message'] = "Akun berhasil dibuat. Silahkan login.";
        header("Location: login.php");
        exit();
    } else {
        echo "<p style='color:red;'>Akun gagal dibuat, silahkan coba lagi.</p>";
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrasi - HealthyReminder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      background: linear-gradient(135deg, #a8e6cf 0%, #dcedc1 100%);
      min-height: 100vh;
    }
    .register-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      max-width: 500px;
      margin: auto;
    }
    .logo-container {
      text-align: center;
      padding: 2rem;
      background-color: #7cd6b6;
      border-radius: 20px 20px 0 0;
    }
    .logo {
      width: 100px;
      height: auto;
    }
    .btn-healthy {
      background-color: #4abd8c;
      color: white;
      transition: all 0.3s ease;
      padding: 12px 30px;
    }
    .btn-healthy:hover {
      background-color: #3da578;
      transform: translateY(-2px);
    }
    .form-control:focus {
      border-color: #7cd6b6;
      box-shadow: 0 0 0 0.25rem rgba(124, 214, 182, 0.25);
    }
    .input-icon {
      position: relative;
    }
    .input-icon i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
    }
    .input-icon input {
      padding-left: 40px;
    }
    .password-toggle {
      cursor: pointer;
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
    }
  </style>
</head>
<body
class="d-flex align-items-center">
  <div class="container">
    <div class="register-card">
      <div class="logo-container">
        <img src="logohealtyreminder.png" alt="HealthyReminder Logo" class="logo">
        <h3 class="mt-3 text-white">Daftar Akun Baru</h3>
      </div>
      
      <div class="p-4">
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <?php foreach($errors as $error) {
                echo "<p>$error</p>";
            } ?>
          </div>
        <?php endif; ?>
        <?php
    // Tampilkan error dari session jika ada
    if (isset($_SESSION['error_register'])) {
      echo "<p style='color:red;'>".$_SESSION['error_register']."</p>";
      unset($_SESSION['error_register']);
    }
  ?>
        
        <form action="register.php" method="POST">
          <div class="mb-3 input-icon">
            <i class="fas fa-user"></i>
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
          </div>
          
          <div class="mb-3 input-icon">
            <i class="fas fa-envelope"></i>
            <input type="email" class="form-control" id="email" name="email" placeholder="email@contoh.com" required>
          </div>
          
          <div class="mb-3 input-icon">
            <i class="fas fa-lock"></i>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('password')"></i>
          </div>
          
          <div class="mb-4 input-icon">
            <i class="fas fa-lock"></i>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
            <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('confirm_password')"></i>
          </div>
          <!-- Pada file register.php -->
<div class="mb-3">
  <label for="security_question" class="form-label">Pertanyaan Keamanan:</label>
  <select class="form-select" id="security_question" name="security_question" required>
    <option value="">Pilih pertanyaan keamanan</option>
    <option value="Apa nama hewan peliharaan Anda?">Apa nama hewan peliharaan Anda?</option>
    <option value="Apa Makanan Favorit Anda?">Apa Makanan Favorit Anda?</option>
    <option value="Apa warna kesukaan Anda">Apa warna kesukaan Anda?</option>
    <option value="Apa hobi Anda?">Apa hobi Anda?</option>
  </select>
</div>
<div class="mb-3">
  <label for="security_answer" class="form-label">Jawaban Keamanan:</label>
  <input type="text" class="form-control" id="security_answer" name="security_answer" required>
</div>

          
          <button type="submit" class="btn btn-healthy w-100">Daftar Sekarang</button>
        </form>
        
        <div class="text-center mt-4">
          <p class="text-secondary">Sudah punya akun? 
            <a href="login.php" class="text-success fw-bold text-decoration-none">Masuk di sini</a>
          </p>
        </div>
      </div>
    </div>
  </div>

  <script>
    function togglePassword(fieldId) {
      const passwordField = document.getElementById(fieldId);
      const icon = passwordField.nextElementSibling;
      
      if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      } else {
        passwordField.type = 'password';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      }
    }
  </script>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>