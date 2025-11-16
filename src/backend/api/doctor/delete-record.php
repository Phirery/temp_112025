<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$conn = new mysqli("localhost", "root", "", "datlichkham");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$maHoSo = $input['maHoSo'] ?? '';

if (!$maHoSo) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã hồ sơ']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM hosobenhan WHERE maHoSo = ?");
    $stmt->bind_param("s", $maHoSo);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Xóa thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Xóa thất bại']);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>