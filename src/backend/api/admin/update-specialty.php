<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Kết nối database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datlichkham";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại: ' . $conn->connect_error]);
    exit;
}

try {
    // Lấy dữ liệu POST
    $data = json_decode(file_get_contents('php://input'), true);

    // maKhoa vẫn được gửi lên từ form, nhưng chúng ta sẽ không dùng nó
    // để UPDATE, chỉ cần tenChuyenKhoa và maChuyenKhoa
    if (empty($data['maChuyenKhoa']) || empty($data['tenChuyenKhoa'])) {
        throw new Exception('Vui lòng cung cấp đủ Mã chuyên khoa và Tên chuyên khoa.');
    }
    
    $maChuyenKhoa = $conn->real_escape_string($data['maChuyenKhoa']);
    $tenChuyenKhoa = $conn->real_escape_string($data['tenChuyenKhoa']);
    $moTa = isset($data['moTa']) ? $conn->real_escape_string($data['moTa']) : '';
    
    // 1. Kiểm tra chuyên khoa có tồn tại không
    $checkSql = "SELECT COUNT(*) as count FROM chuyenkhoa WHERE maChuyenKhoa = ?";
    $stmtCheck = $conn->prepare($checkSql);
    $stmtCheck->bind_param("s", $maChuyenKhoa);
    $stmtCheck->execute();
    $checkResult = $stmtCheck->get_result();
    $count = $checkResult->fetch_assoc()['count'];
    $stmtCheck->close();
    
    if ($count == 0) {
        throw new Exception('Không tìm thấy chuyên khoa để cập nhật!');
    }
    
    // 2. Cập nhật chuyên khoa (CHỈ CẬP NHẬT TÊN VÀ MÔ TẢ)
    // Chúng ta KHÔNG cập nhật maKhoa để đảm bảo tính toàn vẹn
    // của mã (maChuyenKhoa = maKhoa + suffix)
    $sql = "UPDATE chuyenkhoa 
            SET tenChuyenKhoa = ?,
                moTa = ?
            WHERE maChuyenKhoa = ?";
    
    $stmtUpdate = $conn->prepare($sql);
    $stmtUpdate->bind_param("sss", $tenChuyenKhoa, $moTa, $maChuyenKhoa);

    if ($stmtUpdate->execute() === TRUE) {
        if ($conn->affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật chuyên khoa thành công!'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            // Trường hợp này xảy ra khi người dùng bấm "Lưu" mà không thay đổi gì
             echo json_encode([
                'success' => true, 
                'message' => 'Không có thay đổi nào được ghi nhận.'
            ], JSON_UNESCAPED_UNICODE);
        }
    } else {
        throw new Exception('Lỗi khi cập nhật chuyên khoa: ' . $stmtUpdate->error);
    }
    $stmtUpdate->close();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>