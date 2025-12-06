<?php
/**
 * Test Generate VietQR - Không cần API Key
 * Chỉ cần thông tin ngân hàng
 */

// Thông tin ngân hàng của bạn
$bankId = '970422';              // MB Bank
$accountNo = '0825591211';       // Số TK
$accountName = 'PHAM TRUNG TUAN'; // Tên (VIẾT HOA)

// Thông tin đơn hàng test
$amount = 50000;                 // 50,000đ
$orderCode = 'TEST' . date('YmdHis'); // VD: TEST20241205143022
$content = 'HTHREE ' . $orderCode;

// Remove Vietnamese tones
function removeVietnameseTones($str) {
    $vietnameseTones = [
        'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
        'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
        'ì', 'í', 'ị', 'ỉ', 'ĩ',
        'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
        'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
        'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
        'đ',
        'À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ',
        'È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ',
        'Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ',
        'Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ',
        'Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ',
        'Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ',
        'Đ'
    ];
    
    $replacements = [
        'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
        'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
        'i', 'i', 'i', 'i', 'i',
        'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
        'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
        'y', 'y', 'y', 'y', 'y',
        'd',
        'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A',
        'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E',
        'I', 'I', 'I', 'I', 'I',
        'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O',
        'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U',
        'Y', 'Y', 'Y', 'Y', 'Y',
        'D'
    ];
    
    return str_replace($vietnameseTones, $replacements, $str);
}

// Encode content
$contentEncoded = removeVietnameseTones($content);
$contentEncoded = strtoupper($contentEncoded);
$contentEncoded = urlencode($contentEncoded);

// Encode account name
$accountNameEncoded = removeVietnameseTones($accountName);
$accountNameEncoded = strtoupper($accountNameEncoded);
$accountNameEncoded = urlencode($accountNameEncoded);

// Generate VietQR URL
$qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png";
$qrUrl .= "?amount={$amount}";
$qrUrl .= "&addInfo={$contentEncoded}";
$qrUrl .= "&accountName={$accountNameEncoded}";

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test VietQR - HTHREE Film</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .qr-container {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        .qr-container img {
            width: 100%;
            max-width: 350px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #666;
            font-size: 14px;
        }
        .info-value {
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        .amount {
            font-size: 32px !important;
            color: #e74c3c;
        }
        .content-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .content-box h3 {
            color: #856404;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .content-value {
            background: white;
            padding: 12px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            text-align: center;
            letter-spacing: 2px;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px;
            border-radius: 5px;
            font-size: 13px;
            color: #856404;
            margin-top: 20px;
        }
        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 12px;
            border-radius: 5px;
            font-size: 13px;
            color: #155724;
            margin-bottom: 20px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎬 Test VietQR</h1>
        <p class="subtitle">HTHREE Film Payment System</p>

        <div class="success">
            ✅ Mã QR đã được tạo thành công!
        </div>

        <div class="qr-container">
            <img src="<?php echo $qrUrl; ?>" alt="VietQR Code">
        </div>

        <div class="content-box">
            <h3>⚠️ Nội dung chuyển khoản (BẮT BUỘC)</h3>
            <div class="content-value"><?php echo $content; ?></div>
        </div>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Số tiền</span>
                <span class="info-value amount"><?php echo number_format($amount); ?>đ</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ngân hàng</span>
                <span class="info-value">MB Bank</span>
            </div>
            <div class="info-row">
                <span class="info-label">Số tài khoản</span>
                <span class="info-value"><?php echo $accountNo; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Chủ tài khoản</span>
                <span class="info-value"><?php echo $accountName; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Mã đơn hàng</span>
                <span class="info-value"><?php echo $orderCode; ?></span>
            </div>
        </div>

        <div class="warning">
            <strong>📱 Hướng dẫn:</strong><br>
            1. Mở app MB Bank<br>
            2. Quét mã QR phía trên<br>
            3. Kiểm tra thông tin và xác nhận<br>
            4. Vào Casso để xem giao dịch
        </div>

        <a href="https://flow.casso.vn" target="_blank" class="btn">
            Mở Casso để xem giao dịch
        </a>
    </div>
</body>
</html>
