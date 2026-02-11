<!-- <?php
/**
 * Mailer Class
 * Gui email su dung PHPMailer
 * 
 * Yeu cau: Thu vien PHPMailer phai duoc tai ve va dat trong thu muc libraries/PHPMailer/
 * Download tai: https://github.com/PHPMailer/PHPMailer
 */

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
require_once __DIR__ . '/../libraries/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../libraries/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libraries/PHPMailer/src/SMTP.php';

class Mailer {
    
    private $mailer;
    
    /**
     * Khoi tao PHPMailer
     */
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }
    
    /**
     * Cau hinh SMTP
     */
    private function configure() {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host       = MAIL_HOST;
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = MAIL_USERNAME;
            $this->mailer->Password   = MAIL_PASSWORD;
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port       = MAIL_PORT;
            $this->mailer->CharSet    = 'UTF-8';
            
            // Set thong tin nguoi gui
            $this->mailer->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
            
            // Debug (tat khi production)
            if (APP_DEBUG) {
                $this->mailer->SMTPDebug = 0; // 0 = off, 2 = debug
            }
            
        } catch (Exception $e) {
            throw new Exception("Khong the cau hinh email: " . $e->getMessage());
        }
    }
    
    /**
     * Gui email don gian
     * 
     * @param string $to Email nguoi nhan
     * @param string $subject Tieu de
     * @param string $body Noi dung (HTML)
     * @param string $toName Ten nguoi nhan (optional)
     * @return bool
     */
    public function send($to, $subject, $body, $toName = '') {
        try {
            // Nguoi nhan
            $this->mailer->addAddress($to, $toName);
            
            // Noi dung
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = strip_tags($body); // Plain text cho client khong ho tro HTML
            
            // Gui
            $result = $this->mailer->send();
            
            // Clear recipients cho lan gui tiep theo
            $this->mailer->clearAddresses();
            
            return $result;
            
        } catch (Exception $e) {
            // Log loi (trong production nen log vao file)
            if (APP_DEBUG) {
                echo "Email Error: {$this->mailer->ErrorInfo}";
            }
            return false;
        }
    }
    
    /**
     * Gui email dang ky cho nhan vien moi
     * 
     * @param string $email Email nhan vien
     * @param string $fullName Ten nhan vien
     * @param string $token Login token
     * @return bool
     */
    public function sendEmployeeRegistration($email, $fullName, $token) {
        $loginUrl = Router::url('login.php?token=' . $token);
        
        $subject = 'Tai khoan cua ban da duoc tao';
        
        $body = $this->getEmployeeRegistrationTemplate($fullName, $loginUrl);
        
        return $this->send($email, $subject, $body, $fullName);
    }
    
    /**
     * Template email dang ky nhan vien
     */
    private function getEmployeeRegistrationTemplate($fullName, $loginUrl) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .button { display: inline-block; padding: 12px 30px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
                .warning { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Chao mung den voi ' . APP_NAME . '</h1>
                </div>
                
                <div class="content">
                    <p>Xin chao <strong>' . htmlspecialchars($fullName) . '</strong>,</p>
                    
                    <p>Tai khoan cua ban da duoc tao thanh cong trong he thong ' . APP_NAME . '.</p>
                    
                    <p>De dang nhap lan dau tien, vui long click vao nut ben duoi:</p>
                    
                    <p style="text-align: center;">
                        <a href="' . htmlspecialchars($loginUrl) . '" class="button">Dang nhap ngay</a>
                    </p>
                    
                    <div class="warning">
                        <strong>Luu y quan trong:</strong>
                        <ul>
                            <li>Lien ket nay chi co hieu luc trong <strong>' . TOKEN_EXPIRY_MINUTES . ' phut</strong></li>
                            <li>Sau khi het han, vui long lien he quan tri vien de gui lai email</li>
                            <li>Sau khi dang nhap, ban bat buoc phai doi mat khau</li>
                        </ul>
                    </div>
                    
                    <p>Neu nut khong hoat dong, hay copy link sau vao trinh duyet:</p>
                    <p style="word-break: break-all; color: #007bff;">' . htmlspecialchars($loginUrl) . '</p>
                </div>
                
                <div class="footer">
                    <p>Email nay duoc gui tu dong, vui long khong tra loi.</p>
                    <p>&copy; ' . date('Y') . ' ' . APP_NAME . '. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ';
    }
    
    /**
     * Gui email quen mat khau (optional - neu can)
     */
    public function sendPasswordReset($email, $fullName, $resetToken) {
        $resetUrl = Router::url('reset-password.php?token=' . $resetToken);
        
        $subject = 'Yeu cau dat lai mat khau';
        
        $body = '
        <!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"></head>
        <body>
            <h2>Xin chao ' . htmlspecialchars($fullName) . '</h2>
            <p>Chung toi nhan duoc yeu cau dat lai mat khau cho tai khoan cua ban.</p>
            <p><a href="' . htmlspecialchars($resetUrl) . '">Click vao day de dat lai mat khau</a></p>
            <p>Lien ket co hieu luc trong 15 phut.</p>
            <p>Neu ban khong yeu cau dat lai mat khau, vui long bo qua email nay.</p>
        </body>
        </html>
        ';
        
        return $this->send($email, $subject, $body, $fullName);
    }
}
?> -->