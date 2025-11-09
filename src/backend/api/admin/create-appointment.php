<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Kết nối database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datlichkham";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

// Lấy dữ liệu POST
$data = json_decode(file_get_contents('php://input'), true);

$maBenhNhan = $conn->real_escape_string($data['maBenhNhan']);
$maBacSi = $conn->real_escape_string($data['maBacSi']);
$ngayKham = $conn->real_escape_string($data['ngayKham']);
$trangThai = $conn->real_escape_string($data['trangThai']);

// Tạo mã lịch khám tự động (format: LK + timestamp)
$maLichKham = 'LK' . date('YmdHi') . sprintf('%03d', rand(0, 999));

// Kiểm tra xem bác sĩ có lịch khám trùng không
/*
$checkSql = "SELECT COUNT(*) as count FROM lichkham 
             WHERE maBacSi = '$maBacSi' 
             AND ngayKham = '$ngayKham' 
             AND trangThai != 'cancelled'";
$checkResult = $conn->query($checkSql);
$count = $checkResult->fetch_assoc()['count'];

if ($count > 20) {
    echo json_encode([
        'success' => false,
        'message' => 'Bác sĩ đã đầy lịch khám vào ngày này!'
    ], JSON_UNESCAPED_UNICODE);
    $conn->close();
    exit;
}
*/
// Thêm lịch khám mới
if(empty($maBenhNhan) || empty($maBacSi) || empty($ngayKham) || empty($trangThai)) {
    echo json_encode(['success'=>false,'message'=>'Thiếu thông tin bắt buộc']);
    exit;
}
$sql = "INSERT INTO lichkham (maLichKham, maBenhNhan, maBacSi, ngayKham, trangThai) 
        VALUES ('$maLichKham', '$maBenhNhan', '$maBacSi', '$ngayKham', '$trangThai')";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Thêm lịch khám thành công!',
        'maLichKham' => $maLichKham
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $conn->error
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>