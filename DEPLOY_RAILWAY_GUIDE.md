# 🚀 Hướng dẫn Deploy HTHREE lên Railway

## Bước 1: Chuẩn bị

### 1.1. Tạo tài khoản Railway

- Truy cập: https://railway.app
- Đăng ký bằng GitHub

### 1.2. Push code lên GitHub (nếu chưa có)

```bash
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/hthree-film.git
git push -u origin main
```

---

## Bước 2: Deploy Backend (PHP + MySQL)

### 2.1. Tạo Project mới trên Railway

1. Click **"New Project"**
2. Chọn **"Deploy from GitHub repo"**
3. Chọn repo `hthree-film`

### 2.2. Add MySQL Database

1. Click **"+ New"** → **"Database"** → **"Add MySQL"**
2. Railway sẽ tự động tạo database
3. Copy thông tin kết nối:
   - `MYSQL_HOST`
   - `MYSQL_PORT`
   - `MYSQL_USER`
   - `MYSQL_PASSWORD`
   - `MYSQL_DATABASE`

### 2.3. Cấu hình Backend

Tạo file `railway.json` trong thư mục `backend/`:

```json
{
  "$schema": "https://railway.app/railway.schema.json",
  "build": {
    "builder": "NIXPACKS"
  },
  "deploy": {
    "startCommand": "php -S 0.0.0.0:$PORT -t .",
    "restartPolicyType": "ON_FAILURE",
    "restartPolicyMaxRetries": 10
  }
}
```

Tạo file `nixpacks.toml` trong thư mục `backend/`:

```toml
[phases.setup]
nixPkgs = ["php82", "php82Extensions.mysqli", "php82Extensions.pdo", "php82Extensions.pdo_mysql"]

[phases.build]
cmds = ["echo 'Build complete'"]

[start]
cmd = "php -S 0.0.0.0:$PORT -t ."
```

### 2.4. Update Database Config

Sửa file `backend/config/database.php`:

```php
<?php
function getDBConnection() {
    // Railway environment variables
    $host = getenv('MYSQL_HOST') ?: 'localhost';
    $port = getenv('MYSQL_PORT') ?: '3306';
    $dbname = getenv('MYSQL_DATABASE') ?: 'hthree_film';
    $username = getenv('MYSQL_USER') ?: 'root';
    $password = getenv('MYSQL_PASSWORD') ?: '';

    $conn = new mysqli($host, $username, $password, $dbname, $port);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}
?>
```

### 2.5. Import Database

1. Vào Railway Dashboard → MySQL service
2. Click **"Data"** tab
3. Click **"Connect"** → Copy connection string
4. Dùng MySQL Workbench hoặc phpMyAdmin để import file `.sql`

Hoặc dùng CLI:

```bash
mysql -h MYSQL_HOST -P MYSQL_PORT -u MYSQL_USER -p MYSQL_DATABASE < hthree_film.sql
```

### 2.6. Deploy Backend

1. Railway sẽ tự động deploy khi push code
2. Sau khi deploy xong, copy **Backend URL** (ví dụ: `https://backend-production-abc.up.railway.app`)

---

## Bước 3: Deploy Frontend (Vue.js)

### 3.1. Update API URL

Sửa file `.env.production`:

```env
VITE_API_BASE_URL=https://backend-production-abc.up.railway.app/api
VITE_FIREBASE_API_KEY=your_firebase_key
VITE_FIREBASE_AUTH_DOMAIN=your_firebase_domain
# ... other Firebase config
```

### 3.2. Build Frontend

```bash
npm run build
```

### 3.3. Deploy Frontend lên Vercel

```bash
# Install Vercel CLI
npm i -g vercel

# Deploy
vercel --prod
```

Hoặc deploy qua Vercel Dashboard:

1. Vào https://vercel.com
2. Import GitHub repo
3. Vercel tự động detect Vue.js và build

---

## Bước 4: Cấu hình Webhook

### 4.1. Copy Backend URL

Ví dụ: `https://backend-production-abc.up.railway.app`

### 4.2. Update Casso Webhook

1. Đăng nhập Casso: https://casso.vn
2. Vào **Cài đặt** → **Webhook**
3. Nhập URL:
   ```
   https://backend-production-abc.up.railway.app/api/payment/casso_webhook.php
   ```
4. Click **Lưu** và **Test**

---

## Bước 5: Test

1. Truy cập frontend URL (Vercel): `https://hthree-film.vercel.app`
2. Đăng ký/Đăng nhập
3. Mua gói subscription
4. Thanh toán VietQR
5. Kiểm tra webhook tự động kích hoạt

---

## 🔧 Troubleshooting

### Lỗi: Database connection failed

- Check environment variables trên Railway
- Verify MySQL service đang chạy

### Lỗi: CORS

Thêm vào `backend/api/` files:

```php
header('Access-Control-Allow-Origin: https://hthree-film.vercel.app');
```

### Lỗi: 404 Not Found

- Check `railway.json` startCommand
- Verify file paths

---

## 💰 Chi phí

- **Railway**: $5 credit/tháng (miễn phí)
- **Vercel**: Miễn phí
- **Domain** (optional): ~$10/năm

**Tổng: $0/tháng** (trong giới hạn free tier)

---

## 🎉 Hoàn thành!

Bây giờ bạn có:

- ✅ Backend PHP trên Railway với URL cố định
- ✅ Frontend Vue.js trên Vercel
- ✅ MySQL database trên Railway
- ✅ Webhook hoạt động 24/7
- ✅ HTTPS miễn phí
- ✅ Auto deploy từ GitHub

Không cần ngrok nữa! 🚀
