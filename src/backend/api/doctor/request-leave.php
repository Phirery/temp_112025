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

if (!isset($input['ngayNghi']) || !isset($input['lyDo'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
    exit;
}

$ngayNghi = $input['ngayNghi'];
$maCa = $input['maCa'];
$lyDo = $input['lyDo'];

$tomorrow = date('Y-m-d', strtotime('+1 day'));
if ($ngayNghi < $tomorrow) {
    echo json_encode(['success' => false, 'message' => 'Chỉ được xin nghỉ từ ngày mai trở đi']);
    exit;
}

try {
    $conn->begin_transaction();
    
    if ($maCa === null) {
        $stmt = $conn->prepare("
            INSERT INTO ngaynghi (maBacSi, ngayNghi, maCa, lyDo) 
            VALUES (?, ?, 1, ?), (?, ?, 2, ?)
        ");
        $stmt->bind_param("ssisss", $maBacSi, $ngayNghi, $lyDo, $maBacSi, $ngayNghi, $lyDo);
    } else {
        $stmt = $conn->prepare("
            INSERT INTO ngaynghi (maBacSi, ngayNghi, maCa, lyDo) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssis", $maBacSi, $ngayNghi, $maCa, $lyDo);
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Không thể tạo đơn nghỉ phép');
    }
    
    $checkStmt = $conn->prepare("
        SELECT COUNT(*) as count FROM lichkham 
        WHERE maBacSi = ? AND ngayKham = ? AND trangThai = 'Đã đặt'
        " . ($maCa ? " AND maCa = ?" : "")
    );
    
    if ($maCa) {
        $checkStmt->bind_param("ssi", $maBacSi, $ngayNghi, $maCa);
    } else {
        $checkStmt->bind_param("ss", $maBacSi, $ngayNghi);
    }
    
    $checkStmt->execute();
    $count = $checkStmt->get_result()->fetch_assoc()['count'];
    $checkStmt->close();
    
    $conn->commit();
    
    $message = 'Gửi yêu cầu nghỉ phép thành công!';
    if ($count > 0) {
        $message .= " Lưu ý: Có $count lịch khám cần sắp xếp lại.";
    }
    
    echo json_encode(['success' => true, 'message' => $message, 'affectedAppointments' => $count]);
    
    $stmt->close();
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>