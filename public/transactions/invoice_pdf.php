<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../models/Order.php';

Auth::requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$order = Order::getById($id);
if (!$order) {
    Session::setFlash('error', 'Không tìm thấy đơn hàng', 'danger');
    Router::redirect(Router::url('transactions/index.php'));
    exit;
}

if (!Auth::isAdmin() && $order['employee_id'] != Auth::id()) {
    http_response_code(403);
    exit('Không có quyền truy cập');
}

$details = Order::getDetails($id);

$tcpdfPath = __DIR__ . '/../../libraries/TCPDF/tcpdf.php';

if (file_exists($tcpdfPath)) {
    require_once $tcpdfPath;

    $pdf = new TCPDF('P', 'mm', array(80, 220), true, 'UTF-8', false);
    $pdf->SetCreator(APP_NAME);
    $pdf->SetAuthor(APP_NAME);
    $pdf->SetTitle('Hóa đơn ' . $order['order_code']);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(5, 5, 5);
    $pdf->SetAutoPageBreak(true, 5);
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 9);

    $rows = '';
    foreach ($details as $d) {
        $rows .= '<tr>
            <td style="padding:2px 3px;">' . htmlspecialchars($d['product_name']) . '</td>
            <td style="text-align:center;padding:2px 3px;">' . $d['quantity'] . '</td>
            <td style="text-align:right;padding:2px 3px;">' . number_format($d['unit_price'], 0, ',', '.') . ' ₫</td>
            <td style="text-align:right;padding:2px 3px;">' . number_format($d['subtotal'], 0, ',', '.') . ' ₫</td>
        </tr>';
    }

    $createdAt = date('d/m/Y H:i', strtotime($order['created_at']));

    $html = '
    <div style="text-align:center;font-weight:bold;font-size:13pt;">' . APP_NAME . '</div>
    <div style="text-align:center;font-size:9pt;">HÓA ĐƠN BÁN HÀNG</div>
    <hr/>
    <table width="100%" style="font-size:8pt;">
        <tr><td width="40%"><b>Mã đơn:</b></td><td>' . htmlspecialchars($order['order_code']) . '</td></tr>
        <tr><td><b>Thời gian:</b></td><td>' . $createdAt . '</td></tr>
        <tr><td><b>Khách hàng:</b></td><td>' . htmlspecialchars($order['customer_name']) . '</td></tr>
        <tr><td><b>SĐT:</b></td><td>' . htmlspecialchars($order['customer_phone']) . '</td></tr>
        <tr><td><b>Nhân viên:</b></td><td>' . htmlspecialchars($order['employee_name']) . '</td></tr>
    </table>
    <hr/>
    <table width="100%" style="font-size:8pt;" cellpadding="2">
        <thead>
            <tr style="background-color:#eeeeee;">
                <th width="44%">Sản phẩm</th>
                <th width="11%" style="text-align:center;">SL</th>
                <th width="22%" style="text-align:right;">Đơn giá</th>
                <th width="23%" style="text-align:right;">Thành tiền</th>
            </tr>
        </thead>
        <tbody>' . $rows . '</tbody>
    </table>
    <hr/>
    <table width="100%" style="font-size:9pt;">
        <tr>
            <td width="55%"><b>Tổng tiền:</b></td>
            <td style="text-align:right;"><b>' . number_format($order['total_amount'], 0, ',', '.') . ' ₫</b></td>
        </tr>
        <tr>
            <td>Khách đưa:</td>
            <td style="text-align:right;">' . number_format($order['amount_paid'], 0, ',', '.') . ' ₫</td>
        </tr>
        <tr>
            <td>Tiền thừa:</td>
            <td style="text-align:right;">' . number_format($order['change_amount'], 0, ',', '.') . ' ₫</td>
        </tr>
    </table>
    <hr/>
    <div style="text-align:center;font-size:8pt;">Cảm ơn quý khách! Hẹn gặp lại.</div>
    ';

    $pdf->writeHTML($html, true, false, true, false, '');
    $filename = 'hoa_don_' . $order['order_code'] . '.pdf';
    $pdf->Output($filename, 'D');

} else {
    // Fallback: In HTML khi chưa cài TCPDF
    $rows = '';
    foreach ($details as $d) {
        $rows .= '<tr>
            <td>' . htmlspecialchars($d['product_name']) . '</td>
            <td class="c">' . $d['quantity'] . '</td>
            <td class="r">' . number_format($d['unit_price'], 0, ',', '.') . ' ₫</td>
            <td class="r">' . number_format($d['subtotal'], 0, ',', '.') . ' ₫</td>
        </tr>';
    }
    $createdAt = date('d/m/Y H:i', strtotime($order['created_at']));
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Hóa đơn <?php echo htmlspecialchars($order['order_code']); ?></title>
        <style>
            body { font-family: Arial, 'DejaVu Sans', sans-serif; max-width: 320px; margin: 20px auto; font-size: 12px; line-height: 1.4; }
            .center { text-align: center; }
            hr { border: 1px dashed #aaa; margin: 8px 0; }
            table { width: 100%; border-collapse: collapse; }
            td, th { padding: 3px 4px; }
            .r { text-align: right; }
            .c { text-align: center; }
            .bold { font-weight: bold; }
            .big { font-size: 14px; }
            .notice { background: #fff3cd; border: 1px solid #ffc107; padding: 8px; border-radius: 4px; font-size: 11px; margin-bottom: 12px; }
            @media print { 
                .no-print { display: none !important; } 
                body { margin: 0; max-width: 100%; font-size: 10pt; } 
            }
        </style>
    </head>
    <body>
        <div class="no-print">
            <div class="notice">
                <b>TCPDF chưa được cài đặt.</b><br>
                Hiện đang hiển thị bản in HTML (dễ in trực tiếp từ trình duyệt).<br>
                Để xuất file PDF chuyên nghiệp: Tải TCPDF và đặt vào thư mục <code>libraries/TCPDF/</code>
            </div>
            <div class="center" style="margin-bottom:16px;">
                <button onclick="window.print()" style="padding:8px 20px; cursor:pointer; font-size:13px; background:#007bff; color:white; border:none; border-radius:4px;">In hóa đơn</button>
                <button onclick="window.close()" style="padding:8px 20px; margin-left:12px; cursor:pointer; font-size:13px;">Đóng</button>
            </div>
        </div>

        <div class="center bold big" style="font-size:16px; margin-bottom:4px;"><?php echo APP_NAME; ?></div>
        <div class="center" style="font-size:11px; margin-bottom:8px;">HÓA ĐƠN BÁN HÀNG</div>
        <hr>
        <table>
            <tr><td class="bold">Mã đơn:</td><td><?php echo htmlspecialchars($order['order_code']); ?></td></tr>
            <tr><td class="bold">Thời gian:</td><td><?php echo $createdAt; ?></td></tr>
            <tr><td class="bold">Khách hàng:</td><td><?php echo htmlspecialchars($order['customer_name']); ?></td></tr>
            <tr><td class="bold">SĐT:</td><td><?php echo htmlspecialchars($order['customer_phone']); ?></td></tr>
            <tr><td class="bold">Nhân viên:</td><td><?php echo htmlspecialchars($order['employee_name']); ?></td></tr>
        </table>
        <hr>
        <table>
            <thead>
                <tr style="background:#f5f5f5;">
                    <th style="text-align:left;">Sản phẩm</th>
                    <th class="c">SL</th>
                    <th class="r">Đơn giá</th>
                    <th class="r">Thành tiền</th>
                </tr>
            </thead>
            <tbody><?php echo $rows; ?></tbody>
        </table>
        <hr>
        <table>
            <tr>
                <td class="bold big">Tổng tiền:</td>
                <td class="r bold big"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> ₫</td>
            </tr>
            <tr>
                <td>Khách đưa:</td>
                <td class="r"><?php echo number_format($order['amount_paid'], 0, ',', '.'); ?> ₫</td>
            </tr>
            <tr>
                <td>Tiền thừa:</td>
                <td class="r"><?php echo number_format($order['change_amount'], 0, ',', '.'); ?> ₫</td>
            </tr>
        </table>
        <hr>
        <div class="center" style="font-size:11px; margin-top:12px;">Cảm ơn quý khách! Hẹn gặp lại.</div>
    </body>
    </html>
    <?php
}
exit;