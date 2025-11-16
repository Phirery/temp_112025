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

    $sql = "SELECT l.maLichKham, l.ngayKham, 
            bn.tenBenhNhan, bn.ngaySinh, bn.gioiTinh,
            c.tenCa
            FROM lichkham l
            JOIN benhnhan bn ON l.maBenhNhan = bn.maBenhNhan
            JOIN calamviec c ON l.maCa = c.maCa
            WHERE l.maBacSi = ? 
            AND l.trangThai = 'Đã đặt'
            AND l.maLichKham NOT IN (SELECT maLichKham FROM hosobenhan WHERE maLichKham IS NOT NULL)
            ORDER BY l.ngayKham DESC, c.gioBatDau DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    $stmt->close();

    echo json_encode(['success' => true, 'data' => $appointments]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>