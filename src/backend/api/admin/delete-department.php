<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

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

$conn->begin_transaction();

try {
    // Lấy dữ liệu POST
    $data = json_decode(file_get_contents('php://input'), true);
    $maKhoa = $conn->real_escape_string($data['maKhoa']);
    
    // Kiểm tra xem có chuyên khoa nào không
    $checkSpecialtySql = "SELECT COUNT(*) as count FROM chuyenkhoa WHERE maKhoa = '$maKhoa'";
    $checkResult = $conn->query($checkSpecialtySql);
    $specialtyCount = $checkResult->fetch_assoc()['count'];
    
    if ($specialtyCount > 0) {
        // Kiểm tra xem có bác sĩ nào thuộc các chuyên khoa này không
        $checkDoctorSql = "SELECT COUNT(*) as count 
                          FROM bacsi bs
                          JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
                          WHERE ck.maKhoa = '$maKhoa'";
        $doctorResult = $conn->query($checkDoctorSql);
        $doctorCount = $doctorResult->fetch_assoc()['count'];
        
        if ($doctorCount > 0) {
            throw new Exception("Không thể xóa khoa này vì có $doctorCount bác sĩ đang làm việc! Vui lòng chuyển bác sĩ sang khoa khác trước.");
        }
        
        throw new Exception("Không thể xóa khoa này vì có $specialtyCount chuyên khoa! Vui lòng xóa các chuyên khoa trước.");
    }
    
    // Xóa khoa (CASCADE sẽ tự động xóa chuyên khoa nếu có)
    $sql = "DELETE FROM khoa WHERE maKhoa = '$maKhoa'";
    
    if (!$conn->query($sql)) {
        throw new Exception('Lỗi xóa khoa: ' . $conn->error);
    }
    
    if ($conn->affected_rows === 0) {
        throw new Exception('Không tìm thấy khoa để xóa!');
    }
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Xóa khoa thành công!'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    $conn->rollback();
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>