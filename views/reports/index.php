<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo Router::url('assets/css/pos-styles.css'); ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        .range-btn { border-radius: 8px; margin: 2px; }
        .range-btn.active { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; border-color: transparent; }
        .order-row { cursor: pointer; }
        .order-row:hover { background: #f8f9fa; }
    </style>
</head>
<body>
<?php $activePage = 'reports'; require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container content-wrapper">
    <!-- Bộ lọc thời gian -->
    <div class="main-card card mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <?php
                $ranges = [
                    'today'     => 'Hôm nay',
                    'yesterday' => 'Hôm qua',
                    '7days'     => '7 ngày qua',
                    'this_month'=> 'Tháng này',
                    'custom'    => 'Tùy chọn'
                ];
                foreach ($ranges as $key => $label): ?>
                    <a href="?range=<?php echo $key; ?>"
                       class="btn btn-outline-primary range-btn <?php echo $range === $key ? 'active' : ''; ?>">
                        <?php echo $label; ?>
                    </a>
                <?php endforeach; ?>

                <?php if ($range === 'custom'): ?>
                    <form method="GET" class="d-flex gap-2 align-items-center ms-2">
                        <input type="hidden" name="range" value="custom">
                        <input type="date" name="date_from" class="form-control form-control-sm"
                               value="<?php echo $dateFrom; ?>">
                        <span>-</span>
                        <input type="date" name="date_to" class="form-control form-control-sm"
                               value="<?php echo $dateTo; ?>">
                        <button type="submit" class="btn btn-sm btn-primary-grad">Xem báo cáo</button>
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
                <div class="stat-icon" style="background: linear-gradient(135deg,#667eea,#764ba2);">₫</div>
                <div class="stat-label">Doanh thu</div>
                <div class="stat-value" style="font-size:20px;">
                    <?php echo Helper::formatMoney($report['summary']['total_revenue']); ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg,#4facfe,#00f2fe);">#</div>
                <div class="stat-label">Đơn hàng</div>
                <div class="stat-value"><?php echo number_format($report['summary']['total_orders']); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg,#43e97b,#38f9d7);">×</div>
                <div class="stat-label">Sản phẩm đã bán</div>
                <div class="stat-value"><?php echo number_format($report['summary']['total_items']); ?></div>
            </div>
        </div>
        <?php if (Auth::isAdmin()): ?>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg,#f093fb,#f5576c);">+</div>
                <div class="stat-label">Lợi nhuận</div>
                <div class="stat-value" style="font-size:20px;">
                    <?php echo Helper::formatMoney($report['summary']['total_profit']); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <!-- Biểu đồ doanh thu -->
        <div class="col-md-8">
            <div class="main-card card">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">Doanh thu theo ngày</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Top sản phẩm bán chạy -->
        <div class="col-md-4">
            <div class="main-card card h-100">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">Top sản phẩm bán chạy</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($topProducts)): ?>
                        <div class="text-center text-muted py-4">Chưa có dữ liệu</div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($topProducts as $i => $p): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-secondary me-2"><?php echo $i + 1; ?></span>
                                        <span style="font-size:13px;"><?php echo Helper::escape($p['name']); ?></span>
                                    </div>
                                    <span class="badge bg-primary"><?php echo $p['total_qty']; ?> cái</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách đơn hàng -->
    <div class="main-card card mt-4">
        <div class="card-header">
            <h6 class="mb-0 fw-bold">Danh sách đơn hàng (<?php echo count($report['orders']); ?> đơn)</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>SĐT</th>
                            <?php if (Auth::isAdmin()): ?><th>Nhân viên</th><?php endif; ?>
                            <th>Số lượng</th>
                            <th>Tổng tiền</th>
                            <th>Thời gian</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($report['orders'])): ?>
                            <tr><td colspan="<?php echo Auth::isAdmin() ? 8 : 7; ?>" class="text-center py-4 text-muted">Không có đơn hàng nào trong khoảng thời gian này</td></tr>
                        <?php else: ?>
                            <?php foreach ($report['orders'] as $order): ?>
                                <tr class="order-row">
                                    <td><code><?php echo Helper::escape($order['order_code']); ?></code></td>
                                    <td><?php echo Helper::escape($order['customer_name']); ?></td>
                                    <td><?php echo Helper::escape($order['customer_phone']); ?></td>
                                    <?php if (Auth::isAdmin()): ?>
                                        <td><?php echo Helper::escape($order['employee_name']); ?></td>
                                    <?php endif; ?>
                                    <td><?php echo $order['total_items']; ?></td>
                                    <td class="text-success fw-bold"><?php echo Helper::formatMoney($order['total_amount']); ?></td>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const chartData = <?php echo json_encode($chartData); ?>;
const labels = chartData.map(d => {
    const parts = d.date.split('-');
    return parts[2] + '/' + parts[1];
});
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
                ticks: {
                    callback: v => new Intl.NumberFormat('vi-VN').format(v) + ' ₫'
                }
            }
        }
    }
});
</script>
</body>
</html>