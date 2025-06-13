<?php
// save_activity.php
session_start();
require 'config.php';

if(!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $user_id = $_SESSION['user_id'];
    $type = $_POST['type'] ?? '';
    $details = $_POST['details'] ?? '';
    
    $stmt = $conn->prepare("INSERT INTO history (user_id, type, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $type, $details);
    if($stmt->execute()){
        echo "Success";
    } else {
        echo "Error";
    }
    $stmt->close();
} else {
    echo "Invalid request";
}
?>
