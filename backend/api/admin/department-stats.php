<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Kết nối database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datlichkham";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed']);
    exit;
}

$stats = [
    'departments' => 0,
    'specialties' => 0,
    'doctors' => 0
];

// Tổng số khoa
$result = $conn->query("SELECT COUNT(*) as count FROM khoa");
if ($result) {
    $stats['departments'] = $result->fetch_assoc()['count'];
}

// Tổng số chuyên khoa
$result = $conn->query("SELECT COUNT(*) as count FROM chuyenkhoa");
if ($result) {
    $stats['specialties'] = $result->fetch_assoc()['count'];
}

// Tổng số bác sĩ
$result = $conn->query("SELECT COUNT(*) as count FROM bacsi");
if ($result) {
    $stats['doctors'] = $result->fetch_assoc()['count'];
}

echo json_encode($stats);
$conn->close();
?>