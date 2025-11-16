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

    $sql = "SELECT h.maHoSo, h.ngayTao, h.chanDoan, h.dieuTri,
            bn.tenBenhNhan, bn.ngaySinh, bn.gioiTinh,
            l.ngayKham, c.tenCa
            FROM hosobenhan h
            JOIN benhnhan bn ON h.maBenhNhan = bn.maBenhNhan
            JOIN lichkham l ON h.maLichKham = l.maLichKham
            JOIN calamviec c ON l.maCa = c.maCa
            WHERE h.maBacSi = ? AND h.trangThai = 'Chưa hoàn thành'
            ORDER BY h.ngayTao DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    $stmt->close();

    echo json_encode(['success' => true, 'data' => $records]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>