<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doanh số nhân viên - <?php echo APP_NAME; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo Router::url('assets/css/pos-styles.css'); ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        .employee-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e0e0e0;
        }
        .employee-avatar-placeholder {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
        }
        .rank-badge {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }
        .rank-1 { background: #FFD700; color: #7a6000; }
        .rank-2 { background: #C0C0C0; color: #555; }
        .rank-3 { background: #CD7F32; color: #fff; }
        .rank-other { background: #f0f0f0; color: #888; }
        .progress-bar-custom {
            height: 6px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        .detail-section { display: none; }
        .detail-section.show { display: table-row; }
    </style>
</head>
<body>
<?php
    $activePage = 'users';
    require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="container content-wrapper">

    <!-- Flash messages -->
    <?php if (Session::hasFlash('success')): $f = Session::getFlash('success'); ?>
        <div class="alert alert-<?php echo $f['type']; ?> alert-dismissible fade show mb-4">
            <?php echo Helper::escape($f['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Doanh số nhân viên</h5>
            <p class="text-muted small mb-0">
                Nhân viên: <strong><?php echo Helper::escape($employee['full_name']); ?></strong>
                &nbsp;|&nbsp; <?php echo Helper::escape($employee['email']); ?>
                &nbsp;|&nbsp;
                <?php if ($employee['role'] === 'admin'): ?>
                    <span class="badge bg-danger">Quản trị viên</span>
                <?php else: ?>
                    <span class="badge bg-primary">Nhân viên bán hàng</span>
                <?php endif; ?>
            </p>
        </div>
        <a href="<?php echo Router::url('users/detail.php?id=' . $employee['id']); ?>"
           class="btn btn-outline-secondary">
            Quay lại
        </a>
    </div>

    <!-- Bộ lọc thời gian -->
    <div class="main-card card mb-4">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <?php
                $ranges = [
                    'today'      => 'Hôm nay',
                    'yesterday'  => 'Hôm qua',
                    '7days'      => '7 ngày qua',
                    'this_month' => 'Tháng này',
                    'custom'     => 'Tùy chọn',
                ];
                foreach ($ranges as $key => $label): ?>
                    <a href="?id=<?php echo $employee['id']; ?>&range=<?php echo $key; ?>"
                       class="btn btn-sm btn-outline-primary <?php echo $range === $key ? 'active' : ''; ?>"
                       style="border-radius:8px;">
                        <?php echo $label; ?>
                    </a>
                <?php endforeach; ?>

                <?php if ($range === 'custom'): ?>
                    <form method="GET" class="d-flex gap-2 align-items-center ms-2">
                        <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">
                        <input type="hidden" name="range" value="custom">
                        <input type="date" name="date_from" class="form-control form-control-sm"
                               value="<?php echo $dateFrom; ?>">
                        <span>-</span>
                        <input type="date" name="date_to" class="form-control form-control-sm"
                               value="<?php echo $dateTo; ?>">
                        <button type="submit" class="btn btn-sm btn-primary-grad">Xem</button>
                    </form>
                <?php endif; ?>

                <span class="ms-auto text-muted small">
                    <?php echo date('d/m/Y', strtotime($dateFrom)); ?> →
                    <?php echo date('d/m/Y', strtotime($dateTo)); ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#667eea,#764ba2);">₫</div>
                <div class="stat-label">Doanh thu</div>
                <div class="stat-value" style="font-size:18px;">
                    <?php echo Helper::formatMoney($summary['total_revenue'] ?? 0); ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#4facfe,#00f2fe);">#</div>
                <div class="stat-label">Số đơn hàng</div>
                <div class="stat-value"><?php echo number_format($summary['total_orders'] ?? 0); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#43e97b,#38f9d7);">×</div>
                <div class="stat-label">Sản phẩm đã bán</div>
                <div class="stat-value"><?php echo number_format($summary['total_items'] ?? 0); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#f093fb,#f5576c);">+</div>
                <div class="stat-label">Lợi nhuận</div>
                <div class="stat-value" style="font-size:18px;">
                    <?php echo Helper::formatMoney($summary['total_profit'] ?? 0); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ doanh thu theo ngày -->
    <div class="main-card card mb-4">
        <div class="card-header">
            <h6 class="mb-0 fw-bold">Doanh thu theo ngày</h6>
        </div>
        <div class="card-body">
            <canvas id="revenueChart" height="100"></canvas>
        </div>
    </div>

    <!-- Danh sách đơn hàng -->
    <div class="main-card card">
        <div class="card-header">
            <h6 class="mb-0 fw-bold">
                Danh sách đơn hàng
                <span class="badge bg-secondary ms-1"><?php echo count($orders); ?></span>
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>SĐT</th>
                        <th class="text-center">Số SP</th>
                        <th class="text-end">Tổng tiền</th>
                        <th class="text-end">Lợi nhuận</th>
                        <th>Thời gian</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Không có đơn hàng nào trong khoảng thời gian này
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><code><?php echo Helper::escape($order['order_code']); ?></code></td>
                                <td><?php echo Helper::escape($order['customer_name']); ?></td>
                                <td><?php echo Helper::escape($order['customer_phone']); ?></td>
                                <td class="text-center"><?php echo $order['total_items']; ?></td>
                                <td class="text-end text-success fw-bold">
                                    <?php echo Helper::formatMoney($order['total_amount']); ?>
                                </td>
                                <td class="text-end text-primary">
                                    <?php echo Helper::formatMoney($order['total_profit']); ?>
                                </td>
                                <td><?php echo Helper::formatDate($order['created_at'], 'd/m/Y H:i'); ?></td>
                                <td>
                                    <a href="<?php echo Router::url('reports/order_detail.php?id=' . $order['id']); ?>"
                                       class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const chartData = <?php echo json_encode($chartData); ?>;
const labels   = chartData.map(d => { const p = d.date.split('-'); return p[2]+'/'+p[1]; });
const revenues = chartData.map(d => d.revenue);

new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: labels.length ? labels : ['Chưa có dữ liệu'],
        datasets: [{
            label: 'Doanh thu',
            data: revenues.length ? revenues : [0],
            backgroundColor: 'rgba(102,126,234,0.6)',
            borderColor: '#667eea',
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: v => new Intl.NumberFormat('vi-VN').format(v) + ' ₫' }
            }
        }
    }
});
</script>
</body>
</html>
