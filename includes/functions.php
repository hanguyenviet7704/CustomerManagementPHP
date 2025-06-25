<?php
// Hàm xử lý upload ảnh
function uploadImage($file, $targetDir = "uploads/") {
    // Kiểm tra thư mục upload
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // Tạo tên file duy nhất
    $fileName = uniqid() . "_" . basename($file["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
    // Kiểm tra định dạng file
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    if (in_array(strtolower($fileType), $allowTypes)) {
        // Upload file
        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            return $targetFilePath;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// Hàm định dạng ngày tháng
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    $dateObj = new DateTime($date);
    return $dateObj->format($format);
}

// Hàm định dạng tiền tệ
function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . ' ₫';
}

// Hàm kiểm tra và làm sạch dữ liệu đầu vào
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Hàm chuyển đổi định dạng ngày từ d/m/Y sang Y-m-d
function convertDateFormat($date) {
    if (empty($date)) return null;
    $dateObj = DateTime::createFromFormat('d/m/Y', $date);
    if ($dateObj) {
        return $dateObj->format('Y-m-d');
    }
    return null;
}

// Hàm trả về response JSON
function jsonResponse($status, $message, $data = null) {
    header('Content-Type: application/json');
    $response = [
        'status' => $status,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit;
}
?>