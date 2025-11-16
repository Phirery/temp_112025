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

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['maLichKham'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã lịch khám']);
    exit;
}

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

    // Cập nhật trạng thái
    $stmt = $conn->prepare("
        UPDATE lichkham 
        SET trangThai = 'Hoàn thành'
        WHERE maLichKham = ? AND maBacSi = ? AND trangThai = 'Đã đặt'
    ");
    $stmt->bind_param("is", $input['maLichKham'], $maBacSi);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật hoặc lịch đã hoàn thành']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>