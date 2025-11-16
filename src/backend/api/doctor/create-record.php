<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$conn = new mysqli("localhost", "root", "", "datlichkham");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$maLichKham = $input['maLichKham'] ?? '';
$chanDoan = $input['chanDoan'] ?? '';
$dieuTri = $input['dieuTri'] ?? '';

if (!$maLichKham || !$chanDoan || !$dieuTri) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
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

    $stmt = $conn->prepare("SELECT maBenhNhan FROM lichkham WHERE maLichKham = ?");
    $stmt->bind_param("i", $maLichKham);
    $stmt->execute();
    $maBenhNhan = $stmt->get_result()->fetch_assoc()['maBenhNhan'] ?? null;
    $stmt->close();

    if (!$maBenhNhan) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy lịch khám']);
        exit;
    }

    $maHoSo = 'HS' . date('YmdHis') . rand(100, 999);
    
    $stmt = $conn->prepare("INSERT INTO hosobenhan (maHoSo, maBenhNhan, maBacSi, maLichKham, chanDoan, dieuTri, trangThai, ngayTao) VALUES (?, ?, ?, ?, ?, ?, 'Chưa hoàn thành', NOW())");
    $stmt->bind_param("sssiss", $maHoSo, $maBenhNhan, $maBacSi, $maLichKham, $chanDoan, $dieuTri);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Tạo hồ sơ thành công', 'maHoSo' => $maHoSo]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tạo hồ sơ thất bại']);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>