<?php
/**
 * Simple Email Service (không cần Composer)
 * Sử dụng mail() function của PHP
 * 
 * LƯU Ý: Cần cấu hình SMTP trong php.ini của AMPPS
 */

class SimpleEmailService {
    
    /**
     * Gửi mã xác thực đổi mật khẩu
     */
    public function sendPasswordResetCode($toEmail, $code) {
        $subject = 'Mã xác thực đổi mật khẩu - HTHREE Film';
        
        // Headers
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: HTHREE Film <noreply@hthree.com>" . "\r\n";
        
        // Nội dung email
        $message = $this->getPasswordResetTemplate($code);
        
        // Gửi email
        return mail($toEmail, $subject, $message, $headers);
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
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f4f4f4; margin: 0; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%); color: white; padding: 30px; text-align: center; }
                .logo { font-size: 32px; font-weight: bold; color: #fbbf24; margin-bottom: 10px; }
                .content { padding: 30px; }
                .code-box { background: #f9f9f9; border: 2px dashed #fbbf24; padding: 20px; text-align: center; margin: 20px 0; border-radius: 8px; }
                .code { font-size: 36px; font-weight: bold; color: #1a1a1a; letter-spacing: 8px; font-family: 'Courier New', monospace; }
                .warning { background: #fff3cd; border-left: 4px solid #fbbf24; padding: 15px; margin: 20px 0; border-radius: 4px; }
                .footer { text-align: center; color: #666; font-size: 12px; padding: 20px; background: #f9f9f9; }
                .button { display: inline-block; background: #fbbf24; color: #1a1a1a; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>🎬 HTHREE FILM</div>
                    <p style='margin: 0; font-size: 18px;'>Yêu cầu đổi mật khẩu</p>
                </div>
                <div class='content'>
                    <h2 style='color: #1a1a1a; margin-top: 0;'>Xin chào!</h2>
                    <p>Bạn đã yêu cầu đổi mật khẩu cho tài khoản HTHREE Film của mình.</p>
                    
                    <div class='code-box'>
                        <p style='margin: 0 0 10px 0; color: #666; font-size: 14px;'>Mã xác thực của bạn là:</p>
                        <div class='code'>$code</div>
                        <p style='margin: 10px 0 0 0; color: #999; font-size: 12px;'>Nhập mã này vào trang đổi mật khẩu</p>
                    </div>
                    
                    <div class='warning'>
                        <strong style='color: #856404;'>⚠️ Lưu ý quan trọng:</strong>
                        <ul style='margin: 10px 0 0 0; padding-left: 20px; color: #856404;'>
                            <li>Mã này có hiệu lực trong <strong>15 phút</strong></li>
                            <li>Không chia sẻ mã này với bất kỳ ai</li>
                            <li>Nếu bạn không yêu cầu đổi mật khẩu, vui lòng bỏ qua email này</li>
                        </ul>
                    </div>
                    
                    <p style='margin-top: 30px;'>
                        Trân trọng,<br>
                        <strong>Đội ngũ HTHREE Film</strong>
                    </p>
                </div>
                <div class='footer'>
                    <p style='margin: 5px 0;'>Email này được gửi tự động, vui lòng không trả lời.</p>
                    <p style='margin: 5px 0;'>&copy; 2024 HTHREE Film. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
?>
