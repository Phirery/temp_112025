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

try {
    $stmt = $conn->prepare("
        SELECT 
            bn.maBenhNhan,
            bn.tenBenhNhan,
            bn.ngaySinh,
            bn.gioiTinh,
            bn.soTheBHYT,
            nd.soDienThoai,
            COUNT(lk.maLichKham) as soLanKham,
            MAX(lk.ngayKham) as lanKhamGanNhat
        FROM benhnhan bn
        JOIN nguoidung nd ON bn.nguoiDungId = nd.id
        JOIN lichkham lk ON bn.maBenhNhan = lk.maBenhNhan
        WHERE lk.maBacSi = ?
        GROUP BY bn.maBenhNhan, bn.tenBenhNhan, bn.ngaySinh, bn.gioiTinh, bn.soTheBHYT, nd.soDienThoai
        ORDER BY lanKhamGanNhat DESC
    ");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $patients = [];
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $patients
    ]);
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>