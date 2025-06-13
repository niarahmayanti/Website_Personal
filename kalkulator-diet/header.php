<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthyReminder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
      background: linear-gradient(135deg, #a8e6cf 0%, #dcedc1 100%);
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
    .card {
      border-radius: 15px;
      border: none;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    .card-header {
      background: #7cd6b6 !important;
      color: white !important;
      font-weight: 600;
      border-radius: 15px 15px 0 0 !important;
    }
    .btn-healthy {
      background-color: #4abd8c;
      color: white;
      transition: all 0.3s ease;
      padding: 10px 25px;
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

<nav class="navbar navbar-expand-lg navbar-light shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="logohealtyreminder.png" alt="Logo" style="height: 40px; margin-right: 10px;">
            HealthyCare        </a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">Tentang</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Kontak</a></li>
                <li class="nav-item"><a class="nav-link" href="privacy_policy.php">Privacy Policy</a></li>
                <li class="nav-item"><a class="nav-link" href="terms_of_service.php">Terms of Service</a></li>
            </ul>
        </div>
    </div>
</nav>
