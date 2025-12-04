# Database Setup Guide

## 🗄️ Cài đặt Database

### Bước 1: Mở phpMyAdmin

1. Mở AMPPS
2. Click vào **phpMyAdmin** hoặc truy cập: http://localhost/phpmyadmin
3. Đăng nhập:
   - Username: `root`
   - Password: `mysql` (AMPPS default)

### Bước 2: Import Database

1. Click tab **SQL** ở trên cùng
2. Copy toàn bộ nội dung file `schema.sql`
3. Paste vào ô SQL
4. Click **Go** để chạy

### Bước 3: Kiểm tra

Database `hthree_film` sẽ được tạo với các bảng:

- ✅ `users` - Người dùng
- ✅ `watch_history` - Lịch sử xem
- ✅ `favorites` - Phim yêu thích
- ✅ `ratings` - Đánh giá phim

### Bước 4: Test

Truy cập: http://localhost/HTHREE_film/backend/

## 👤 Tài khoản mẫu

Đã tạo sẵn 2 tài khoản để test:

**Admin:**

- Username: `admin`
- Email: `admin@hthree.com`
- Password: `123456`

**User:**

- Username: `user1`
- Email: `user1@hthree.com`
- Password: `123456`

## 📡 API Endpoints

### Authentication

**Đăng ký:**

```
POST /backend/api/auth/register.php
Body: {
  "username": "newuser",
  "email": "user@example.com",
  "password": "123456",
  "full_name": "Nguyen Van A"
}
```

**Đăng nhập:**

```
POST /backend/api/auth/login.php
Body: {
  "username": "admin",
  "password": "123456"
}
```

### Watch History

**Lấy lịch sử:**

```
GET /backend/api/user/watch-history.php?user_id=1&limit=20
```

**Thêm lịch sử:**

```
POST /backend/api/user/watch-history.php
Body: {
  "user_id": 1,
  "movie_slug": "avatar-2",
  "movie_name": "Avatar 2",
  "movie_poster": "...",
  "episode": "Full",
  "watch_time": 1200,
  "duration": 7200
}
```

### Favorites

**Lấy danh sách yêu thích:**

```
GET /backend/api/user/favorites.php?user_id=1
```

**Thêm yêu thích:**

```
POST /backend/api/user/favorites.php
Body: {
  "user_id": 1,
  "movie_slug": "avatar-2",
  "movie_name": "Avatar 2",
  "movie_poster": "...",
  "movie_year": 2022,
  "movie_quality": "FHD"
}
```

**Xóa yêu thích:**

```
DELETE /backend/api/user/favorites.php?user_id=1&movie_slug=avatar-2
```

## 🔧 Troubleshooting

### Lỗi kết nối database

- Check AMPPS MySQL đã start chưa
- Check password trong `config/database.php` (default: `mysql`)

### Lỗi import SQL

- Đảm bảo đã chọn database `hthree_film`
- Hoặc chạy từng câu lệnh CREATE TABLE riêng

### Lỗi CORS

- Check `config/cors.php`
- Thêm origin vào `ALLOWED_ORIGINS`
