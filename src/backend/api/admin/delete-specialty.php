<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST'); // Dùng POST cho an toàn
header('Access-Control-Allow-Headers: Content-Type');

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

$conn->begin_transaction();

try {
    // Lấy dữ liệu POST
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['maChuyenKhoa'])) {
        throw new Exception('Vui lòng cung cấp Mã chuyên khoa.');
    }
    
    $maChuyenKhoa = $conn->real_escape_string($data['maChuyenKhoa']);
    
    // 1. Kiểm tra xem có bác sĩ nào thuộc chuyên khoa này không
    $checkDoctorSql = "SELECT COUNT(*) as count FROM bacsi WHERE maChuyenKhoa = '$maChuyenKhoa'";
    $checkResult = $conn->query($checkDoctorSql);
    $doctorCount = $checkResult->fetch_assoc()['count'];
    
    if ($doctorCount > 0) {
        // Dựa vào file db.sql, ràng buộc `bacsi_ibfk_2` là ON DELETE SET NULL
        // Tuy nhiên, việc thông báo cho admin biết là tốt hơn
        throw new Exception("Không thể xóa chuyên khoa này vì có $doctorCount bác sĩ đang thuộc chuyên khoa. (Hoặc bạn có thể cấu hình để tự động gán bác sĩ sang 'Chưa phân loại')");
        
        /* // LỰA CHỌN KHÁC: Nếu bạn muốn tự động gán bác sĩ sang NULL (như trong db.sql)
        // thì không cần throw Exception ở đây, cứ để câu lệnh DELETE chạy.
        // Ràng buộc ngoại khóa sẽ tự xử lý.
        // Nhưng logic an toàn là nên thông báo.
        */
    }
    
    // 2. Xóa chuyên khoa
    // Do db.sql có `ON DELETE SET NULL` cho bảng `bacsi`,
    // và `ON DELETE CASCADE` không có ở đây, ta có thể xóa trực tiếp
    // nếu đã qua bước kiểm tra bác sĩ ở trên.
    $sql = "DELETE FROM chuyenkhoa WHERE maChuyenKhoa = '$maChuyenKhoa'";
    
    if (!$conn->query($sql)) {
        throw new Exception('Lỗi xóa chuyên khoa: ' . $conn->error);
    }
    
    if ($conn->affected_rows === 0) {
        throw new Exception('Không tìm thấy chuyên khoa để xóa!');
    }
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Xóa chuyên khoa thành công!'
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    $conn->rollback(); // Hoàn tác nếu có lỗi
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>