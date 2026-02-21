<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết khách hàng - <?php echo APP_NAME; ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo Router::url('assets/css/pos-styles.css'); ?>" rel="stylesheet">
</head>

<body>
<?php
    $activePage = 'customers';
    require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="container content-wrapper">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">Chi tiết khách hàng</h5>
        <a href="<?php echo Router::url('customers/index.php'); ?>"
           class="btn btn-outline-secondary">
            Quay lại
        </a>
    </div>

    <div class="row g-4">

        <!-- Thông tin cá nhân -->
        <div class="col-md-4">
            <div class="main-card card">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">Thông tin cá nhân</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted">Họ tên</td>
                            <td>
                                <strong>
                                    <?php echo Helper::escape($customer['full_name']); ?>
                                </strong>
                            </td>
                        </tr>

                        <tr>
                            <td class="text-muted">Số điện thoại</td>
                            <td>
                                <?php echo Helper::escape($customer['phone']); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="text-muted">Địa chỉ</td>
                            <td>
                                <?php echo Helper::escape($customer['address'] ?? '-'); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="text-muted">Khách từ</td>
                            <td>
                                <?php echo Helper::formatDate($customer['created_at'], 'd/m/Y'); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="text-muted">Số đơn hàng</td>
                            <td>
                                <strong class="text-primary">
                                    <?php echo count($orders); ?>
                                </strong>
                            </td>
                        </tr>

                        <tr>
                            <td class="text-muted">Tổng chi</td>
                            <td>
                                <strong class="text-success">
                                    <?php echo Helper::formatMoney(
                                        array_sum(array_column($orders, 'total_amount'))
                                    ); ?>
                                </strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Lịch sử mua hàng -->
        <div class="col-md-8">
            <div class="main-card card">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">Lịch sử mua hàng</h6>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th class="text-center">Sản phẩm</th>
                                <th class="text-end">Tổng tiền</th>
                                <th class="text-end">Khách đưa</th>
                                <th class="text-end">Tiền thừa</th>
                                <th>Thời gian</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="7"
                                        class="text-center py-4 text-muted">
                                        Chưa có đơn hàng nào
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $o): ?>
                                    <tr>
                                        <td>
                                            <code>
                                                <?php echo Helper::escape($o['order_code']); ?>
                                            </code>
                                        </td>

                                        <td class="text-center">
                                            <?php echo $o['item_count']; ?> sản phẩm
                                        </td>

                                        <td class="text-end text-success fw-bold">
                                            <?php echo Helper::formatMoney($o['total_amount']); ?>
                                        </td>

                                        <td class="text-end">
                                            <?php echo Helper::formatMoney($o['amount_paid']); ?>
                                        </td>

                                        <td class="text-end">
                                            <?php echo Helper::formatMoney($o['change_amount']); ?>
                                        </td>

                                        <td>
                                            <?php echo Helper::formatDate($o['created_at'], 'd/m/Y H:i'); ?>
                                        </td>

                                        <td>
                                            <a href="<?php echo Router::url('reports/order_detail.php?id=' . $o['id']); ?>"
                                               class="btn btn-sm btn-outline-primary">
                                                Xem
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>