<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khách hàng - <?php echo APP_NAME; ?></title>

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

    <!-- Flash message -->
    <?php if (Session::hasFlash('success')): 
        $f = Session::getFlash('success'); ?>
        <div class="alert alert-<?php echo $f['type']; ?> alert-dismissible fade show flash-message mb-4">
            <?php echo Helper::escape($f['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="main-card card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Danh sách khách hàng</h5>
        </div>

        <div class="card-body">

            <!-- Form tìm kiếm -->
            <form method="GET" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Tìm theo số điện thoại hoặc tên..."
                               value="<?php echo Helper::escape($search ?? ''); ?>">
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary-grad">
                            Tìm kiếm
                        </button>

                        <a href="?" class="btn btn-outline-secondary ms-1">
                            Xóa lọc
                        </a>
                    </div>
                </div>
            </form>

            <!-- Bảng danh sách khách hàng -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Họ tên</th>
                            <th>Số điện thoại</th>
                            <th>Địa chỉ</th>
                            <th class="text-center">Số đơn hàng</th>
                            <th class="text-end">Tổng chi tiêu</th>
                            <th>Ngày tạo</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="7"
                                    class="text-center py-4 text-muted">
                                    Chưa có khách hàng nào
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customers as $c): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?php echo Helper::escape($c['full_name']); ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?php echo Helper::escape($c['phone']); ?>
                                    </td>

                                    <td>
                                        <?php echo Helper::escape($c['address'] ?? '-'); ?>
                                    </td>

                                    <td class="text-center">
                                        <?php echo $c['total_orders']; ?>
                                    </td>

                                    <td class="text-end text-success">
                                        <?php echo Helper::formatMoney($c['total_spent']); ?>
                                    </td>

                                    <td>
                                        <?php echo Helper::formatDate($c['created_at'], 'd/m/Y'); ?>
                                    </td>

                                    <td>
                                        <a href="<?php echo Router::url('customers/detail.php?id=' . $c['id']); ?>"
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

            <!-- Tổng số -->
            <p class="text-muted mb-0 mt-2">
                Tổng số:
                <strong><?php echo count($customers); ?></strong>
                khách hàng
            </p>

        </div>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>