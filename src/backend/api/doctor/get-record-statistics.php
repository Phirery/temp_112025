<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

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

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM hosobenhan WHERE maBacSi = ?");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $totalRecords = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM hosobenhan WHERE maBacSi = ? AND trangThai = 'Chưa hoàn thành'");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $pendingRecords = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM hosobenhan WHERE maBacSi = ? AND trangThai = 'Đã hoàn thành'");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $completedRecords = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM hosobenhan WHERE maBacSi = ? AND DATE(ngayTao) = CURDATE()");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $todayRecords = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    echo json_encode([
        'success' => true,
        'data' => [
            'totalRecords' => (int)$totalRecords,
            'pendingRecords' => (int)$pendingRecords,
            'completedRecords' => (int)$completedRecords,
            'todayRecords' => (int)$todayRecords
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>