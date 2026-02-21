<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bán hàng POS - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo Router::url('assets/css/pos-styles.css'); ?>" rel="stylesheet">
    <style>
        .pos-layout { display: flex; height: calc(100vh - 60px); overflow: hidden; }
        .pos-left   { flex: 1; overflow-y: auto; padding: 20px; background: #f0f2f5; }
        .pos-right  { width: 400px; min-width: 340px; background: #fff; border-left: 1px solid #e0e0e0; display: flex; flex-direction: column; }

        .cart-header { padding: 16px 20px; border-bottom: 2px solid #f0f0f0; }
        .cart-body   { flex: 1; overflow-y: auto; padding: 10px; }
        .cart-footer { padding: 16px 20px; border-top: 2px solid #f0f0f0; background: #fafafa; }

        .cart-item { background: #fff; border: 1px solid #e8e8e8; border-radius: 10px; padding: 12px; margin-bottom: 8px; }
        .cart-item:hover { border-color: #667eea; }

        .qty-control { display: flex; align-items: center; gap: 6px; }
        .qty-btn { width: 28px; height: 28px; border: 1px solid #dee2e6; background: #f8f9fa; border-radius: 6px; cursor: pointer; font-size: 16px; line-height: 1; display: flex; align-items: center; justify-content: center; }
        .qty-btn:hover { background: #667eea; color: #fff; border-color: #667eea; }
        .qty-input { width: 50px; text-align: center; border: 1px solid #dee2e6; border-radius: 6px; padding: 3px; }

        .total-amount { font-size: 26px; font-weight: 700; color: #28a745; }
        .change-amount { font-size: 20px; font-weight: 600; color: #667eea; }

        .empty-cart { text-align: center; padding: 40px 20px; color: #aaa; }
        .section-card { background: #fff; border-radius: 12px; padding: 20px; margin-bottom: 16px; box-shadow: 0 1px 6px rgba(0,0,0,0.07); }

        #searchDropdown { position: absolute; z-index: 1000; background: #fff; border: 1px solid #ddd; border-radius: 10px; width: 100%; max-height: 280px; overflow-y: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: none; }
        .search-wrapper { position: relative; }
        .drop-item { padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; }
        .drop-item:hover { background: #f0f2f5; }
    </style>
</head>
<body>
<?php $activePage = 'pos'; require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="pos-layout">
    <div class="pos-left">
        <?php if (Session::hasFlash('success')): $f = Session::getFlash('success'); ?>
            <div class="alert alert-<?php echo $f['type']; ?> alert-dismissible fade show flash-message">
                <?php echo Helper::escape($f['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="section-card">
            <h6 class="fw-bold mb-3">Tìm kiếm sản phẩm</h6>
            <div class="search-wrapper mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Nhập tên sản phẩm để tìm kiếm...">
                <div id="searchDropdown"></div>
            </div>
            <div class="d-flex gap-2">
                <input type="text" id="barcodeInput" class="form-control" placeholder="Quét hoặc nhập mã vạch rồi nhấn Enter">
                <button class="btn btn-outline-secondary" onclick="lookupBarcode()">Thêm</button>
            </div>
        </div>

        <div class="section-card">
            <h6 class="fw-bold mb-3">Thông tin khách hàng</h6>
            <div class="row g-2">
                <div class="col-md-5">
                    <label class="form-label small fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2">
                        <input type="text" id="customerPhone" class="form-control" placeholder="0912345678">
                        <button class="btn btn-outline-primary btn-sm" onclick="lookupCustomer()">Tìm</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Họ tên <span class="text-danger">*</span></label>
                    <input type="text" id="customerName" class="form-control" placeholder="Nguyễn Văn A">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Địa chỉ</label>
                    <input type="text" id="customerAddress" class="form-control" placeholder="Hà Nội">
                </div>
            </div>
            <div id="customerInfo" class="mt-2" style="display:none;">
                <span class="badge bg-success">Khách quen</span>
                <span id="customerFound" class="text-success small"></span>
            </div>
        </div>

        <div class="section-card">
            <h6 class="fw-bold mb-3">Thanh toán</h6>
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-bold">Tổng tiền</label>
                    <div class="form-control bg-light fw-bold text-success" id="displayTotal">0 ₫</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Khách đưa <span class="text-danger">*</span></label>
                    <input type="number" id="amountPaid" class="form-control" placeholder="0" min="0" oninput="calcChange()">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Tiền thừa</label>
                    <div class="form-control bg-light fw-bold text-primary" id="displayChange">0 ₫</div>
                </div>
            </div>
        </div>
    </div>

    <div class="pos-right">
        <div class="cart-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">Giỏ hàng</h6>
            <span id="cartCount" class="badge bg-secondary">0 sản phẩm</span>
        </div>

        <div class="cart-body" id="cartBody">
            <div class="empty-cart" id="emptyCart">
                <div style="font-size:40px;">🛒</div>
                <p class="mt-2">Giỏ hàng trống<br><small>Tìm sản phẩm để thêm vào</small></p>
            </div>
        </div>

        <div class="cart-footer">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold">Tổng cộng:</span>
                <span class="total-amount" id="totalAmount">0 ₫</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold">Tiền thừa:</span>
                <span class="change-amount" id="changeAmount">0 ₫</span>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger flex-fill" onclick="clearCart()">Xóa giỏ</button>
                <button class="btn btn-primary-grad flex-fill" onclick="processCheckout()">Thanh toán</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal hóa đơn -->
<div class="modal fade" id="invoiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hóa đơn thanh toán</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="invoiceContent"></div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetAfterCheckout()">Giao dịch mới</button>
                <a id="btnExportPdf" href="#" target="_blank" class="btn btn-danger">Xuất PDF</a>
                <button class="btn btn-primary-grad" onclick="printInvoice()">In hóa đơn</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const BASE_URL = '<?php echo Router::url(''); ?>';
let cart = {};
let searchTimeout = null;
let lastOrderId = null;

function formatMoney(n) {
    return new Intl.NumberFormat('vi-VN').format(n) + ' ₫';
}

// Tìm kiếm sản phẩm
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const kw = this.value.trim();
    if (kw.length < 1) { document.getElementById('searchDropdown').style.display = 'none'; return; }
    searchTimeout = setTimeout(() => searchProducts(kw), 300);
});

document.getElementById('searchInput').addEventListener('blur', function() {
    setTimeout(() => { document.getElementById('searchDropdown').style.display = 'none'; }, 200);
});

function searchProducts(kw) {
    fetch(BASE_URL + 'transactions/search.php?keyword=' + encodeURIComponent(kw))
        .then(r => r.json())
        .then(data => {
            const dd = document.getElementById('searchDropdown');
            if (!data.length) { dd.style.display = 'none'; return; }
            dd.innerHTML = data.map(p => {
                const encoded = encodeURIComponent(JSON.stringify(p));
                return '<div class="drop-item" onclick=\'addToCart(' + JSON.stringify(p).replace(/'/g, '&#39;') + ')\'>'
                    + '<div><strong>' + p.name + '</strong><br>'
                    + '<small class="text-muted">' + p.barcode + ' | Tồn: ' + p.stock_quantity + '</small></div>'
                    + '<span class="text-success fw-bold">' + formatMoney(p.retail_price) + '</span>'
                    + '</div>';
            }).join('');
            dd.style.display = 'block';
        });
}

function lookupBarcode() {
    const barcode = document.getElementById('barcodeInput').value.trim();
    if (!barcode) return;
    fetch(BASE_URL + 'transactions/barcode.php?barcode=' + encodeURIComponent(barcode))
        .then(r => r.json())
        .then(data => {
            if (data.error) { alert(data.error); return; }
            addToCart(data);
            document.getElementById('barcodeInput').value = '';
        });
}

document.getElementById('barcodeInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') lookupBarcode();
});

function addToCart(product) {
    const id = product.id;
    if (cart[id]) {
        if (cart[id].qty >= product.stock_quantity) {
            alert('Đã đạt giới hạn tồn kho (' + product.stock_quantity + ')');
            return;
        }
        cart[id].qty++;
    } else {
        cart[id] = Object.assign({}, product, { qty: 1 });
    }
    renderCart();
    document.getElementById('searchInput').value = '';
    document.getElementById('searchDropdown').style.display = 'none';
}

function updateQty(id, val) {
    const qty = parseInt(val);
    if (qty <= 0) { removeFromCart(id); return; }
    if (qty > cart[id].stock_quantity) {
        alert('Số lượng vượt quá tồn kho (' + cart[id].stock_quantity + ')');
        cart[id].qty = cart[id].stock_quantity;
    } else {
        cart[id].qty = qty;
    }
    renderCart();
}

function removeFromCart(id) {
    delete cart[id];
    renderCart();
}

function clearCart() {
    if (!Object.keys(cart).length) return;
    if (confirm('Xóa toàn bộ giỏ hàng?')) { cart = {}; renderCart(); }
}

function renderCart() {
    const body = document.getElementById('cartBody');
    const items = Object.values(cart);

    if (!items.length) {
        body.innerHTML = '<div class="empty-cart"><div style="font-size:40px;">🛒</div><p class="mt-2">Giỏ hàng trống<br><small>Tìm sản phẩm để thêm vào</small></p></div>';
        document.getElementById('cartCount').textContent = '0 sản phẩm';
        document.getElementById('totalAmount').textContent = '0 ₫';
        document.getElementById('displayTotal').textContent = '0 ₫';
        calcChange();
        return;
    }

    let total = 0;
    let html = '';
    items.forEach(function(item) {
        const sub = item.retail_price * item.qty;
        total += sub;
        html += '<div class="cart-item">'
            + '<div class="d-flex justify-content-between align-items-start mb-1">'
            + '<strong style="font-size:13px;flex:1;">' + item.name + '</strong>'
            + '<button class="btn btn-sm text-danger p-0 ms-2" onclick="removeFromCart(' + item.id + ')">✕</button>'
            + '</div>'
            + '<div class="d-flex justify-content-between align-items-center">'
            + '<div class="qty-control">'
            + '<button class="qty-btn" onclick="updateQty(' + item.id + ',' + (item.qty - 1) + ')">-</button>'
            + '<input class="qty-input" type="number" value="' + item.qty + '" min="1" max="' + item.stock_quantity + '" onchange="updateQty(' + item.id + ',this.value)">'
            + '<button class="qty-btn" onclick="updateQty(' + item.id + ',' + (item.qty + 1) + ')">+</button>'
            + '</div>'
            + '<div class="text-end">'
            + '<div class="text-muted small">' + formatMoney(item.retail_price) + ' × ' + item.qty + '</div>'
            + '<div class="fw-bold text-success">' + formatMoney(sub) + '</div>'
            + '</div></div></div>';
    });

    body.innerHTML = html;
    const totalItems = items.reduce(function(s, i) { return s + i.qty; }, 0);
    document.getElementById('cartCount').textContent = totalItems + ' sản phẩm';
    document.getElementById('totalAmount').textContent = formatMoney(total);
    document.getElementById('displayTotal').textContent = formatMoney(total);
    calcChange();
}

function calcChange() {
    const items = Object.values(cart);
    const total = items.reduce(function(s, i) { return s + i.retail_price * i.qty; }, 0);
    const paid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const change = paid - total;
    const display = formatMoney(change >= 0 ? change : 0);
    document.getElementById('displayChange').textContent = display;
    document.getElementById('changeAmount').textContent = display;
}

function lookupCustomer() {
    const phone = document.getElementById('customerPhone').value.trim();
    if (!phone) return;
    fetch(BASE_URL + 'transactions/customer_lookup.php?phone=' + encodeURIComponent(phone))
        .then(r => r.json())
        .then(data => {
            const info = document.getElementById('customerInfo');
            if (data.found) {
                document.getElementById('customerName').value = data.customer.full_name;
                document.getElementById('customerAddress').value = data.customer.address || '';
                document.getElementById('customerFound').textContent = ' ' + data.customer.full_name;
                info.style.display = 'block';
            } else {
                document.getElementById('customerName').value = '';
                document.getElementById('customerAddress').value = '';
                info.style.display = 'none';
            }
        });
}

function processCheckout() {
    const items = Object.values(cart);
    if (!items.length) { alert('Giỏ hàng trống!'); return; }

    const phone = document.getElementById('customerPhone').value.trim();
    const name = document.getElementById('customerName').value.trim();
    const address = document.getElementById('customerAddress').value.trim();
    const paid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const total = items.reduce(function(s, i) { return s + i.retail_price * i.qty; }, 0);

    if (!phone || !name) { alert('Vui lòng nhập số điện thoại và họ tên khách hàng!'); return; }
    if (paid < total) { alert('Số tiền khách đưa chưa đủ!'); return; }

    const payload = {
        items: items.map(function(i) { return { product_id: i.id, quantity: i.qty }; }),
        phone: phone,
        customer_name: name,
        customer_address: address,
        amount_paid: paid
    };

    fetch(BASE_URL + 'transactions/checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.error) { alert(data.error); return; }
        showInvoice(data);
    })
    .catch(function() { alert('Lỗi kết nối, vui lòng thử lại!'); });
}

function showInvoice(data) {
    const o = data.order;
    const details = data.details;
    lastOrderId = data.order_id;

    // Cập nhật link xuất PDF
    document.getElementById('btnExportPdf').href = BASE_URL + 'transactions/invoice_pdf.php?id=' + lastOrderId;

    let rows = details.map(function(d) {
        return '<tr>'
            + '<td>' + d.product_name + '</td>'
            + '<td class="text-center">' + d.quantity + '</td>'
            + '<td class="text-end">' + formatMoney(d.unit_price) + '</td>'
            + '<td class="text-end fw-bold">' + formatMoney(d.subtotal) + '</td>'
            + '</tr>';
    }).join('');

    const createdAt = new Date(o.created_at.replace(' ', 'T')).toLocaleString('vi-VN');

    document.getElementById('invoiceContent').innerHTML =
        '<div style="font-family:monospace;">'
        + '<div class="text-center mb-3">'
        + '<h5 class="fw-bold">' + '<?php echo APP_NAME; ?>' + '</h5>'
        + '<div class="text-muted small">HÓA ĐƠN BÁN HÀNG</div>'
        + '<div class="small">Mã đơn: <strong>' + o.order_code + '</strong></div>'
        + '<div class="small">' + createdAt + '</div>'
        + '</div><hr>'
        + '<div class="row mb-2">'
        + '<div class="col-6"><strong>Khách hàng:</strong><br>' + o.customer_name + '</div>'
        + '<div class="col-6"><strong>SĐT:</strong><br>' + o.customer_phone + '</div>'
        + '</div>'
        + '<div class="mb-2"><strong>Nhân viên:</strong> ' + o.employee_name + '</div><hr>'
        + '<table class="table table-sm"><thead><tr>'
        + '<th>Sản phẩm</th><th class="text-center">SL</th>'
        + '<th class="text-end">Đơn giá</th><th class="text-end">Thành tiền</th>'
        + '</tr></thead><tbody>' + rows + '</tbody></table><hr>'
        + '<div class="row"><div class="col-6 fw-bold">Tổng tiền:</div>'
        + '<div class="col-6 text-end fw-bold text-success">' + formatMoney(o.total_amount) + '</div></div>'
        + '<div class="row"><div class="col-6">Khách đưa:</div>'
        + '<div class="col-6 text-end">' + formatMoney(o.amount_paid) + '</div></div>'
        + '<div class="row"><div class="col-6">Tiền thừa:</div>'
        + '<div class="col-6 text-end text-primary fw-bold">' + formatMoney(o.change_amount) + '</div></div>'
        + '<hr><div class="text-center text-muted small">Cảm ơn quý khách! Hẹn gặp lại.</div>'
        + '</div>';

    new bootstrap.Modal(document.getElementById('invoiceModal')).show();
}

function printInvoice() {
    const content = document.getElementById('invoiceContent').innerHTML;
    const win = window.open('', '_blank');
    win.document.write('<html><head><title>Hóa đơn</title>'
        + '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">'
        + '</head><body style="padding:20px;">' + content + '</body></html>');
    win.document.close();
    win.print();
}

function resetAfterCheckout() {
    cart = {};
    lastOrderId = null;
    renderCart();
    document.getElementById('customerPhone').value = '';
    document.getElementById('customerName').value = '';
    document.getElementById('customerAddress').value = '';
    document.getElementById('amountPaid').value = '';
    document.getElementById('customerInfo').style.display = 'none';
    document.getElementById('btnExportPdf').href = '#';
}
</script>
</body>
</html>