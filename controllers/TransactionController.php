<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Order.php';

class TransactionController {

    public static function index() {
        Auth::requireLogin();
        if (Auth::requirePasswordChange()) {
            Router::redirect(Router::url('first-login'));
            exit;
        }
        require_once __DIR__ . '/../views/transactions/index.php';
    }

    // API: Tim san pham (AJAX)
    public static function searchProducts() {
        Auth::requireLogin();
        header('Content-Type: application/json');
        $keyword = Helper::get('keyword', '');
        if (strlen($keyword) < 1) {
            echo json_encode([]);
            exit;
        }
        $products = Product::search($keyword, 10);
        $result = [];
        foreach ($products as $p) {
            $result[] = [
                'id' => $p['id'],
                'barcode' => $p['barcode'],
                'name' => $p['name'],
                'retail_price' => (float)$p['retail_price'],
                'import_price' => (float)$p['import_price'],
                'stock_quantity' => (int)$p['stock_quantity'],
                'category_name' => $p['category_name']
            ];
        }
        echo json_encode($result);
        exit;
    }

    // API: Tim san pham theo barcode (AJAX)
    public static function getByBarcode() {
        Auth::requireLogin();
        header('Content-Type: application/json');
        $barcode = Helper::get('barcode', '');
        if (!$barcode) {
            http_response_code(400);
            echo json_encode(['error' => 'Barcode không hợp lệ']);
            exit;
        }
        $product = Product::getByBarcode($barcode);
        if (!$product) {
            http_response_code(404);
            echo json_encode(['error' => 'Không tìm thấy sản phẩm']);
            exit;
        }
        if ($product['stock_quantity'] <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Sản phẩm đã hết hàng']);
            exit;
        }
        echo json_encode([
            'id' => $product['id'],
            'barcode' => $product['barcode'],
            'name' => $product['name'],
            'retail_price' => (float)$product['retail_price'],
            'import_price' => (float)$product['import_price'],
            'stock_quantity' => (int)$product['stock_quantity'],
            'category_name' => $product['category_name']
        ]);
        exit;
    }

    // API: Tim khach hang theo SDT (AJAX)
    public static function lookupCustomer() {
        Auth::requireLogin();
        header('Content-Type: application/json');
        $phone = Helper::get('phone', '');
        if (!$phone) {
            echo json_encode(['found' => false]);
            exit;
        }
        $customer = Customer::getByPhone($phone);
        if ($customer) {
            echo json_encode(['found' => true, 'customer' => $customer]);
        } else {
            echo json_encode(['found' => false]);
        }
        exit;
    }

    // Xu ly thanh toan
    public static function checkout() {
        Auth::requireLogin();
        if (!Helper::isPost()) {
            Router::redirect(Router::url('transactions/index.php'));
            exit;
        }

        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
            exit;
        }

        $items = $input['items'] ?? [];
        $phone = trim($input['phone'] ?? '');
        $customerName = trim($input['customer_name'] ?? '');
        $customerAddress = trim($input['customer_address'] ?? '');
        $amountPaid = (float)($input['amount_paid'] ?? 0);

        if (empty($items)) {
            http_response_code(400);
            echo json_encode(['error' => 'Giỏ hàng trống']);
            exit;
        }
        if (!$phone || !$customerName) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu thông tin khách hàng']);
            exit;
        }

        // Kiem tra va tinh toan
        $processedItems = [];
        $totalAmount = 0;
        foreach ($items as $item) {
            $product = Product::getById($item['product_id']);
            if (!$product) {
                http_response_code(400);
                echo json_encode(['error' => 'Sản phẩm không tồn tại: ' . $item['product_id']]);
                exit;
            }
            $qty = (int)$item['quantity'];
            if ($qty <= 0 || $product['stock_quantity'] < $qty) {
                http_response_code(400);
                echo json_encode(['error' => 'Số lượng không hợp lệ: ' . $product['name']]);
                exit;
            }
            $processedItems[] = [
                'product_id' => $product['id'],
                'quantity' => $qty,
                'unit_price' => (float)$product['retail_price'],
                'import_price' => (float)$product['import_price']
            ];
            $totalAmount += $product['retail_price'] * $qty;
        }

        if ($amountPaid < $totalAmount) {
            http_response_code(400);
            echo json_encode(['error' => 'Số tiền khách đưa không dư']);
            exit;
        }

        $customer = Customer::getOrCreate($phone, $customerName, $customerAddress);
        if (!$customer) {
            http_response_code(500);
            echo json_encode(['error' => 'Loi tao khach hang']);
            exit;
        }

        $changeAmount = $amountPaid - $totalAmount;
        $totalItems = array_sum(array_column($processedItems, 'quantity'));

        $orderId = Order::create([
            'customer_id' => $customer['id'],
            'total_amount' => $totalAmount,
            'amount_paid' => $amountPaid,
            'change_amount' => $changeAmount,
            'total_items' => $totalItems,
            'items' => $processedItems
        ]);

        $order = Order::getById($orderId);
        $details = Order::getDetails($orderId);

        echo json_encode([
            'success' => true,
            'order_id' => $orderId,
            'order' => $order,
            'details' => $details
        ]);
        exit;
    }

    // Hoa don (in)
    public static function invoice($orderId) {
        Auth::requireLogin();
        $order = Order::getById($orderId);
        if (!$order) {
            Session::setFlash('error', 'Không tìm thấy đơn hàng', 'danger');
            Router::redirect(Router::url('transactions/index.php'));
            exit;
        }
        $details = Order::getDetails($orderId);
        require_once __DIR__ . '/../views/transactions/invoice.php';
    }
}
