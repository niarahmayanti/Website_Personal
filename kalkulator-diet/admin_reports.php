<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}
require 'config.php';

// Query untuk mengambil laporan aktivitas, beserta username dari tabel users
$query = "SELECT h.id, h.type, h.details, h.created_at, u.username 
          FROM history h 
          LEFT JOIN users u ON h.user_id = u.id 
          ORDER BY h.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laporan Aktivitas - HealthyReminder Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      background: linear-gradient(135deg, #e3f2ed 0%, #f5f9e1 100%);
      min-height: 100vh;
    }
    .navbar {
      background: rgba(255, 255, 255, 0.95) !important;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    }
    .navbar-brand {
      color: #4abd8c !important;
      font-weight: 700;
      font-size: 1.5rem;
    }
    .nav-link {
      color: #6c757d !important;
      transition: all 0.3s ease;
    }
    .nav-link:hover {
      color: #4abd8c !important;
    }
    .admin-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      border: none;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="admin_dashboard.php">
        <img src="logohealtyreminder.png" alt="Logo" style="height: 40px; margin-right: 10px;">
        HealthyReminder <span class="badge bg-danger ms-2">Admin</span>
      </a>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  
  <!-- Konten Utama -->
  <div class="container mt-4">
    <h1>Laporan Aktivitas</h1>
    <p>Berikut adalah laporan aktivitas sistem HealthyReminder.</p>
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>No</th>
            <th>User</th>
            <th>Tipe</th>
            <th>Detail</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result && $result->num_rows > 0) {
              $no = 1;
              while ($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>" . $no++ . "</td>";
                  echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['details']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='5' class='text-center'>Belum ada laporan.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
