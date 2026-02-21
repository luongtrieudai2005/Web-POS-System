<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../libraries/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../libraries/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libraries/PHPMailer/src/SMTP.php';

class Mailer {
    
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }
    
    private function configure() {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host       = MAIL_HOST;
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = MAIL_USERNAME;
            $this->mailer->Password   = MAIL_PASSWORD;
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port       = MAIL_PORT;
            $this->mailer->CharSet    = 'UTF-8';
            
            $this->mailer->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
            
            if (APP_DEBUG) {
                $this->mailer->SMTPDebug = 0;
            }
            
        } catch (Exception $e) {
            throw new Exception("Không thể cấu hình email: " . $e->getMessage());
        }
    }
    
    public function send($to, $subject, $body, $toName = '') {
        try {
            $this->mailer->addAddress($to, $toName);
            
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = strip_tags($body);
            
            $result = $this->mailer->send();
            
            $this->mailer->clearAddresses();
            
            return $result;
            
        } catch (Exception $e) {
            if (APP_DEBUG) {
                echo "Lỗi gửi email: {$this->mailer->ErrorInfo}";
            }
            return false;
        }
    }
    
    /**
     * Gửi email thông báo tài khoản nhân viên mới được tạo
     */
    public function sendEmployeeRegistration($email, $fullName, $token) {
        $loginUrl = Router::url('login?token=' . $token);
        
        $subject = 'Thông tin tài khoản của bạn đã được tạo';
        
        $body = $this->getEmployeeRegistrationTemplate($fullName, $loginUrl);
        
        return $this->send($email, $subject, $body, $fullName);
    }
    
    /**
     * Template email đẹp hơn cho nhân viên mới
     */
    private function getEmployeeRegistrationTemplate($fullName, $loginUrl) {
        return '
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Chào mừng đến với ' . APP_NAME . '</title>
            <style>
                body { margin: 0; padding: 0; font-family: "Helvetica Neue", Arial, sans-serif; background: #f4f4f9; color: #333; line-height: 1.6; }
                .container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; }
                .content { padding: 40px 30px; background: #ffffff; }
                .greeting { font-size: 20px; font-weight: 600; margin-bottom: 20px; }
                .button { display: inline-block; padding: 14px 36px; background: #28a745; color: white !important; text-decoration: none; border-radius: 50px; font-size: 16px; font-weight: 600; margin: 25px 0; transition: all 0.3s; }
                .button:hover { background: #218838; transform: translateY(-2px); }
                .warning { background: #fff8e1; border-left: 5px solid #ffc107; padding: 20px; margin: 25px 0; border-radius: 6px; }
                .link { color: #667eea; text-decoration: underline; word-break: break-all; }
                .footer { background: #f8f9fa; padding: 25px 30px; text-align: center; font-size: 13px; color: #666; border-top: 1px solid #eee; }
                @media only screen and (max-width: 600px) {
                    .container { margin: 15px; }
                    .content, .header { padding: 30px 20px; }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Chào mừng bạn đến với ' . APP_NAME . '</h1>
                </div>
                
                <div class="content">
                    <div class="greeting">Xin chào ' . htmlspecialchars($fullName) . ',</div>
                    
                    <p>Tài khoản của bạn đã được tạo thành công trong hệ thống quản lý bán hàng ' . APP_NAME . '.</p>
                    
                    <p>Để đăng nhập lần đầu tiên và bắt đầu sử dụng, vui lòng nhấn vào nút bên dưới:</p>
                    
                    <p style="text-align: center;">
                        <a href="' . htmlspecialchars($loginUrl) . '" class="button">Đăng nhập ngay</a>
                    </p>
                    
                    <div class="warning">
                        <strong>Lưu ý quan trọng:</strong>
                        <ul style="margin: 10px 0 0 20px; padding-left: 0;">
                            <li>Liên kết này chỉ có hiệu lực trong <strong>' . TOKEN_EXPIRY_MINUTES . ' phút</strong></li>
                            <li>Sau khi hết hạn, vui lòng liên hệ quản trị viên để được gửi lại email</li>
                            <li>Sau khi đăng nhập lần đầu, bạn <strong>bắt buộc phải đổi mật khẩu</strong> để bảo mật tài khoản</li>
                        </ul>
                    </div>
                    
                    <p>Nếu nút trên không hoạt động, bạn có thể sao chép và dán đường dẫn sau vào trình duyệt:</p>
                    <p class="link">' . htmlspecialchars($loginUrl) . '</p>
                </div>
                
                <div class="footer">
                    <p>Email này được gửi tự động, vui lòng không trả lời trực tiếp.</p>
                    <p>© ' . date('Y') . ' ' . APP_NAME . ' - Hệ thống quản lý bán hàng</p>
                </div>
            </div>
        </body>
        </html>
        ';
    }
    
    /**
     * Gửi email yêu cầu đặt lại mật khẩu
     */
    public function sendPasswordReset($email, $fullName, $resetToken) {
        $resetUrl = Router::url('reset-password?token=' . $resetToken);
        
        $subject = 'Yêu cầu đặt lại mật khẩu';
        
        $body = '
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Đặt lại mật khẩu</title>
            <style>
                body { margin: 0; padding: 0; font-family: "Helvetica Neue", Arial, sans-serif; background: #f4f4f9; color: #333; line-height: 1.6; }
                .container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 40px 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 26px; }
                .content { padding: 40px 30px; }
                .greeting { font-size: 20px; font-weight: 600; margin-bottom: 20px; }
                .button { display: inline-block; padding: 14px 36px; background: #dc3545; color: white !important; text-decoration: none; border-radius: 50px; font-size: 16px; font-weight: 600; margin: 25px 0; }
                .button:hover { background: #c82333; }
                .footer { background: #f8f9fa; padding: 25px 30px; text-align: center; font-size: 13px; color: #666; border-top: 1px solid #eee; }
                .link { color: #dc3545; word-break: break-all; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Đặt lại mật khẩu</h1>
                </div>
                
                <div class="content">
                    <div class="greeting">Xin chào ' . htmlspecialchars($fullName) . ',</div>
                    
                    <p>Chúng tôi vừa nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại ' . APP_NAME . '.</p>
                    
                    <p style="text-align: center;">
                        <a href="' . htmlspecialchars($resetUrl) . '" class="button">Đặt lại mật khẩu ngay</a>
                    </p>
                    
                    <p>Liên kết này có hiệu lực trong <strong>15 phút</strong>. Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
                    
                    <p>Nếu nút không hoạt động, bạn có thể sao chép đường dẫn sau:</p>
                    <p class="link">' . htmlspecialchars($resetUrl) . '</p>
                </div>
                
                <div class="footer">
                    <p>Email này được gửi tự động, vui lòng không trả lời.</p>
                    <p>© ' . date('Y') . ' ' . APP_NAME . ' - Hệ thống quản lý bán hàng</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        return $this->send($email, $subject, $body, $fullName);
    }
}