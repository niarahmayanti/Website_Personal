<?php
// history.php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
require 'config.php';

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM history WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$activities = array();
while($row = $result->fetch_assoc()){
    $activities[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Riwayat Aktivitas - HealthyReminder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
    <img src="logohealtyreminder.png" style="height: 40px; margin-right: 10px;">
        HealthyReminder
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
              aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.php">Beranda</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  
  <div class="container py-5">
    <h2 class="mb-4">Riwayat Aktivitas</h2>
    <?php if(empty($activities)): ?>
      <div class="alert alert-info">Belum ada aktivitas yang tercatat.</div>
    <?php else: ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Tipe</th>
            <th>Detail</th>
            <th>Waktu</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($activities as $activity): ?>
          <tr>
            <td><?php echo htmlspecialchars($activity['type']); ?></td>
            <td><?php echo htmlspecialchars($activity['details']); ?></td>
            <td><?php echo htmlspecialchars($activity['created_at']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
