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
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

try {
    $stmt = $conn->prepare("
        SELECT 
            lk.maLichKham,
            lk.ngayKham,
            lk.trangThai,
            bn.tenBenhNhan,
            bn.ngaySinh,
            bn.gioiTinh,
            ca.tenCa,
            ca.maCa,
            sk.gioBatDau,
            sk.gioKetThuc,
            gk.tenGoi
        FROM lichkham lk
        JOIN benhnhan bn ON lk.maBenhNhan = bn.maBenhNhan
        JOIN calamviec ca ON lk.maCa = ca.maCa
        JOIN suatkham sk ON lk.maSuat = sk.maSuat
        JOIN goikham gk ON lk.maGoi = gk.maGoi
        WHERE lk.maBacSi = ? AND lk.ngayKham = ?
        ORDER BY ca.maCa, sk.gioBatDau
    ");
    $stmt->bind_param("ss", $maBacSi, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $appointments
    ]);
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>