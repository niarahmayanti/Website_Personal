<?php
// admin.php
session_start();
require 'config.php';

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Proses penghapusan akun jika parameter delete_id ada di URL
if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];
    
    // Jangan izinkan admin menghapus akun sendiri
    if ($delete_id === $_SESSION['user_id']) {
        $errorMsg = "Tidak dapat menghapus akun sendiri.";
    } else {
        // Hapus data history yang terkait dengan user terlebih dahulu
        $stmt = $conn->prepare("DELETE FROM history WHERE user_id = ?");
        if (!$stmt) {
            $errorMsg = "Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param("i", $delete_id);
            $stmt->execute();
            $stmt->close();
        }

        // Setelah data history dihapus, baru hapus data user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        if (!$stmt) {
            $errorMsg = "Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param("i", $delete_id);
            if ($stmt->execute()) {
                $successMsg = "Akun dengan ID $delete_id berhasil dihapus.";
            } else {
                $errorMsg = "Gagal menghapus akun: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    header("Location: admin.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kelola Akun User - HealthyReminder Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="admin_dashboard.php">HealthyReminder - Admin</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
              aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
              <li class="nav-item">
                  <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
              </li>
          </ul>
      </div>
    </div>
  </nav>
  
  <!-- Konten Utama -->
  <div class="container py-5">
    <h2>Kelola Akun User</h2>
    
    <!-- Tampilkan pesan error atau sukses -->
    <?php if (isset($errorMsg)): ?>
      <div class="alert alert-danger">
        <?php echo $errorMsg; ?>
      </div>
    <?php endif; ?>
    <?php if (isset($successMsg)): ?>
      <div class="alert alert-success">
        <?php echo $successMsg; ?>
      </div>
    <?php endif; ?>

    <?php
      // Ambil semua data user
      $sql = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
      $result = $conn->query($sql);
    ?>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Dibuat</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo htmlspecialchars($row['username']); ?></td>
              <td><?php echo htmlspecialchars($row['email']); ?></td>
              <td><?php echo htmlspecialchars($row['role']); ?></td>
              <td><?php echo $row['created_at']; ?></td>
              <td>
                <?php if ($row['id'] === $_SESSION['user_id']): ?>
                  <span class="text-muted">Tidak dapat menghapus diri sendiri</span>
                <?php else: ?>
                  <a href="admin.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus akun ini?')">Hapus</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center">Belum ada akun yang terdaftar.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
