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

// Lấy danh sách bệnh nhân
$sql = "SELECT 
            bn.maBenhNhan,
            bn.tenBenhNhan,
            bn.ngaySinh,
            bn.gioiTinh,
            bn.soTheBHYT,
            nd.soDienThoai
        FROM benhnhan bn
        LEFT JOIN nguoidung nd ON bn.nguoiDungId = nd.id
        ORDER BY bn.tenBenhNhan ASC";

$result = $conn->query($sql);

if ($result === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi truy vấn: ' . $conn->error
    ]);
    $conn->close();
    exit;
}

$patients = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $patients[] = [
            'maBenhNhan' => $row['maBenhNhan'],
            'tenBenhNhan' => $row['tenBenhNhan'],
            'ngaySinh' => $row['ngaySinh'],
            'gioiTinh' => $row['gioiTinh'],
            'soTheBHYT' => $row['soTheBHYT'],
            'soDienThoai' => $row['soDienThoai']
        ];
    }
}

echo json_encode([
    'success' => true,
    'data' => $patients
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>