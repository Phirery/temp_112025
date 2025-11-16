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

$idNguoiDung = 5;

try {
    $stmt = $conn->prepare("SELECT maBacSi FROM bacsi WHERE nguoiDungId = ?");
    $stmt->bind_param("i", $idNguoiDung);
    $stmt->execute();
    $maBacSi = $stmt->get_result()->fetch_assoc()['maBacSi'] ?? null;
    $stmt->close();

    if (!$maBacSi) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy bác sĩ']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM thongbao WHERE maBacSi = ? AND daXem = 1");
    $stmt->bind_param("s", $maBacSi);
    
    if ($stmt->execute()) {
        $deleted = $stmt->affected_rows;
        echo json_encode(['success' => true, 'deleted' => $deleted]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Xóa thất bại']);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>