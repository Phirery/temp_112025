<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

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

// Lấy danh sách khoa
$sql = "SELECT 
            maKhoa,
            tenKhoa,
            moTa
        FROM khoa
        ORDER BY tenKhoa ASC";

$result = $conn->query($sql);

if ($result === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi truy vấn: ' . $conn->error
    ]);
    $conn->close();
    exit;
}

$departments = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $departments[] = [
            'maKhoa' => $row['maKhoa'],
            'tenKhoa' => $row['tenKhoa'],
            'moTa' => $row['moTa']
        ];
    }
}

echo json_encode([
    'success' => true,
    'data' => $departments
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>