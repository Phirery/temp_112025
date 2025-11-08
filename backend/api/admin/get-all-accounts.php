<?php
// Cho phép truy cập từ mọi nguồn và trả về JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Kết nối database (thay '../../config/database.php')
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datlichkham";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4"); // đảm bảo tiếng Việt hiển thị đúng

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

// -------------------------------------------
// Lấy danh sách tài khoản cùng thông tin liên quan
// - Bảng nguoidung: thông tin đăng nhập cơ bản
// - Bảng benhnhan: thông tin bệnh nhân nếu vai trò là 'benhnhan'
// - Bảng bacsi: thông tin bác sĩ nếu vai trò là 'bacsi'
// - Bảng chuyenkhoa: tên chuyên khoa của bác sĩ
// -------------------------------------------

$sql = "
SELECT 
    nd.id,                       -- ID người dùng
    nd.tenDangNhap,               -- Tên đăng nhập
    nd.soDienThoai,               -- Số điện thoại
    nd.vaiTro,                    -- Vai trò: benhnhan / bacsi / quantri
    CASE 
        WHEN nd.vaiTro = 'benhnhan' THEN bn.tenBenhNhan
        WHEN nd.vaiTro = 'bacsi' THEN bs.tenBacSi
        ELSE 'Admin'
    END AS hoTen,                 -- Họ tên hiển thị
    CASE 
        WHEN nd.vaiTro = 'benhnhan' THEN bn.ngaySinh
        ELSE NULL
    END AS ngaySinh,              -- Ngày sinh (bệnh nhân)
    CASE 
        WHEN nd.vaiTro = 'benhnhan' THEN bn.gioiTinh
        ELSE NULL
    END AS gioiTinh,              -- Giới tính (bệnh nhân)
    CASE 
        WHEN nd.vaiTro = 'benhnhan' THEN bn.soTheBHYT
        ELSE NULL
    END AS soTheBHYT,             -- Số thẻ BHYT (bệnh nhân)
    CASE 
        WHEN nd.vaiTro = 'bacsi' THEN bs.maBacSi
        ELSE NULL
    END AS maBacSi,               -- Mã bác sĩ
    CASE 
        WHEN nd.vaiTro = 'bacsi' THEN ck.tenChuyenKhoa
        ELSE NULL
    END AS tenChuyenKhoa,         -- Tên chuyên khoa của bác sĩ
    0 AS locked                   -- Trạng thái khóa (hiện chưa dùng)
FROM nguoidung nd
LEFT JOIN benhnhan bn ON nd.id = bn.nguoiDungId
LEFT JOIN bacsi bs ON nd.id = bs.nguoiDungId
LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
ORDER BY nd.id DESC
";

// Thực thi truy vấn
$result = $conn->query($sql);

if ($result) {
    $accounts = [];
    while ($row = $result->fetch_assoc()) {
        $accounts[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $accounts
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $conn->error
    ]);
}

$conn->close();
?>
