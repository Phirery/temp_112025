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

// Lấy danh sách khoa với số lượng chuyên khoa
$sql = "SELECT 
            k.maKhoa,
            k.tenKhoa,
            k.moTa,
            COUNT(DISTINCT ck.maChuyenKhoa) as soChuyenKhoa
        FROM khoa k
        LEFT JOIN chuyenkhoa ck ON k.maKhoa = ck.maKhoa
        GROUP BY k.maKhoa, k.tenKhoa, k.moTa
        ORDER BY k.tenKhoa ASC";

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
        $maKhoa = $row['maKhoa'];
        
        // Lấy danh sách chuyên khoa của khoa này
        $specialtySql = "SELECT maChuyenKhoa, tenChuyenKhoa, moTa 
                         FROM chuyenkhoa 
                         WHERE maKhoa = '$maKhoa'
                         ORDER BY tenChuyenKhoa ASC";
        $specialtyResult = $conn->query($specialtySql);
        
        $specialties = [];
        if ($specialtyResult && $specialtyResult->num_rows > 0) {
            while($spec = $specialtyResult->fetch_assoc()) {
                $specialties[] = [
                    'maChuyenKhoa' => $spec['maChuyenKhoa'],
                    'tenChuyenKhoa' => $spec['tenChuyenKhoa'],
                    'moTa' => $spec['moTa']
                ];
            }
        }
        
        $departments[] = [
            'maKhoa' => $row['maKhoa'],
            'tenKhoa' => $row['tenKhoa'],
            'moTa' => $row['moTa'],
            'soChuyenKhoa' => (int)$row['soChuyenKhoa'],
            'specialties' => $specialties
        ];
    }
}

echo json_encode([
    'success' => true,
    'data' => $departments,
    'total' => count($departments)
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>