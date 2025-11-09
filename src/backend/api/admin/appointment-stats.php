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
    'total' => 0,
    'pending' => 0,
    'confirmed' => 0,
    'cancelled' => 0
];

// Tổng số lịch khám
$result = $conn->query("SELECT COUNT(*) as count FROM lichkham");
$stats['total'] = $result->fetch_assoc()['count'];

// Đếm theo trạng thái
$result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE trangThai = 'pending'");
$stats['pending'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE trangThai = 'confirmed'");
$stats['confirmed'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE trangThai = 'cancelled'");
$stats['cancelled'] = $result->fetch_assoc()['count'];

echo json_encode($stats);
$conn->close();
?>