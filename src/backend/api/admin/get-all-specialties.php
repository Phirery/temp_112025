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

// Lấy danh sách chuyên khoa (có thể lọc theo khoa nếu có tham số)
$maKhoa = isset($_GET['maKhoa']) ? $conn->real_escape_string($_GET['maKhoa']) : '';

$sql = "SELECT 
            maChuyenKhoa,
            tenChuyenKhoa,
            maKhoa,
            moTa
        FROM chuyenkhoa";

if ($maKhoa) {
    $sql .= " WHERE maKhoa = '$maKhoa'";
}

$sql .= " ORDER BY tenChuyenKhoa ASC";

$result = $conn->query($sql);

if ($result === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi truy vấn: ' . $conn->error
    ]);
    $conn->close();
    exit;
}

$specialties = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $specialties[] = [
            'maChuyenKhoa' => $row['maChuyenKhoa'],
            'tenChuyenKhoa' => $row['tenChuyenKhoa'],
            'maKhoa' => $row['maKhoa'],
            'moTa' => $row['moTa']
        ];
    }
}

echo json_encode([
    'success' => true,
    'data' => $specialties
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>