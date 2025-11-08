<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datlichkham";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

$stats = [
    'total' => 0,
    'patients' => 0,
    'doctors' => 0,
    'admins' => 0,
];

// Lấy tổng số tài khoản người dùng
$result = $conn->query("SELECT COUNT(*) as total FROM nguoidung");
if ($result) {
    $stats['total'] = $result->fetch_assoc()['total'];}

// Lấy tổng số tài khoản bệnh nhân
$result = $conn->query("SELECT COUNT(*) as total FROM nguoidung WHERE vaiTro = 'benhnhan'");
if ($result) {
    $stats['patients'] = $result->fetch_assoc()['total'];}

// Lấy tổng số tài khoản bác sĩ
$result = $conn->query("SELECT COUNT(*) as total FROM nguoidung WHERE vaiTro = 'bacsi'");
if ($result) {
    $stats['doctors'] = $result->fetch_assoc()['total'];}

// Lấy tổng số tài khoản quản trị viên
$result = $conn->query("SELECT COUNT(*) as total FROM nguoidung WHERE vaiTro = 'quantri'");
if ($result) {
    $stats['admins'] = $result->fetch_assoc()['total'];}

echo json_encode(array_merge(['success' => true], $stats));

$conn->close();
?>
