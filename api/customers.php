<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
//haha
// Xử lý CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Xác định phương thức HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Lấy danh sách khách hàng hoặc thông tin chi tiết một khách hàng
        if (isset($_GET['id'])) {
            // Lấy thông tin chi tiết một khách hàng
            getCustomer($pdo, $_GET['id']);
        } else {
            // Lấy danh sách khách hàng
            getCustomers($pdo);
        }
        break;
    case 'POST':
        // Thêm khách hàng mới
        addCustomer($pdo);
        break;
    case 'PUT':
        // Cập nhật thông tin khách hàng
        updateCustomer($pdo);
        break;
    case 'DELETE':
        // Xóa khách hàng
        if (isset($_GET['id'])) {
            deleteCustomer($pdo, $_GET['id']);
        } else {
            jsonResponse(false, "ID khách hàng không được cung cấp");
        }
        break;
    default:
        jsonResponse(false, "Phương thức không được hỗ trợ");
}

// Hàm lấy danh sách khách hàng
function getCustomers($pdo) {
    try {
        $sql = "SELECT c.*, co.company_name 
                FROM customers c 
                LEFT JOIN companies co ON c.company_id = co.company_id";
        
        // Thêm điều kiện lọc nếu có
        $params = [];
        $whereClause = [];
        
        if (isset($_GET['gender']) && !empty($_GET['gender'])) {
            $whereClause[] = "c.gender = ?";
            $params[] = $_GET['gender'];
        }
        
        if (isset($_GET['company_id']) && !empty($_GET['company_id'])) {
            $whereClause[] = "c.company_id = ?";
            $params[] = $_GET['company_id'];
        }
        
        if (isset($_GET['min_spent']) && !empty($_GET['min_spent'])) {
            $whereClause[] = "c.total_spent >= ?";
            $params[] = $_GET['min_spent'];
        }
        
        if (!empty($whereClause)) {
            $sql .= " WHERE " . implode(" AND ", $whereClause);
        }
        
        $sql .= " ORDER BY c.customer_id DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $customers = $stmt->fetchAll();
        
        // Định dạng dữ liệu trước khi trả về
        foreach ($customers as &$customer) {
            $customer['dob'] = formatDate($customer['dob']);
            $customer['total_spent'] = formatCurrency($customer['total_spent']);
        }
        
        jsonResponse(true, "Lấy danh sách khách hàng thành công", $customers);
    } catch (PDOException $e) {
        jsonResponse(false, "Lỗi khi lấy danh sách khách hàng: " . $e->getMessage());
    }
}

// Hàm lấy thông tin chi tiết một khách hàng
function getCustomer($pdo, $id) {
    try {
        $sql = "SELECT c.*, co.company_name 
                FROM customers c 
                LEFT JOIN companies co ON c.company_id = co.company_id 
                WHERE c.customer_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $customer = $stmt->fetch();
        
        if ($customer) {
            // Lấy danh sách ghi chú
            $sqlNotes = "SELECT * FROM customer_notes WHERE customer_id = ? ORDER BY created_at DESC";
            $stmtNotes = $pdo->prepare($sqlNotes);
            $stmtNotes->execute([$id]);
            $notes = $stmtNotes->fetchAll();
            
            // Lấy danh sách đánh giá
            $sqlReviews = "SELECT * FROM customer_reviews WHERE customer_id = ? ORDER BY created_at DESC";
            $stmtReviews = $pdo->prepare($sqlReviews);
            $stmtReviews->execute([$id]);
            $reviews = $stmtReviews->fetchAll();
            
            // Lấy danh sách tags
            $sqlTags = "SELECT t.* FROM customer_tags t 
                        JOIN customer_tag_map m ON t.tag_id = m.tag_id 
                        WHERE m.customer_id = ?";
            $stmtTags = $pdo->prepare($sqlTags);
            $stmtTags->execute([$id]);
            $tags = $stmtTags->fetchAll();
            
            // Định dạng dữ liệu
            $customer['dob'] = formatDate($customer['dob']);
            $customer['total_spent'] = formatCurrency($customer['total_spent']);
            
            // Thêm dữ liệu liên quan
            $customer['notes'] = $notes;
            $customer['reviews'] = $reviews;
            $customer['tags'] = $tags;
            
            jsonResponse(true, "Lấy thông tin khách hàng thành công", $customer);
        } else {
            jsonResponse(false, "Không tìm thấy khách hàng");
        }
    } catch (PDOException $e) {
        jsonResponse(false, "Lỗi khi lấy thông tin khách hàng: " . $e->getMessage());
    }
}

// Hàm thêm khách hàng mới
function addCustomer($pdo) {
    try {
        // Lấy dữ liệu từ request
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Nếu không có dữ liệu JSON, thử lấy từ POST
        if (!$data) {
            $data = $_POST;
        }
        
        // Kiểm tra dữ liệu bắt buộc
        if (empty($data['full_name']) || empty($data['email'])) {
            jsonResponse(false, "Họ tên và email là bắt buộc");
        }
        
        // Kiểm tra email đã tồn tại chưa
        $checkEmail = $pdo->prepare("SELECT customer_id FROM customers WHERE email = ?");
        $checkEmail->execute([$data['email']]);
        if ($checkEmail->rowCount() > 0) {
            jsonResponse(false, "Email đã tồn tại trong hệ thống");
        }
        
        // Xử lý upload ảnh đại diện nếu có
        $avatarUrl = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            $avatarUrl = uploadImage($_FILES['avatar'], "../uploads/avatars/");
            if (!$avatarUrl) {
                jsonResponse(false, "Lỗi khi upload ảnh đại diện");
            }
        }
        
        // Chuyển đổi định dạng ngày sinh
        $dob = !empty($data['dob']) ? convertDateFormat($data['dob']) : null;
        
        // Chuẩn bị câu lệnh SQL
        $sql = "INSERT INTO customers (full_name, email, phone, gender, dob, description, total_spent, avatar_url, company_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['full_name'],
            $data['email'],
            $data['phone'] ?? null,
            $data['gender'] ?? null,
            $dob,
            $data['description'] ?? null,
            $data['total_spent'] ?? 0,
            $avatarUrl,
            $data['company_id'] ?? null
        ]);
        
        $customerId = $pdo->lastInsertId();
        
        // Xử lý tags nếu có
        if (isset($data['tags']) && is_array($data['tags'])) {
            foreach ($data['tags'] as $tagId) {
                $sqlTag = "INSERT INTO customer_tag_map (customer_id, tag_id) VALUES (?, ?)";
                $stmtTag = $pdo->prepare($sqlTag);
                $stmtTag->execute([$customerId, $tagId]);
            }
        }
        
        jsonResponse(true, "Thêm khách hàng thành công", ['customer_id' => $customerId]);
    } catch (PDOException $e) {
        jsonResponse(false, "Lỗi khi thêm khách hàng: " . $e->getMessage());
    }
}

// Hàm cập nhật thông tin khách hàng
function updateCustomer($pdo) {
    try {
        // Lấy dữ liệu từ request
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Kiểm tra ID khách hàng
        if (empty($data['customer_id'])) {
            jsonResponse(false, "ID khách hàng không được cung cấp");
        }
        
        $customerId = $data['customer_id'];
        
        // Kiểm tra khách hàng tồn tại
        $checkCustomer = $pdo->prepare("SELECT customer_id FROM customers WHERE customer_id = ?");
        $checkCustomer->execute([$customerId]);
        if ($checkCustomer->rowCount() == 0) {
            jsonResponse(false, "Không tìm thấy khách hàng");
        }
        
        
        // Chuẩn bị câu lệnh SQL
        $updateFields = [];
        $params = [];
        
        if (isset($data['full_name'])) {
            $updateFields[] = "full_name = ?";
            $params[] = $data['full_name'];
        }
        
        if (isset($data['email'])) {
            $updateFields[] = "email = ?";
            $params[] = $data['email'];
        }
        
        if (isset($data['phone'])) {
            $updateFields[] = "phone = ?";
            $params[] = $data['phone'];
        }
        
        if (isset($data['gender'])) {
            $updateFields[] = "gender = ?";
            $params[] = $data['gender'];
        }
        
        if (isset($data['dob'])) {
            $updateFields[] = "dob = ?";
            $params[] = convertDateFormat($data['dob']);
        }
        
        if (isset($data['description'])) {
            $updateFields[] = "description = ?";
            $params[] = $data['description'];
        }
        
        if (isset($data['total_spent'])) {
            $updateFields[] = "total_spent = ?";
            $params[] = $data['total_spent'];
        }
        
        if (isset($data['company_id'])) {
            $updateFields[] = "company_id = ?";
            $params[] = $data['company_id'];
        }
        
        if (empty($updateFields)) {
            jsonResponse(false, "Không có dữ liệu để cập nhật");
        }
        
        $sql = "UPDATE customers SET " . implode(", ", $updateFields) . " WHERE customer_id = ?";
        $params[] = $customerId;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        jsonResponse(true, "Cập nhật thông tin khách hàng thành công");
    } catch (PDOException $e) {
        jsonResponse(false, "Lỗi khi cập nhật thông tin khách hàng: " . $e->getMessage());
    }
}

// Hàm xóa khách hàng
function deleteCustomer($pdo, $id) {
    try {
        // Kiểm tra khách hàng tồn tại
        $checkCustomer = $pdo->prepare("SELECT customer_id FROM customers WHERE customer_id = ?");
        $checkCustomer->execute([$id]);
        if ($checkCustomer->rowCount() == 0) {
            jsonResponse(false, "Không tìm thấy khách hàng");
        }
        
        // Xóa khách hàng
        $sql = "DELETE FROM customers WHERE customer_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        jsonResponse(true, "Xóa khách hàng thành công");
    } catch (PDOException $e) {
        jsonResponse(false, "Lỗi khi xóa khách hàng: " . $e->getMessage());
    }
}
?>