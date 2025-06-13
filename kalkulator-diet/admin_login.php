<?php
// admin_login.php
session_start();
require 'config.php';

$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if(empty($email) || empty($password)){
        $errors[] = "Email dan Password harus diisi.";
    } else {
        // Ambil data user dengan role 'admin'
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ? AND role = 'admin'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows == 1){
            $stmt->bind_result($user_id, $username, $hashed_password, $role);
            $stmt->fetch();
            if(password_verify($password, $hashed_password)){
                // Simpan data ke session
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $errors[] = "Password salah.";
            }
        } else {
            $errors[] = "Akun admin dengan email tersebut tidak ditemukan.";
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
  <title>Login Admin - HealthyReminder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <h2>Login Admin</h2>
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach($errors as $error) {
            echo "<p>$error</p>";
        } ?>
      </div>
    <?php endif; ?>
    <form action="admin_login.php" method="POST">
      <div class="mb-3">
          <label for="email" class="form-label">Email:</label>
          <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <div class="mb-3">
          <label for="password" class="form-label">Password:</label>
          <input type="password" class="form-control" id="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary">Login Admin</button>
    </form>
    <p class="mt-3">Bukan admin? <a href="login.php">Login sebagai User</a></p>
  </div>
</body>
</html>
