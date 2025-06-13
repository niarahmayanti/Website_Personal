<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}
require 'config.php';

// Menghapus akun jika admin mengklik tombol hapus
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM users WHERE id = $delete_id");
    header("Location: admin_manage_accounts.php");
    exit();
}

// Mengubah peran pengguna
if (isset($_POST['update_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['role'];
    $conn->query("UPDATE users SET role = '$new_role' WHERE id = $user_id");
    header("Location: admin_manage_accounts.php");
    exit();
}

// Mengambil daftar akun pengguna
$query = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kelola Akun - Admin HealthyReminder</title>
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
    .table-container {
      background: rgba(255, 255, 255, 0.95);
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
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
    <h1>Kelola Akun Pengguna</h1>
    <p>Admin dapat melihat, menghapus, dan mengubah peran akun pengguna di bawah ini.</p>
    <div class="table-container">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>No</th>
            <th>Username</th>
            <th>Email</th>
            <th>Peran</th>
            <th>Tanggal Dibuat</th>
            <th>Aksi</th>
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
                  echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                  echo "<td>
                          <form method='POST' style='display:inline-block;'>
                            <input type='hidden' name='user_id' value='" . $row['id'] . "'>
                            <select name='role' class='form-select' style='width:auto; display:inline-block;'>
                              <option value='user' " . ($row['role'] == 'user' ? 'selected' : '') . ">User</option>
                              <option value='admin' " . ($row['role'] == 'admin' ? 'selected' : '') . ">Admin</option>
                            </select>
                            <button type='submit' name='update_role' class='btn btn-success btn-sm'><i class='fas fa-save'></i></button>
                          </form>
                        </td>";
                  echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                  echo "<td>
                          <a href='?delete_id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin menghapus akun ini?\");'><i class='fas fa-trash'></i> Hapus</a>
                        </td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='6' class='text-center'>Tidak ada akun yang tersedia.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
