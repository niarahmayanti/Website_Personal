<?php
// index.php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
require 'config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kalkulator Diet & Atur Pengingat - HealthyReminder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      background: linear-gradient(135deg,rgb(234, 112, 187) 0%,rgb(239, 144, 203) 100%);
      min-height: 100vh;
    }
    .navbar {
      background: rgba(255, 255, 255, 0.95) !important;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    }
    .navbar-brand {
      color:rgb(243, 138, 225) !important;
      font-weight: 700;
      font-size: 1.5rem;
    }
    .nav-link {
      color: #6c757d !important;
      transition: all 0.3s ease;
    }
    .nav-link:hover {
      color:rgb(189, 74, 162) !important;
    }
    .card {
      border-radius: 15px;
      border: none;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    .card-header {
      background:rgb(214, 124, 191) !important;
      color: white !important;
      font-weight: 600;
      border-radius: 15px 15px 0 0 !important;
    }
    .btn-healthy {
      background-color:rgb(189, 74, 179);
      color: white;
      transition: all 0.3s ease;
      padding: 10px 25px;
    }
    .btn-healthy:hover {
      background-color:rgb(225, 154, 210);
      transform: translateY(-2px);
    }
    .form-control:focus {
      border-color:rgb(214, 124, 184);
      box-shadow: 0 0 0 0.25rem rgba(124, 214, 182, 0.25);
    }
    .clock {
      font-size: 2rem;
      font-weight: 600;
      color:rgb(189, 74, 166);
      padding: 15px;
      background: rgba(255, 255, 255, 0.9);
      border-radius: 10px;
      display: inline-block;
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
  </style>
</head>
<body>
  <!-- Navbar -->
 <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <img src="logohealtyreminder.png" style="height: 40px; margin-right: 10px;">
        HealthyCare
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
              aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="index.php">Beranda</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="about.php">Tentang</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="contact.php">Kontak</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="privacy_policy.php">Privacy Policy</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="terms_of_service.php">Terms of Service</a>
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
    <h1 class="text-center mb-4">Kalkulator Diet & Atur Pengingat</h1>
    <div class="row">
      <!-- Kalkulator Diet -->
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            Kalkulator Diet
          </div>
          <div class="card-body">
            <form id="dietForm">
              <div class="mb-3">
                <label for="age" class="form-label">Usia (tahun)</label>
                <input type="number" class="form-control" id="age" placeholder="Masukkan usia Anda" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Jenis Kelamin</label>
                <div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="male" value="male" required>
                    <label class="form-check-label" for="male">Laki-laki</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="female" value="female" required>
                    <label class="form-check-label" for="female">Perempuan</label>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label for="weight" class="form-label">Berat Badan (kg)</label>
                <input type="number" class="form-control" id="weight" placeholder="Masukkan berat badan Anda" required>
              </div>
              <div class="mb-3">
                <label for="height" class="form-label">Tinggi Badan (cm)</label>
                <input type="number" class="form-control" id="height" placeholder="Masukkan tinggi badan Anda" required>
              </div>
              <div class="mb-3">
                <label for="activity" class="form-label">Tingkat Aktivitas</label>
                <select class="form-select" id="activity" required>
                  <option value="">Pilih tingkat aktivitas</option>
                  <option value="1.2">Sedentari (minim aktivitas)</option>
                  <option value="1.375">Ringan (olahraga ringan 1-3 hari/minggu)</option>
                  <option value="1.55">Sedang (olahraga 3-5 hari/minggu)</option>
                  <option value="1.725">Berat (olahraga 6-7 hari/minggu)</option>
                  <option value="1.9">Sangat berat (pekerjaan fisik berat atau 2x latihan sehari)</option>
                </select>
              </div>
              <button type="submit" class="btn btn-success">Hitung Kalori Harian</button>
            </form>
            <div id="dietResult" class="mt-3"></div>
          </div>
        </div>
      </div>
      <!-- Atur Pengingat -->
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-secondary text-white">
            Atur Pengingat
          </div>
          <div class="card-body">
            <div class="text-center mb-4">
              <div id="clock" class="clock">00:00:00</div>
            </div>
            <form id="alarmForm" class="mb-3">
              <div class="mb-2">
                <label for="reminderType" class="form-label">Jenis Pengingat</label>
                <select class="form-select" id="reminderType" required>
                  <option value="">Pilih jenis pengingat</option>
                  <option value="Makan">Makan</option>
                  <option value="Minum">Minum</option>
                  <option value="Olahraga">Olahraga</option>
                  <option value="Olahraga">Sahur</option>
                </select>
              </div>
              <div class="mb-2">
                <label for="alarmTime" class="form-label">Waktu</label>
                <input type="time" class="form-control" id="alarmTime" required>
              </div>
              <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Set Pengingat</button>
                <button type="button" id="clearAlarm" class="btn btn-danger">Clear Pengingat</button>
              </div>
            </form>
            <div id="alarmStatus" class="mt-3 text-center"></div>
          </div>
        </div>
      </div>
    </div>
    <!-- Link ke halaman riwayat -->
    <div class="mt-5 text-center">
      <a href="history.php" class="btn btn-info">Lihat Riwayat Aktivitas</a>
    </div>
  </div>

  <!-- Audio Element untuk Suara Pengingat -->
  <audio id="alarmSound" src="https://www.soundjay.com/button/beep-07.mp3" preload="auto"></audio>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom JS -->
  <script src="script.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>