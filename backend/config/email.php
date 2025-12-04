<?php
/**
 * Email Configuration
 * Cấu hình Gmail SMTP để gửi email
 */

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        
        try {
            // Cấu hình SMTP
            $this->mail->isSMTP();
            $this->mail->SMTPDebug = 0; // 0 = tắt debug, 2 = bật debug chi tiết
            $this->mail->Host       = 'smtp.gmail.com';
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = 'hient7182@gmail.com'; // Email Gmail của bạn
            $this->mail->Password   = 'ceytfubvcycyleoz';     // App Password (16 ký tự, không dấu cách)
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = 587;
            $this->mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Cấu hình người gửi
            $this->mail->setFrom('hient7182@gmail.com', 'HTHREE Film');
            $this->mail->CharSet = 'UTF-8';
            
        } catch (Exception $e) {
            error_log("Email configuration error: {$this->mail->ErrorInfo}");
        }
    }
    
    /**
     * Gửi mã xác thực đổi mật khẩu
     */
    public function sendPasswordResetCode($toEmail, $code) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($toEmail);
            
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Mã xác thực đổi mật khẩu - HTHREE Film';
            
            // Nội dung email HTML
            $this->mail->Body = $this->getPasswordResetTemplate($code);
            
            // Nội dung email text thuần
            $this->mail->AltBody = "Mã xác thực của bạn là: $code\n\nMã này có hiệu lực trong 15 phút.\n\nNếu bạn không yêu cầu đổi mật khẩu, vui lòng bỏ qua email này.";
            
            $this->mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Email sending error: {$this->mail->ErrorInfo}");
            return false;
        }
    }
    
    /**
     * Template HTML cho email đổi mật khẩu
     */
    private function getPasswordResetTemplate($code) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .logo { font-size: 32px; font-weight: bold; color: #fbbf24; margin-bottom: 10px; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .code-box { background: white; border: 2px dashed #fbbf24; padding: 20px; text-align: center; margin: 20px 0; border-radius: 8px; }
                .code { font-size: 36px; font-weight: bold; color: #1a1a1a; letter-spacing: 8px; }
                .warning { background: #fff3cd; border-left: 4px solid #fbbf24; padding: 15px; margin: 20px 0; }
                .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>🎬 HTHREE FILM</div>
                    <p>Yêu cầu đổi mật khẩu</p>
                </div>
                <div class='content'>
                    <h2>Xin chào!</h2>
                    <p>Bạn đã yêu cầu đổi mật khẩu cho tài khoản HTHREE Film của mình.</p>
                    
                    <div class='code-box'>
                        <p style='margin: 0 0 10px 0; color: #666;'>Mã xác thực của bạn là:</p>
                        <div class='code'>$code</div>
                    </div>
                    
                    <div class='warning'>
                        <strong>⚠️ Lưu ý:</strong>
                        <ul style='margin: 10px 0 0 0; padding-left: 20px;'>
                            <li>Mã này có hiệu lực trong <strong>15 phút</strong></li>
                            <li>Không chia sẻ mã này với bất kỳ ai</li>
                            <li>Nếu bạn không yêu cầu đổi mật khẩu, vui lòng bỏ qua email này</li>
                        </ul>
                    </div>
                    
                    <p>Trân trọng,<br><strong>Đội ngũ HTHREE Film</strong></p>
                </div>
                <div class='footer'>
                    <p>Email này được gửi tự động, vui lòng không trả lời.</p>
                    <p>&copy; 2024 HTHREE Film. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
?>
