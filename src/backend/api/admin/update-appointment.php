<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

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

$maLichKham = $conn->real_escape_string($data['maLichKham']);
$maBenhNhan = $conn->real_escape_string($data['maBenhNhan']);
$maBacSi = $conn->real_escape_string($data['maBacSi']);
$ngayKham = $conn->real_escape_string($data['ngayKham']);
$trangThai = $conn->real_escape_string($data['trangThai']);

// Kiểm tra xem bác sĩ có lịch khám trùng không (trừ lịch khám hiện tại)
$checkSql = "SELECT COUNT(*) as count FROM lichkham 
             WHERE maBacSi = '$maBacSi' 
             AND ngayKham = '$ngayKham' 
             AND maLichKham != '$maLichKham'
             AND trangThai != 'cancelled'";
$checkResult = $conn->query($checkSql);
$count = $checkResult->fetch_assoc()['count'];

if ($count > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Bác sĩ đã có lịch khám vào ngày này!'
    ], JSON_UNESCAPED_UNICODE);
    $conn->close();
    exit;
}

// Cập nhật lịch khám
$sql = "UPDATE lichkham 
        SET maBenhNhan = '$maBenhNhan',
            maBacSi = '$maBacSi',
            ngayKham = '$ngayKham',
            trangThai = '$trangThai'
        WHERE maLichKham = '$maLichKham'";

if ($conn->query($sql) === TRUE) {
    if ($conn->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật lịch khám thành công!'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy lịch khám hoặc dữ liệu không thay đổi'
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $conn->error
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>