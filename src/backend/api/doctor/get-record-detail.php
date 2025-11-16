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

$maHoSo = $_GET['maHoSo'] ?? '';

if (!$maHoSo) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã hồ sơ']);
    exit;
}

try {
    $sql = "SELECT h.maHoSo, h.ngayTao, h.ngayHoanThanh, h.chanDoan, h.dieuTri, h.trangThai,
            bn.tenBenhNhan, bn.ngaySinh, bn.gioiTinh,
            l.ngayKham, c.tenCa
            FROM hosobenhan h
            JOIN benhnhan bn ON h.maBenhNhan = bn.maBenhNhan
            LEFT JOIN lichkham l ON h.maLichKham = l.maLichKham
            LEFT JOIN calamviec c ON l.maCa = c.maCa
            WHERE h.maHoSo = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maHoSo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy hồ sơ']);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>