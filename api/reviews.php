<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Xử lý CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Xác định phương thức HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Lấy danh sách đánh giá hoặc thông tin chi tiết một đánh giá
        if (isset($_GET['id'])) {
            // Lấy thông tin chi tiết một đánh giá
            getReview($pdo, $_GET['id']);
        } else if (isset($_GET['customer_id'])) {
            // Lấy danh sách đánh giá của một khách hàng
            getCustomerReviews($pdo, $_GET['customer_id']);
        } else {
            // Lấy danh sách tất cả đánh giá
            getReviews($pdo);
        }
        break;
    case 'POST':
        // Thêm đánh giá mới
        addReview($pdo);
        break;
    case 'PUT':
        // Cập nhật đánh giá
        updateReview($pdo);
        break;
    case 'DELETE':
        // Xóa đánh giá
        if (isset($_GET['id'])) {
            deleteReview($pdo, $_GET['id']);
        } else {
            jsonResponse(false, "ID đánh giá không được cung cấp");
        }
        break;
    default:
        jsonResponse(false, "Phương thức không được hỗ trợ");
}

// Hàm lấy danh sách tất cả đánh giá
function getReviews($pdo) {
    try {
        $sql = "SELECT r.*, c.full_name, c.email, c.avatar_url 
                FROM customer_reviews r 
                JOIN customers c ON r.customer_id = c.customer_id 
                ORDER BY r.created_at DESC";
        
        // Thêm điều kiện lọc nếu có
        $params = [];
        $whereClause = [];
        
        if (isset($_GET['rating']) && !empty($_GET['rating'])) {
            $whereClause[] = "r.rating = ?";
            $params[] = $_GET['rating'];
        }
        
        if (!empty($whereClause)) {
            $sql = str_replace("ORDER BY", "WHERE " . implode(" AND ", $whereClause) . " ORDER BY", $sql);
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reviews = $stmt->fetchAll();
        
        // Định dạng dữ liệu
        foreach ($reviews as &$review) {
            $review['created_at'] = formatDate($review['created_at'], 'd/m/Y H:i');
        }
        
        jsonResponse(true, "Lấy danh sách đánh giá thành công", $reviews);
    } catch (PDOException $e) {
        jsonResponse(false, "Lỗi khi lấy danh sách đánh giá: " . $e->getMessage());
    }
}

// Hàm lấy danh sách đánh giá của một khách hàng
function getCustomerReviews($pdo, $customerId) {
    try {
        $sql = "SELECT * FROM customer_reviews WHERE customer_id = ? ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$customerId]);
        $reviews = $stmt->fetchAll();
        
        // Định dạng dữ liệu
        foreach ($reviews as &$review) {
            $review['created_at'] = formatDate($review['created_at'], 'd/m/Y H:i');
        }
        
        jsonResponse(true, "Lấy danh sách đánh giá của khách hàng thành công", $reviews);
    } catch (PDOException $e) {
        jsonResponse(false, "Lỗi khi lấy danh sách đánh giá của khách hàng: " . $e->getMessage());
    }
}

// Hàm lấy thông tin chi tiết một đánh giá
function getReview($pdo, $id) {
    try {
        $sql = "SELECT r.*, c.full_name, c.email, c.avatar_url 
                FROM customer_reviews r 
                JOIN customers c ON r.customer_id = c.customer_id 
                WHERE r.review_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $review = $stmt->fetch();
        
        if ($review) {
            // Định dạng dữ liệu
            $review['created_at'] = formatDate($review['created_at'], 'd/m/Y H:i');
            
            jsonResponse(true, "Lấy thông tin đánh giá thành công", $review);
        } else {
            jsonResponse(false, "Không tìm thấy đánh giá");
        }
    } catch (PDOException $e) {
        jsonResponse(false, "Lỗi khi lấy thông tin đánh giá: " . $e->getMessage());
    }
}

// Hàm thêm đánh giá mới
function addReview($pdo) {
    try {
        // Lấy dữ liệu từ request
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Nếu không có dữ liệu JSON, thử lấy từ POST
        if (!$data) {
            $data = $_POST;
        }
        
        // Kiểm tra dữ liệu bắt buộc
        if (empty($data['customer_id']) || empty($data['review_content']) || !isset($data['rating'])) {
            jsonResponse(false, "ID khách hàng, nội dung đánh giá và số sao là bắt buộc");
        }
        
        // Kiểm tra khách hàng tồn tại
        $checkCustomer = $pdo->prepare("SELECT customer_id FROM customers WHERE customer_id = ?");
        $checkCustomer->execute([$data['customer_id']]);
        if ($checkCustomer->rowCount() == 0) {
            jsonResponse(false, "Không tìm thấy khách hàng");
        }
        
        // Kiểm tra rating hợp lệ
        $rating = intval($data['rating']);
        if ($rating < 1 || $rating > 5) {
            jsonResponse(false, "Số sao phải từ 1 đến 5");
        }
        
        // Chuẩn bị câu lệnh SQL
        $sql = "INSERT INTO customer_reviews (customer_id, review_title, review_content, rating, created_at) 
                VALUES (?, ?, ?, ?, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['customer_id'],
            $data['review_title'] ?? null,
            $data['review_content'],
            $rating
        ]);
        
        $reviewId = $pdo->lastInsertId();
        
        jsonResponse(true, "Thêm đánh giá thành công", ['review_id' => $reviewId]);
    } catch (PDOException $e) {
        jsonResponse(false, "Lỗi khi thêm đánh giá: " . $e->getMessage());
    }
}

// Hàm cập nhật đánh giá
function updateReview($pdo) {
    try {
        // Lấy dữ liệu từ request
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Nếu không có dữ liệu JSON, thử lấy từ POST
        if (!$data) {
            parse_str(file_get_contents("php://input"), $data);
        }
        
        // Kiểm tra ID đánh giá
        if (empty($data['review_id'])) {
            jsonResponse(false, "ID đánh giá không được cung cấp");
        }
        
        $reviewId = $data['review_id'];
        
        // Kiểm tra đánh giá tồn tại
        $checkReview = $pdo->prepare("SELECT review_id FROM customer_reviews WHERE review_id = ?");
        $checkReview->execute([$reviewId]);
        if ($checkReview->rowCount() == 0) {
            jsonResponse(false, "Không tìm thấy đánh giá");
        }
        
        // Chuẩn bị câu lệnh SQL
        $updateFields = [];
        $params = [];
        
        if (isset($data['review_title'])) {
            $updateFields[] = "review_title = ?";
            $params[] = $data['review_title'];
        }
        
        if (isset($data['review_content'])) {
            $updateFields[] = "review_content = ?";
            $params[] = $data['review_content'];
        }
        
        if (isset($data['rating'])) {
            $rating = intval($data['rating']);
            if ($rating < 1 || $rating > 5) {
                jsonResponse(false, "Số sao phải từ 1 đến 5");
            }
            $updateFields[] = "rating = ?";
            $params[] = $rating;
        }
        
        if (empty($updateFields)) {
            jsonResponse(false, "Không có dữ liệu để cập nhật");
        }
        
        $sql = "UPDATE customer_reviews SET " . implode(", ", $updateFields) . " WHERE review_id = ?";
        $params[] = $reviewId;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        jsonResponse(true, "Cập nhật đánh giá thành công");
    } catch (PDOException $e) {
        jsonResponse(false, "Lỗi khi cập nhật đánh giá: " . $e->getMessage());
    }
}

// Hàm xóa đánh giá
function deleteReview($pdo, $id) {
    try {
        // Kiểm tra đánh giá tồn tại
        $checkReview = $pdo->prepare("SELECT review_id FROM customer_reviews WHERE review_id = ?");
        $checkReview->execute([$id]);
        if ($checkReview->rowCount() == 0) {
            jsonResponse(false, "Không tìm thấy đánh giá");
        }
        
        // Xóa đánh giá
        $sql = "DELETE FROM customer_reviews WHERE review_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        jsonResponse(true, "Xóa đánh giá thành công");
    } catch (PDOException $e) {
        jsonResponse(false, "Lỗi khi xóa đánh giá: " . $e->getMessage());
    }
}
?>