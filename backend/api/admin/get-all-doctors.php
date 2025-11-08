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
    echo json_encode([
        'success' => false,
        'message' => 'Kết nối database thất bại: ' . $conn->connect_error
    ]);
    exit;
}

// Lấy danh sách bác sĩ với thông tin đầy đủ
$sql = "SELECT 
            bs.maBacSi,
            bs.tenBacSi,
            bs.nguoiDungId,
            bs.maChuyenKhoa,
            ck.tenChuyenKhoa,
            ck.maKhoa,
            k.tenKhoa,
            nd.soDienThoai,
            nd.tenDangNhap
        FROM bacsi bs
        LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
        LEFT JOIN khoa k ON ck.maKhoa = k.maKhoa
        LEFT JOIN nguoidung nd ON bs.nguoiDungId = nd.id
        ORDER BY bs.tenBacSi ASC";

$result = $conn->query($sql);

if ($result === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi truy vấn: ' . $conn->error
    ]);
    $conn->close();
    exit;
}

$doctors = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $doctors[] = [
            'maBacSi' => $row['maBacSi'],
            'tenBacSi' => $row['tenBacSi'],
            'nguoiDungId' => $row['nguoiDungId'],
            'maChuyenKhoa' => $row['maChuyenKhoa'],
            'tenChuyenKhoa' => $row['tenChuyenKhoa'],
            'maKhoa' => $row['maKhoa'],
            'tenKhoa' => $row['tenKhoa'],
            'soDienThoai' => $row['soDienThoai'],
            'tenDangNhap' => $row['tenDangNhap']
        ];
    }
}

echo json_encode([
    'success' => true,
    'data' => $doctors,
    'total' => count($doctors)
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>