<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

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

// Lấy danh sách lịch khám với thông tin bệnh nhân và bác sĩ
$sql = "SELECT 
            lk.maLichKham,
            lk.maBenhNhan,
            lk.maBacSi,
            lk.ngayKham,
            lk.trangThai,
            bn.tenBenhNhan,
            bn.ngaySinh,
            bn.gioiTinh,
            bs.tenBacSi,
            ck.tenChuyenKhoa,
            k.tenKhoa
        FROM lichkham lk
        LEFT JOIN benhnhan bn ON lk.maBenhNhan = bn.maBenhNhan
        LEFT JOIN bacsi bs ON lk.maBacSi = bs.maBacSi
        LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
        LEFT JOIN khoa k ON ck.maKhoa = k.maKhoa
        ORDER BY lk.ngayKham DESC";

$result = $conn->query($sql);

if ($result === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi truy vấn: ' . $conn->error
    ]);
    $conn->close();
    exit;
}

$appointments = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $appointments[] = [
            'maLichKham' => $row['maLichKham'],
            'maBenhNhan' => $row['maBenhNhan'],
            'maBacSi' => $row['maBacSi'],
            'ngayKham' => $row['ngayKham'],
            'trangThai' => $row['trangThai'],
            'tenBenhNhan' => $row['tenBenhNhan'] ?? 'N/A',
            'ngaySinh' => $row['ngaySinh'],
            'gioiTinh' => $row['gioiTinh'],
            'tenBacSi' => $row['tenBacSi'] ?? 'N/A',
            'tenChuyenKhoa' => $row['tenChuyenKhoa'],
            'tenKhoa' => $row['tenKhoa']
        ];
    }
}

echo json_encode([
    'success' => true,
    'data' => $appointments,
    'total' => count($appointments)
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>