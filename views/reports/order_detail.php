<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng - <?php echo APP_NAME; ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo Router::url('assets/css/pos-styles.css'); ?>" rel="stylesheet">
</head>

<body>
<?php 
    $activePage = 'reports'; 
    require_once __DIR__ . '/../layouts/navbar.php'; 
?>

<div class="container content-wrapper">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">
            Chi tiết đơn hàng: 
            <code><?php echo Helper::escape($order['order_code']); ?></code>
        </h5>
        <a href="javascript:history.back()" class="btn btn-outline-secondary">
            Quay lại
        </a>
    </div>

    <div class="row g-4">

        <!-- Thông tin đơn hàng -->
        <div class="col-md-5">
            <div class="main-card card">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">Thông tin đơn hàng</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted">Mã đơn</td>
                            <td><code><?php echo Helper::escape($order['order_code']); ?></code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Khách hàng</td>
                            <td><?php echo Helper::escape($order['customer_name']); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Số điện thoại</td>
                            <td><?php echo Helper::escape($order['customer_phone']); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Địa chỉ</td>
                            <td><?php echo Helper::escape($order['customer_address'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nhân viên</td>
                            <td><?php echo Helper::escape($order['employee_name']); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Thời gian</td>
                            <td><?php echo Helper::formatDate($order['created_at'], 'd/m/Y H:i:s'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tổng kết thanh toán -->
        <div class="col-md-7">
            <div class="main-card card">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">Tổng kết thanh toán</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Tổng tiền hàng</span>
                        <strong class="text-success">
                            <?php echo Helper::formatMoney($order['total_amount']); ?>
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Khách đưa</span>
                        <span>
                            <?php echo Helper::formatMoney($order['amount_paid']); ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Tiền thừa</span>
                        <span class="text-primary fw-bold">
                            <?php echo Helper::formatMoney($order['change_amount']); ?>
                        </span>
                    </div>

                    <?php if (Auth::isAdmin()): ?>
                    <div class="d-flex justify-content-between py-2">
                        <span>Lợi nhuận</span>
                        <span class="text-danger fw-bold">
                            <?php echo Helper::formatMoney($order['total_profit']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách sản phẩm -->
    <div class="main-card card mt-4">
        <div class="card-header">
            <h6 class="mb-0 fw-bold">Danh sách sản phẩm</h6>
        </div>

        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Barcode</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-end">Thành tiền</th>
                        <?php if (Auth::isAdmin()): ?>
                            <th class="text-end">Lợi nhuận</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($details as $d): ?>
                        <tr>
                            <td><?php echo Helper::escape($d['product_name']); ?></td>
                            <td>
                                <code><?php echo Helper::escape($d['barcode']); ?></code>
                            </td>
                            <td class="text-center"><?php echo $d['quantity']; ?></td>
                            <td class="text-end">
                                <?php echo Helper::formatMoney($d['unit_price']); ?>
                            </td>
                            <td class="text-end fw-bold">
                                <?php echo Helper::formatMoney($d['subtotal']); ?>
                            </td>

                            <?php if (Auth::isAdmin()): ?>
                                <td class="text-end text-success">
                                    <?php echo Helper::formatMoney($d['profit']); ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>