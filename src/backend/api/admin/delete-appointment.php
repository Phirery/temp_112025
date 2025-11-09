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

// Xóa lịch khám
$sql = "DELETE FROM lichkham WHERE maLichKham = '$maLichKham'";

if ($conn->query($sql) === TRUE) {
    if ($conn->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Xóa lịch khám thành công!'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy lịch khám để xóa'
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