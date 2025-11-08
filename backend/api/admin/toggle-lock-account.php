<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datlichkham";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4"); // Hỗ trợ tiếng Việt

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['id'])) {
    $id = intval($data['id']);
    // Nếu có "lock" = true → đặt trạng thái "Khóa", ngược lại "Hoạt Động"
    $trangThai = !empty($data['lock']) ? 'Khóa' : 'Hoạt Động';

    // Kiểm tra xem tài khoản tồn tại không
    $checkQuery = "SELECT id FROM nguoidung WHERE id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Tài khoản không tồn tại']);
        exit;
    }

    // Cập nhật trạng thái tài khoản
    $updateQuery = "UPDATE nguoidung SET trangThai = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $trangThai, $id);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => ($trangThai === 'Khóa' ? 'Khóa' : 'Mở khóa') . ' tài khoản thành công'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái tài khoản']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu ID tài khoản'
    ]);
}
?>
