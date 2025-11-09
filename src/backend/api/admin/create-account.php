<?php
// CẤU HÌNH API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// KẾT NỐI DATABASE
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

// NHẬN DỮ LIỆU TỪ FRONTEND
$data = json_decode(file_get_contents("php://input"), true);

if (
    !empty($data['vaiTro']) &&
    !empty($data['tenDangNhap']) &&
    !empty($data['matKhau']) &&
    !empty($data['soDienThoai']) &&
    !empty($data['hoTen'])
) {
    // Bắt đầu transaction
    $conn->begin_transaction();

    try {
        // TẠO TÀI KHOẢN NGƯỜI DÙNG
        $hashedPassword = password_hash($data['matKhau'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            INSERT INTO nguoidung (tenDangNhap, matKhau, soDienThoai, vaiTro)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssss", $data['tenDangNhap'], $hashedPassword, $data['soDienThoai'], $data['vaiTro']);
        $stmt->execute();

        $nguoiDungId = $conn->insert_id; // Lấy id người dùng vừa tạo

        if ($data['vaiTro'] === 'benhnhan') {
            // --- Sinh mã bệnh nhân tự động ---
            $result = $conn->query("SELECT COUNT(*) as total FROM benhnhan");
            $count = $result->fetch_assoc()['total'];
            $maBenhNhan = 'BN' . date('YmdHi') . sprintf('%03d', rand(0, 999));

            $stmt = $conn->prepare("
                INSERT INTO benhnhan (nguoiDungId, maBenhNhan, tenBenhNhan, ngaySinh, gioiTinh, soTheBHYT)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "isssss",
                $nguoiDungId,
                $maBenhNhan,
                $data['hoTen'],
                $data['ngaySinh'],
                $data['gioiTinh'],
                $data['soTheBHYT']
            );
            $stmt->execute();

            } elseif ($data['vaiTro'] === 'bacsi') {
            // --- Sinh mã bác sĩ tự động ---
            $result = $conn->query("SELECT COUNT(*) as total FROM bacsi");
            $count = $result->fetch_assoc()['total'];
            $maBacSi = 'BS' . date('YmdHi') . sprintf('%03d', rand(0, 999));

            $stmt = $conn->prepare("
                INSERT INTO bacsi (nguoiDungId, maBacSi, tenBacSi, maChuyenKhoa, moTa)
                VALUES (?, ?, ?, ?, ?)
            ");
            $moTa = isset($data['moTa']) ? $data['moTa'] : null;
            $stmt->bind_param(
                "issss",
                $nguoiDungId,
                $maBacSi,
                $data['hoTen'],
                $data['maChuyenKhoa'],
                $moTa
            );
            $stmt->execute();

        } elseif ($data['vaiTro'] === 'quantri') {
            // --- Sinh mã quản trị viên tự động ---
            $result = $conn->query("SELECT COUNT(*) as total FROM quantrivien");
            $count = $result->fetch_assoc()['total'];
            $maQuanTriVien = 'ADMIN' . date('YmdHi') . sprintf('%03d', rand(0, 999));

            $stmt = $conn->prepare("
                INSERT INTO quantrivien (nguoiDungId, maQuanTriVien)
                VALUES (?, ?)
            ");
            $stmt->bind_param("is", $nguoiDungId, $maQuanTriVien);
            $stmt->execute();
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Tạo tài khoản thành công!'
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        // Rollback khi có lỗi
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Dữ liệu không đầy đủ'
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>