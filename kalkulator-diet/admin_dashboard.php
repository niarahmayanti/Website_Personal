<?php
// Mulai sesi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah pengguna sudah login dan memiliki role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Sertakan file konfigurasi database
require 'config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - HealthyReminder</title>
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
            <a class="navbar-brand" href="#">
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
    
    <!-- Konten Dashboard -->
    <div class="container mt-4">
        <h1>Dashboard Admin</h1>
        <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        
        <div class="row mt-5">
            <!-- Sidebar Menu -->
            <div class="col-lg-3">
                <div class="admin-card p-4">
                    <h5 class="mb-4 text-secondary"><i class="fas fa-bars me-2"></i>Menu Admin</h5>
                    <div class="list-group">
                        <a href="admin_manage_accounts.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-cog me-2"></i>Kelola Akun
                        </a>
                        <a href="admin_reports.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-line me-2"></i>Lihat Laporan
                        </a>
                        <a href="admin_user_data.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-users me-2"></i>Kelola Data Pengguna
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Konten Utama Dashboard -->
            <div class="col-lg-9">
                <div class="admin-card p-4">
                    <h3 class="mb-4">Ringkasan Sistem</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Pengguna</h5>
                                    <p class="card-text" id="totalUsers">0</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card text-white bg-warning mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Laporan</h5>
                                    <p class="card-text" id="totalReports">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>Create with by HNDK MEDIA PROJECT</footer>

    <script>
    function updateSummary() {
        fetch('data_summary.php')
            .then(response => response.json())
            .then(data => {
                console.log('Data dari server:', data); // Debugging
                document.getElementById('totalUsers').innerText = data.total_users;
                document.getElementById('totalReports').innerText = data.total_reports;
            })
            .catch(error => console.error('Error:', error));
    }

    updateSummary();
    setInterval(updateSummary, 5000);
    </script>

</body>
</html>