<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$conn = new mysqli("localhost", "root", "", "datlichkham");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Kết nối thất bại:((']);
    exit;
}

// ===== Khi có login thì mở lại đoạn này =====
/*
session_start();
if (!isset($_SESSION['id']) || $_SESSION['vaiTro'] !== 'bacsi') {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập hoặc không phải bác sĩ']);
    exit;
}
$idNguoiDung = $_SESSION['id'];
*/

// ===== Gán tạm ID bác sĩ để test =====
$idNguoiDung = 5;

try {
    // Lấy mã bác sĩ
    $stmt = $conn->prepare("SELECT maBacSi FROM bacsi WHERE nguoiDungId = ?");
    $stmt->bind_param("i", $idNguoiDung);
    $stmt->execute();
    $maBacSi = $stmt->get_result()->fetch_assoc()['maBacSi'] ?? null;
    $stmt->close();

    if (!$maBacSi) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy bác sĩ']);
        exit;
    }

    // Tổng số bệnh nhân
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT maBenhNhan) AS total FROM lichkham WHERE maBacSi = ?");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $totalPatients = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    // Lịch hẹn hôm nay
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM lichkham WHERE maBacSi = ? AND ngayKham = CURDATE()");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $todayCount = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    // Hoàn thành
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM lichkham WHERE maBacSi = ? AND trangThai = 'Hoàn thành'");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $completedCount = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    // Đã đặt
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM lichkham WHERE maBacSi = ? AND trangThai = 'Đã đặt'");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $pendingCount = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    echo json_encode([
        'success' => true,
        'data' => [
            'totalPatients' => (int)$totalPatients,
            'todayCount' => (int)$todayCount,
            'completedCount' => (int)$completedCount,
            'pendingCount' => (int)$pendingCount
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>