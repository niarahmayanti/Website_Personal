<?php
// forgot_password_security.php
session_start();
require 'config.php';

$error = '';
$success = '';

// Proses berdasarkan langkah (step)
$step = $_GET['step'] ?? 'email';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Langkah 1: User memasukkan email
    if (isset($_POST['step']) && $_POST['step'] == 'email') {
        $email = trim($_POST['email']);
        $stmt = $conn->prepare("SELECT id, security_question, security_answer FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($user_id, $security_question, $security_answer);
            $stmt->fetch();
            // Simpan data yang diperlukan di session
            $_SESSION['reset_user_id'] = $user_id;
            $_SESSION['reset_security_question'] = $security_question;
            $_SESSION['reset_security_answer'] = $security_answer;
            $_SESSION['reset_email'] = $email;
            header("Location: forgot_password_security.php?step=question");
            exit();
        } else {
            $error = "Email tidak ditemukan.";
        }
        $stmt->close();
    }
    
    // Langkah 2: User menjawab pertanyaan keamanan
    elseif (isset($_POST['step']) && $_POST['step'] == 'verify') {
        $answer = trim($_POST['answer']);
        if (isset($_SESSION['reset_security_answer'])) {
            // Periksa jawaban dengan menggunakan password_verify (asumsi jawaban disimpan sebagai hash)
            if (password_verify($answer, $_SESSION['reset_security_answer'])) {
                header("Location: forgot_password_security.php?step=reset");
                exit();
            } else {
                $error = "Jawaban tidak tepat.";
            }
        } else {
            $error = "Sesi habis. Silakan ulangi proses reset password.";
        }
    }
    
    // Langkah 3: Reset password
    elseif (isset($_POST['step']) && $_POST['step'] == 'reset') {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        if ($new_password !== $confirm_password) {
            $error = "Password tidak cocok.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $user_id = $_SESSION['reset_user_id'];
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);
            if ($stmt->execute()) {
                $success = "Password berhasil direset. Silakan login.";
                // Hapus data sesi reset
                unset($_SESSION['reset_user_id'], $_SESSION['reset_security_question'], $_SESSION['reset_security_answer'], $_SESSION['reset_email']);
            } else {
                $error = "Gagal mereset password. Silakan coba lagi.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password - HealthyReminder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #a8e6cf 0%, #dcedc1 100%);
            min-height: 100vh;
        }
        .reset-card {
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
<body class="d-flex align-items-center">
    <div class="container">
        <div class="reset-card">
            <div class="logo-container">
            <img src="logohealtyreminder.png" alt="HealthyReminder Logo" class="logo">
                <h3 class="mt-3 text-white">Reset Password</h3>
            </div>
            
            <div class="p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?>
                        <a href="login.php" class="btn btn-healthy mt-3">Login</a>
                    </div>
                <?php elseif ($step == 'email'): ?>
                    <!-- Langkah 1: Input Email -->
                    <form action="forgot_password_security.php" method="POST">
                        <input type="hidden" name="step" value="email">
                        <div class="mb-4 input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" class="form-control" 
                                   placeholder="Masukkan email Anda" required>
                        </div>
                        <button type="submit" class="btn btn-healthy w-100">Lanjutkan</button>
                    </form>
                <?php elseif ($step == 'question' && isset($_SESSION['reset_security_question'])): ?>
                    <!-- Langkah 2: Pertanyaan Keamanan -->
                    <form action="forgot_password_security.php" method="POST">
                        <input type="hidden" name="step" value="verify">
                        <div class="mb-4">
                            <div class="alert alert-info">
                                <i class="fas fa-question-circle me-2"></i>
                                <strong>Pertanyaan Keamanan:</strong><br>
                                <?php echo $_SESSION['reset_security_question']; ?>
                            </div>
                        </div>
                        <div class="mb-4 input-icon">
                            <i class="fas fa-comment-dots"></i>
                            <input type="text" name="answer" class="form-control" 
                                   placeholder="Masukkan jawaban Anda" required>
                        </div>
                        <button type="submit" class="btn btn-healthy w-100">Verifikasi</button>
                    </form>
                <?php elseif ($step == 'reset'): ?>
                    <!-- Langkah 3: Reset Password -->
                    <form action="forgot_password_security.php" method="POST">
                        <input type="hidden" name="step" value="reset">
                        <div class="mb-4 input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="new_password" class="form-control" 
                                   id="new_password" placeholder="Password Baru" required>
                            <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('new_password')"></i>
                        </div>
                        <div class="mb-4 input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="confirm_password" class="form-control" 
                                   id="confirm_password" placeholder="Konfirmasi Password" required>
                            <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('confirm_password')"></i>
                        </div>
                        <button type="submit" class="btn btn-healthy w-100">Reset Password</button>
                    </form>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <a href="login.php" class="text-success fw-bold text-decoration-none">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Login
                    </a>
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
