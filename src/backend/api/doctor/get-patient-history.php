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

if (!isset($_GET['maBenhNhan'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bệnh nhân']);
    exit;
}

$maBenhNhan = $_GET['maBenhNhan'];

try {
    $stmt = $conn->prepare("
        SELECT 
            lk.ngayKham,
            lk.trangThai,
            ca.tenCa,
            gk.tenGoi,
            lk.ghiChu
        FROM lichkham lk
        JOIN calamviec ca ON lk.maCa = ca.maCa
        JOIN goikham gk ON lk.maGoi = gk.maGoi
        WHERE lk.maBacSi = ? AND lk.maBenhNhan = ?
        ORDER BY lk.ngayKham DESC
    ");
    $stmt->bind_param("ss", $maBacSi, $maBenhNhan);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $history
    ]);
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>