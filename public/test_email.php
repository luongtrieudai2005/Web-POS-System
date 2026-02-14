<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../core/Mailer.php';

Auth::requireAdmin();

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Test ket noi Email</h4>
                    </div>
                    
                    <div class="card-body">
                        <?php
                        if (Helper::isPost()) {
                            $testEmail = Helper::post('test_email');
                            
                            try {
                                $mailer = new Mailer();
                                
                                $subject = 'Test Email tu ' . APP_NAME;
                                $body = '
                                    <h2>Test Email thanh cong!</h2>
                                    <p>Neu ban nhan duoc email nay tuc la cau hinh SMTP da dung.</p>
                                    <p><strong>Thoi gian:</strong> ' . date('d/m/Y H:i:s') . '</p>
                                ';
                                
                                $result = $mailer->send($testEmail, $subject, $body);
                                
                                if ($result) {
                                    echo '<div class="alert alert-success">';
                                    echo 'Gui email thanh cong den: <strong>' . Helper::escape($testEmail) . '</strong>';
                                    echo '<br>Vui long kiem tra hop thu cua ban.';
                                    echo '</div>';
                                } else {
                                    echo '<div class="alert alert-danger">';
                                    echo 'Khong gui duoc email. Vui long kiem tra cau hinh SMTP.';
                                    echo '</div>';
                                }
                            } catch (Exception $e) {
                                echo '<div class="alert alert-danger">';
                                echo '<strong>Loi:</strong> ' . Helper::escape($e->getMessage());
                                echo '</div>';
                            }
                        }
                        ?>
                        
                        <h5>Cau hinh hien tai:</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">MAIL_HOST</th>
                                <td><?php echo MAIL_HOST; ?></td>
                            </tr>
                            <tr>
                                <th>MAIL_PORT</th>
                                <td><?php echo MAIL_PORT; ?></td>
                            </tr>
                            <tr>
                                <th>MAIL_USERNAME</th>
                                <td><?php echo MAIL_USERNAME; ?></td>
                            </tr>
                            <tr>
                                <th>MAIL_PASSWORD</th>
                                <td>***hidden***</td>
                            </tr>
                        </table>
                        
                        <form method="POST" class="mt-4">
                            <div class="mb-3">
                                <label for="test_email" class="form-label">Nhap email de test:</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="test_email" 
                                       name="test_email" 
                                       placeholder="your-email@gmail.com"
                                       required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Gui email test</button>
                            <a href="<?php echo Router::url('dashboard'); ?>" class="btn btn-secondary">Quay lai</a>
                        </form>
                        
                        <hr class="my-4">
                        
                        <h6>Huong dan cau hinh Gmail App Password:</h6>
                        <ol class="small">
                            <li>Truy cap: <a href="https://myaccount.google.com/security" target="_blank">https://myaccount.google.com/security</a></li>
                            <li>Bat "2-Step Verification"</li>
                            <li>Tao "App Password" (chon Mail, Other)</li>
                            <li>Copy mat khau 16 ky tu</li>
                            <li>Cap nhat vao <code>config/app.php</code></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
