# 🚀 Hướng dẫn Deploy HTHREE lên Hostinger

## Bước 1: Mua Hosting

### 1.1. Đăng ký Hostinger

- Truy cập: https://www.hostinger.vn
- Chọn gói **Premium** hoặc **Business** (có MySQL)
- Giá: ~30,000đ - 50,000đ/tháng
- Tặng domain miễn phí 1 năm

### 1.2. Chọn Domain

- Chọn domain mới (miễn phí): `hthreefilm.com`
- Hoặc dùng domain có sẵn

---

## Bước 2: Upload Backend

### 2.1. Đăng nhập cPanel

1. Vào Hostinger Dashboard
2. Click **"Quản lý"** → **"cPanel"**

### 2.2. Upload Code

**Cách 1: File Manager (Dễ nhất)**

1. Vào **File Manager**
2. Vào thư mục `public_html`
3. Click **Upload**
4. Upload toàn bộ thư mục `backend/`
5. Giải nén (nếu upload file .zip)

**Cách 2: FTP (Nhanh hơn)**

1. Download FileZilla: https://filezilla-project.org
2. Kết nối FTP:
   - Host: `ftp.yourdomain.com`
   - Username: (từ Hostinger)
   - Password: (từ Hostinger)
   - Port: 21
3. Upload thư mục `backend/` vào `public_html/`

### 2.3. Cấu trúc thư mục

```
public_html/
├── api/
│   ├── auth/
│   ├── payment/
│   ├── orders.php
│   └── ...
├── config/
│   └── database.php
└── index.php
```

---

## Bước 3: Tạo Database MySQL

### 3.1. Tạo Database

1. Vào cPanel → **MySQL Databases**
2. Tạo database mới: `hthree_film`
3. Tạo user: `hthree_user`
4. Set password mạnh
5. Add user vào database với **ALL PRIVILEGES**

### 3.2. Import Database

1. Vào cPanel → **phpMyAdmin**
2. Chọn database `hthree_film`
3. Click tab **Import**
4. Chọn file `hthree_film.sql`
5. Click **Go**

### 3.3. Update Database Config

Sửa file `public_html/config/database.php`:

```php
<?php
function getDBConnection() {
    $host = 'localhost'; // Hostinger thường dùng localhost
    $dbname = 'u123456_hthree_film'; // Tên database từ cPanel
    $username = 'u123456_hthree_user'; // Username từ cPanel
    $password = 'YOUR_PASSWORD'; // Password bạn đặt

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}
?>
```

---

## Bước 4: Deploy Frontend

### 4.1. Update API URL

Sửa file `.env.production`:

```env
VITE_API_BASE_URL=https://yourdomain.com/api
```

### 4.2. Build Frontend

```bash
npm run build
```

### 4.3. Upload Frontend

**Option A: Upload vào Hostinger (Same domain)**

1. Upload thư mục `dist/` vào `public_html/`
2. Rename `dist/` thành `app/` hoặc để nguyên
3. Truy cập: `https://yourdomain.com/app`

**Option B: Deploy lên Vercel (Recommended)**

```bash
vercel --prod
```

- Frontend: `https://hthreefilm.vercel.app`
- Backend: `https://yourdomain.com/api`

---

## Bước 5: Cấu hình Domain & SSL

### 5.1. SSL Certificate (HTTPS)

1. Vào Hostinger Dashboard
2. Click **SSL**
3. Enable **Free SSL** (Let's Encrypt)
4. Đợi 5-10 phút để active

### 5.2. Force HTTPS

Tạo file `.htaccess` trong `public_html/`:

```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# CORS Headers
Header set Access-Control-Allow-Origin "*"
Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
Header set Access-Control-Allow-Headers "Content-Type, Authorization"
```

---

## Bước 6: Cấu hình Webhook

### 6.1. Test Backend API

Truy cập: `https://yourdomain.com/api/test.php`

Tạo file `public_html/api/test.php`:

```php
<?php
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'API is working!',
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
```

### 6.2. Update Casso Webhook

1. Đăng nhập Casso: https://casso.vn
2. Vào **Cài đặt** → **Webhook**
3. Nhập URL:
   ```
   https://yourdomain.com/api/payment/casso_webhook.php
   ```
4. Click **Lưu** và **Test**

---

## Bước 7: Test Production

1. Truy cập: `https://yourdomain.com`
2. Đăng ký tài khoản
3. Mua gói subscription
4. Thanh toán VietQR
5. Kiểm tra webhook tự động kích hoạt

---

## 🔧 Troubleshooting

### Lỗi: 500 Internal Server Error

- Check file permissions: 644 cho files, 755 cho folders
- Check `.htaccess` syntax
- Check PHP error logs trong cPanel

### Lỗi: Database connection failed

- Verify database credentials
- Check database user có quyền truy cập
- Ping database từ PHP

### Lỗi: CORS

- Check `.htaccess` có CORS headers
- Verify `Access-Control-Allow-Origin` header

---

## 💰 Chi phí

- **Hostinger Premium**: ~30,000đ/tháng
- **Domain**: Miễn phí năm đầu
- **SSL**: Miễn phí (Let's Encrypt)

**Tổng: ~30,000đ/tháng**

---

## 🎉 Hoàn thành!

Bây giờ bạn có:

- ✅ Website chạy trên domain riêng
- ✅ Backend PHP + MySQL trên Hostinger
- ✅ HTTPS miễn phí
- ✅ Webhook hoạt động 24/7
- ✅ cPanel dễ quản lý

Không cần ngrok nữa! 🚀
