<?php
// login.php
session_start();
require 'config.php';

$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $errors[] = "Email dan Password harus diisi.";
    } else {
        // Perbarui query untuk mengambil kolom role
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($user_id, $username, $hashed_password, $role);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                // Simpan data user ke session termasuk role
                $_SESSION['user_id']   = $user_id;
                $_SESSION['username']  = $username;
                $_SESSION['role']      = $role;
                
                // Redirect berdasarkan role
                if ($role === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $errors[] = "Password salah.";
            }
        } else {
            $errors[] = "Email tidak ditemukan.";
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
  <title>Login - HealthyReminder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      background: linear-gradient(135deg, #a8e6cf 0%, #dcedc1 100%);
      min-height: 100vh;
    }
    .login-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }
    .logo-container {
      text-align: center;
      padding: 2rem;
      background-color: #7cd6b6;
    }
    .logo {
      width: 120px;
      height: auto;
    }
    .btn-healthy {
      background-color: #4abd8c;
      color: white;
      transition: all 0.3s ease;
    }
    .btn-healthy:hover {
      background-color: #3da578;
      transform: translateY(-2px);
    }
    .form-control:focus {
      border-color: #7cd6b6;
      box-shadow: 0 0 0 0.25rem rgba(124, 214, 182, 0.25);
    }
  </style>
</head>
<body class="d-flex align-items-center">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="login-card">
          <div class="logo-container">
            <img src="logohealtyreminder.png" alt="HealthyReminder Logo" class="logo">
            <h2 class="mt-3 text-white">HealthyCare</h2>
          </div>
          <div class="p-4">
            <h4 class="mb-4 text-center text-secondary">Selamat Datang Kembali</h4>
            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger">
                <?php foreach ($errors as $error) {
                    echo "<p>$error</p>";
                } ?>
              </div>
            <?php endif; ?>
            <form action="login.php" method="POST">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-envelope text-secondary"></i></span>
                  <input type="email" class="form-control" id="email" name="email" placeholder="email@contoh.com" required>
                </div>
              </div>
              <div class="mb-4">
    <label for="password" class="form-label">Password</label>
    <div class="input-group">
      <span class="input-group-text"><i class="fas fa-lock text-secondary"></i></span>
      <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
      <button type="button" class="btn password-toggle" id="togglePassword">
        <i class="fas fa-eye-slash"></i>
      </button>
    </div>
  </div>
              <button type="submit" class="btn btn-healthy w-100 py-2 fw-bold">MASUK</button>
            </form>
            <div class="text-center mt-4">
              <a href="register.php" class="text-decoration-none text-secondary">Belum punya akun? <span class="text-success fw-bold">Daftar Sekarang</span></a>
              <br>
              <a href="forgot_password_security.php" class="text-decoration-none text-secondary small">Lupa Password?<span class="text-success fw-bold">Reset Password</span></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const icon = this.querySelector('i');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      } else {
        passwordInput.type = 'password';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>