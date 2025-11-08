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
    'internal' => 0,
    'surgery' => 0,
    'other' => 0
];

// Tổng số bác sĩ
$result = $conn->query("SELECT COUNT(*) as count FROM bacsi");
$stats['total'] = $result->fetch_assoc()['count'];

// Đếm bác sĩ khoa Nội (INT1025)
$result = $conn->query("
    SELECT COUNT(*) as count 
    FROM bacsi bs
    JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
    WHERE ck.maKhoa = 'INT1025'
");
$stats['internal'] = $result->fetch_assoc()['count'];

// Đếm bác sĩ khoa Ngoại (SUR1025)
$result = $conn->query("
    SELECT COUNT(*) as count 
    FROM bacsi bs
    JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
    WHERE ck.maKhoa = 'SUR1025'
");
$stats['surgery'] = $result->fetch_assoc()['count'];

// Đếm bác sĩ các khoa khác
$stats['other'] = $stats['total'] - $stats['internal'] - $stats['surgery'];

echo json_encode($stats);
$conn->close();
?>