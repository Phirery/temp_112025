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

$maBacSi = 'BS202511090112882';

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['maLichKham'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
    exit;
}

try {
    $stmt = $conn->prepare("
        UPDATE lichkham 
        SET trangThai = 'Hủy'
        WHERE maLichKham = ? AND maBacSi = ? AND trangThai = 'Đã đặt'
    ");
    $stmt->bind_param("is", $input['maLichKham'], $maBacSi);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Hủy lịch thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể hủy hoặc đã hủy']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>