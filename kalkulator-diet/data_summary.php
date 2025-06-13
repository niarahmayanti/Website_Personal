<?php
require 'config.php'; // Sertakan koneksi database

// Query untuk total pengguna
$query_users = "SELECT COUNT(*) as total_users FROM users";
$result_users = mysqli_query($conn, $query_users);
$total_users = mysqli_fetch_assoc($result_users)['total_users'];

// Query untuk total laporan (menghitung semua entri di history)
$query_reports = "SELECT COUNT(*) as total_reports FROM history";
$result_reports = mysqli_query($conn, $query_reports);
$total_reports = mysqli_fetch_assoc($result_reports)['total_reports'];

// Format data sebagai JSON
$data = [
    'total_users' => $total_users,
    'total_reports' => $total_reports
];

// Set header untuk JSON
header('Content-Type: application/json');
echo json_encode($data);
?>