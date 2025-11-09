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

// Lấy tham số
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'month';
$dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : null;
$dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : null;

// Xác định khoảng thời gian
$dateCondition = getDateCondition($filter, $dateFrom, $dateTo);
$previousDateCondition = getPreviousDateCondition($filter, $dateFrom, $dateTo);

// 1. SUMMARY DATA
$summary = getSummaryData($conn, $dateCondition, $previousDateCondition);

// 2. APPOINTMENTS TREND
$appointmentsTrend = getAppointmentsTrend($conn, $filter, $dateCondition);

// 3. PATIENTS TREND
$patientsTrend = getPatientsTrend($conn, $filter, $dateCondition);

// 4. DEPARTMENTS DATA
$departmentsData = getDepartmentsData($conn, $dateCondition);

// 5. STATUS DATA
$statusData = getStatusData($conn, $dateCondition);

// 6. REVENUE TREND
$revenueTrend = getRevenueTrend($conn, $filter, $dateCondition);

// 7. TOP DOCTORS
$topDoctors = getTopDoctors($conn, $dateCondition);

// Trả về JSON
echo json_encode([
    'success' => true,
    'data' => [
        'summary' => $summary,
        'appointmentsTrend' => $appointmentsTrend,
        'patientsTrend' => $patientsTrend,
        'departmentsData' => $departmentsData,
        'statusData' => $statusData,
        'revenueTrend' => $revenueTrend,
        'topDoctors' => $topDoctors
    ]
], JSON_UNESCAPED_UNICODE);

$conn->close();

// ===================== HELPER FUNCTIONS =====================

function getDateCondition($filter, $dateFrom, $dateTo) {
    $today = date('Y-m-d');
    
    switch($filter) {
        case 'week':
            $startOfWeek = date('Y-m-d', strtotime('monday this week'));
            return "ngayKham >= '$startOfWeek'";
            
        case 'month':
            $startOfMonth = date('Y-m-01');
            return "ngayKham >= '$startOfMonth'";
            
        case 'year':
            $startOfYear = date('Y-01-01');
            return "ngayKham >= '$startOfYear'";
            
        case 'custom':
            if ($dateFrom && $dateTo) {
                return "ngayKham BETWEEN '$dateFrom' AND '$dateTo'";
            }
            return "1=1";
            
        case 'all':
        default:
            return "1=1";
    }
}

function getPreviousDateCondition($filter, $dateFrom, $dateTo) {
    $today = date('Y-m-d');
    
    switch($filter) {
        case 'week':
            $startOfLastWeek = date('Y-m-d', strtotime('monday last week'));
            $endOfLastWeek = date('Y-m-d', strtotime('sunday last week'));
            return "ngayKham BETWEEN '$startOfLastWeek' AND '$endOfLastWeek'";
            
        case 'month':
            $startOfLastMonth = date('Y-m-01', strtotime('first day of last month'));
            $endOfLastMonth = date('Y-m-t', strtotime('last day of last month'));
            return "ngayKham BETWEEN '$startOfLastMonth' AND '$endOfLastMonth'";
            
        case 'year':
            $lastYear = date('Y') - 1;
            return "YEAR(ngayKham) = $lastYear";
            
        default:
            return "1=1";
    }
}

function getSummaryData($conn, $dateCondition, $previousDateCondition) {
    // Current period
    $result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE $dateCondition");
    $currentAppointments = $result->fetch_assoc()['count'];
    
    // Previous period
    $result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE $previousDateCondition");
    $previousAppointments = $result->fetch_assoc()['count'];
    
    $appointmentChange = $previousAppointments > 0 
        ? (($currentAppointments - $previousAppointments) / $previousAppointments) * 100 
        : 0;
    
    // Patients
    $result = $conn->query("SELECT COUNT(*) as count FROM benhnhan");
    $totalPatients = $result->fetch_assoc()['count'];
    
    // Doctors
    $result = $conn->query("SELECT COUNT(*) as count FROM bacsi");
    $totalDoctors = $result->fetch_assoc()['count'];
    
    // Revenue (ước tính: mỗi lịch khám = 500,000 VNĐ)
    $revenue = $currentAppointments * 150000;
    $previousRevenue = $previousAppointments * 150000;
    $revenueChange = $previousRevenue > 0 
        ? (($revenue - $previousRevenue) / $previousRevenue) * 100 
        : 0;
    
    return [
        'appointments' => $currentAppointments,
        'patients' => $totalPatients,
        'doctors' => $totalDoctors,
        'revenue' => $revenue,
        'appointmentChange' => round($appointmentChange, 1),
        'patientChange' => 0, // Giả lập
        'revenueChange' => round($revenueChange, 1)
    ];
}

function getAppointmentsTrend($conn, $filter, $dateCondition) {
    $labels = [];
    $values = [];
    
    if ($filter == 'week') {
        // 7 ngày gần nhất
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dayName = date('l', strtotime($date));
            $dayNames = [
                'Monday' => 'T2', 'Tuesday' => 'T3', 'Wednesday' => 'T4',
                'Thursday' => 'T5', 'Friday' => 'T6', 'Saturday' => 'T7', 'Sunday' => 'CN'
            ];
            $labels[] = $dayNames[$dayName];
            
            $result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE ngayKham = '$date'");
            $values[] = (int)$result->fetch_assoc()['count'];
        }
    } elseif ($filter == 'month') {
        // 4 tuần trong tháng
        for ($i = 3; $i >= 0; $i--) {
            $labels[] = "Tuần " . (4 - $i);
            $startDate = date('Y-m-d', strtotime("-" . ($i * 7) . " days"));
            $endDate = date('Y-m-d', strtotime("-" . ($i * 7 - 6) . " days"));
            
            $result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE ngayKham BETWEEN '$endDate' AND '$startDate'");
            $values[] = (int)$result->fetch_assoc()['count'];
        }
    } elseif ($filter == 'year') {
        // 12 tháng trong năm
        $currentYear = date('Y');
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = "T" . $i;
            $result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE YEAR(ngayKham) = $currentYear AND MONTH(ngayKham) = $i");
            $values[] = (int)$result->fetch_assoc()['count'];
        }
    } else {
        // Toàn bộ - theo năm
        $result = $conn->query("SELECT YEAR(ngayKham) as year, COUNT(*) as count FROM lichkham GROUP BY YEAR(ngayKham) ORDER BY year");
        while($row = $result->fetch_assoc()) {
            $labels[] = "Năm " . $row['year'];
            $values[] = (int)$row['count'];
        }
    }
    
    return [
        'labels' => $labels,
        'values' => $values
    ];
}

function getPatientsTrend($conn, $filter, $dateCondition) {
    // Giả lập dữ liệu (vì không có trường ngày tạo trong bảng benhnhan)
    $labels = [];
    $values = [];
    
    if ($filter == 'week') {
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dayName = date('l', strtotime($date));
            $dayNames = [
                'Monday' => 'T2', 'Tuesday' => 'T3', 'Wednesday' => 'T4',
                'Thursday' => 'T5', 'Friday' => 'T6', 'Saturday' => 'T7', 'Sunday' => 'CN'
            ];
            $labels[] = $dayNames[$dayName];
            $values[] = rand(5, 20); // Giả lập
        }
    } elseif ($filter == 'month') {
        for ($i = 3; $i >= 0; $i--) {
            $labels[] = "Tuần " . (4 - $i);
            $values[] = rand(30, 80);
        }
    } else {
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = "T" . $i;
            $values[] = rand(100, 300);
        }
    }
    
    return [
        'labels' => $labels,
        'values' => $values
    ];
}

function getDepartmentsData($conn, $dateCondition) {
    $sql = "SELECT k.tenKhoa, COUNT(lk.maLichKham) as count
            FROM khoa k
            LEFT JOIN chuyenkhoa ck ON k.maKhoa = ck.maKhoa
            LEFT JOIN bacsi bs ON ck.maChuyenKhoa = bs.maChuyenKhoa
            LEFT JOIN lichkham lk ON bs.maBacSi = lk.maBacSi AND $dateCondition
            GROUP BY k.maKhoa, k.tenKhoa
            HAVING count > 0
            ORDER BY count DESC
            LIMIT 7";
    
    $result = $conn->query($sql);
    
    $labels = [];
    $values = [];
    
    while($row = $result->fetch_assoc()) {
        $labels[] = $row['tenKhoa'];
        $values[] = (int)$row['count'];
    }
    
    return [
        'labels' => $labels,
        'values' => $values
    ];
}

function getStatusData($conn, $dateCondition) {
    $sql = "SELECT 
                SUM(CASE WHEN trangThai = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN trangThai = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN trangThai = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            FROM lichkham
            WHERE $dateCondition";
    
    $result = $conn->query($sql);
    $data = $result->fetch_assoc();
    
    return [
        'confirmed' => (int)$data['confirmed'],
        'pending' => (int)$data['pending'],
        'cancelled' => (int)$data['cancelled']
    ];
}

function getRevenueTrend($conn, $filter, $dateCondition) {
    $appointmentsTrend = getAppointmentsTrend($conn, $filter, $dateCondition);
    
    // Doanh thu = Số lịch khám * 150,000 VNĐ
    $values = array_map(function($count) {
        return $count * 150000;
    }, $appointmentsTrend['values']);
    
    return [
        'labels' => $appointmentsTrend['labels'],
        'values' => $values
    ];
}

function getTopDoctors($conn, $dateCondition) {
    $sql = "SELECT 
                bs.maBacSi,
                bs.tenBacSi,
                ck.tenChuyenKhoa,
                COUNT(lk.maLichKham) as total,
                SUM(CASE WHEN lk.trangThai = 'confirmed' THEN 1 ELSE 0 END) as completed
            FROM bacsi bs
            LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
            LEFT JOIN lichkham lk ON bs.maBacSi = lk.maBacSi AND $dateCondition
            GROUP BY bs.maBacSi, bs.tenBacSi, ck.tenChuyenKhoa
            HAVING total > 0
            ORDER BY total DESC
            LIMIT 10";
    
    $result = $conn->query($sql);
    
    $doctors = [];
    while($row = $result->fetch_assoc()) {
        $doctors[] = [
            'maBacSi' => $row['maBacSi'],
            'tenBacSi' => $row['tenBacSi'],
            'tenChuyenKhoa' => $row['tenChuyenKhoa'],
            'total' => (int)$row['total'],
            'completed' => (int)$row['completed']
        ];
    }
    
    return $doctors;
}
?>