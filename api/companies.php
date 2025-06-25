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
        // Lấy danh sách công ty hoặc thông tin chi tiết một công ty
        if (isset($_GET['id'])) {
            // Lấy thông tin chi tiết một công ty
            getCompany($pdo, $_GET['id']);
        } else {
            // Lấy danh sách công ty
            getCompanies($pdo);
        }
        break;
    case 'POST':
        // Thêm công ty mới
        addCompany($pdo);
        break;
    case 'PUT':
        // Cập nhật thông tin công ty
        updateCompany($pdo);
        break;
    case 'DELETE':
        // Xóa công ty
        if (isset($_GET['id'])) {
            deleteCompany($pdo, $_GET['id']);
        } else {
            jsonResponse(false, "ID công ty không được cung cấp");
        }
        break;
    default:
        jsonResponse(false, "Phương thức không được hỗ trợ");
}

// Hàm lấy danh sách công ty
function getCompanies($pdo) {
    try {
        $sql = "SELECT * FROM companies ORDER BY company_name ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $companies = $stmt->fetchAll();
        
        jsonResponse(true, "Lấy danh sách công ty thành công", $companies);
    } catch (PDOException $e) {
        jsonResponse(false, "Lỗi khi lấy danh sách công ty: " . $e->getMessage());
    }
}

// Hàm lấy thông tin chi tiết một công ty
function getCompany($pdo, $id) {
    try {
        $sql = "SELECT * FROM companies WHERE company_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $company = $stmt->fetch();
        
        if ($company) {
            // Lấy danh sách khách hàng thuộc công ty
            $sqlCustomers = "SELECT customer_id, full_name, email, phone, total_spent 
                            FROM customers 
                            WHERE company_id = ? 
                            ORDER BY full_name ASC";
            $stmtCustomers = $pdo->prepare($sqlCustomers);
            $stmtCustomers->execute([$id]);
            $customers = $stmtCustomers->fetchAll();
            
            // Định dạng dữ liệu
            foreach ($customers as &$customer) {
                $customer['total_spent'] = formatCurrency($customer['total_spent']);
            }
            
            // Thêm dữ liệu liên quan
            $company['customers'] = $customers;
            
            jsonResponse(true, "Lấy thông tin công ty thành công", $company);
        } else {
            jsonResponse(false, "Không tìm thấy công ty");
        }
    } catch (PDOException $e) {
        jsonResponse(false, "Lỗi khi lấy thông tin công ty: " . $e->getMessage());
    }
}

// Hàm thêm công ty mới
function addCompany($pdo) {
    try {
        // Lấy dữ liệu từ request
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Nếu không có dữ liệu JSON, thử lấy từ POST
        if (!$data) {
            $data = $_POST;
        }
        
        // Kiểm tra dữ liệu bắt buộc
        if (empty($data['company_name'])) {
            jsonResponse(false, "Tên công ty là bắt buộc");
        }
        
        // Chuẩn bị câu lệnh SQL
        $sql = "INSERT INTO companies (company_name, industry, address, phone, website, description) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['company_name'],
            $data['industry'] ?? null,
            $data['address'] ?? null,
            $data['phone'] ?? null,
            $data['website'] ?? null,
            $data['description'] ?? null
        ]);
        
        $companyId = $pdo->lastInsertId();
        
        jsonResponse(true, "Thêm công ty thành công", ['company_id' => $companyId]);
    } catch (PDOException $e) {
        jsonResponse(false, "Lỗi khi thêm công ty: " . $e->getMessage());
    }
}

// Hàm cập nhật thông tin công ty
function updateCompany($pdo) {
    try {
        // Lấy dữ liệu từ request
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Nếu không có dữ liệu JSON, thử lấy từ POST
        if (!$data) {
            parse_str(file_get_contents("php://input"), $data);
        }
        
        // Kiểm tra ID công ty
        if (empty($data['company_id'])) {
            jsonResponse(false, "ID công ty không được cung cấp");
        }
        
        $companyId = $data['company_id'];
        
        // Kiểm tra công ty tồn tại
        $checkCompany = $pdo->prepare("SELECT company_id FROM companies WHERE company_id = ?");
        $checkCompany->execute([$companyId]);
        if ($checkCompany->rowCount() == 0) {
            jsonResponse(false, "Không tìm thấy công ty");
        }
        
        // Chuẩn bị câu lệnh SQL
        $updateFields = [];
        $params = [];
        
        if (isset($data['company_name'])) {
            $updateFields[] = "company_name = ?";
            $params[] = $data['company_name'];
        }
        
        if (isset($data['industry'])) {
            $updateFields[] = "industry = ?";
            $params[] = $data['industry'];
        }
        
        if (isset($data['address'])) {
            $updateFields[] = "address = ?";
            $params[] = $data['address'];
        }
        
        if (isset($data['phone'])) {
            $updateFields[] = "phone = ?";
            $params[] = $data['phone'];
        }
        
        if (isset($data['website'])) {
            $updateFields[] = "website = ?";
            $params[] = $data['website'];
        }
        
        if (isset($data['description'])) {
            $updateFields[] = "description = ?";
            $params[] = $data['description'];
        }
        
        if (empty($updateFields)) {
            jsonResponse(false, "Không có dữ liệu để cập nhật");
        }
        
        $sql = "UPDATE companies SET " . implode(", ", $updateFields) . " WHERE company_id = ?";
        $params[] = $companyId;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        jsonResponse(true, "Cập nhật thông tin công ty thành công");
    } catch (PDOException $e) {
        jsonResponse(false, "Lỗi khi cập nhật thông tin công ty: " . $e->getMessage());
    }
}

// Hàm xóa công ty
function deleteCompany($pdo, $id) {
    try {
        // Kiểm tra công ty tồn tại
        $checkCompany = $pdo->prepare("SELECT company_id FROM companies WHERE company_id = ?");
        $checkCompany->execute([$id]);
        if ($checkCompany->rowCount() == 0) {
            jsonResponse(false, "Không tìm thấy công ty");
        }
        
        // Kiểm tra xem có khách hàng nào thuộc công ty này không
        $checkCustomers = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE company_id = ?");
        $checkCustomers->execute([$id]);
        $customerCount = $checkCustomers->fetchColumn();
        
        if ($customerCount > 0) {
            // Cập nhật company_id thành NULL cho các khách hàng thuộc công ty này
            $updateCustomers = $pdo->prepare("UPDATE customers SET company_id = NULL WHERE company_id = ?");
            $updateCustomers->execute([$id]);
        }
        
        // Xóa công ty
        $sql = "DELETE FROM companies WHERE company_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        jsonResponse(true, "Xóa công ty thành công");
    } catch (PDOException $e) {
        jsonResponse(false, "Lỗi khi xóa công ty: " . $e->getMessage());
    }
}
?>