# 🗄️ HƯỚNG DẪN KHÔI PHỤC DATABASE

## 📁 Các file SQL có sẵn trong project:

1. **`backend/database/schema.sql`** - Schema chính (users, comments, favorites, etc.)
2. **`backend/database/create_comments_table.sql`** - Bảng comments + dữ liệu mẫu
3. **`backend/database/add_password_reset_table.sql`** - Bảng password reset
4. **`movies_rows.sql`** - Dữ liệu phim (Supabase - optional)

---

## 🚀 CÁCH KHÔI PHỤC:

### **Bước 1: Mở phpMyAdmin**
1. Mở AMPPS
2. Click **phpMyAdmin** hoặc vào: http://localhost/phpMyAdmin

### **Bước 2: Tạo Database**
1. Click tab **"SQL"**
2. Copy và paste lệnh sau:

```sql
CREATE DATABASE IF NOT EXISTS hthree_film CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. Click **"Go"**

### **Bước 3: Import Schema chính**
1. Chọn database **`hthree_film`** ở sidebar trái
2. Click tab **"Import"**
3. Click **"Choose File"**
4. Chọn file: `backend/database/schema.sql`
5. Click **"Import"**

✅ Xong! Bảng users, comments, favorites, ratings, watch_history đã được tạo!

### **Bước 4: Kiểm tra**
1. Click vào database **`hthree_film`**
2. Bạn sẽ thấy các bảng:
   - ✅ users (2 users mẫu)
   - ✅ comments (8 comments mẫu)
   - ✅ favorites
   - ✅ ratings
   - ✅ watch_history
   - ✅ password_reset_tokens

---

## 👤 TÀI KHOẢN MẪU:

### Admin:
- **Username**: `admin`
- **Email**: `admin@hthree.com`
- **Password**: `123456`

### User Demo:
- **Username**: `user1`
- **Email**: `user1@hthree.com`
- **Password**: `123456`

---

## 📊 CẤU TRÚC DATABASE:

### 1. **users** - Người dùng
```
- id (PK)
- username (unique)
- email (unique)
- password (hashed)
- full_name
- avatar
- created_at
- updated_at
- last_login
- is_active
```

### 2. **comments** - Bình luận
```
- id (PK)
- user_id (FK → users)
- movie_slug
- parent_id (FK → comments) - cho replies
- content
- likes
- created_at
- updated_at
```

### 3. **favorites** - Phim yêu thích
```
- id (PK)
- user_id (FK → users)
- movie_slug
- movie_name
- movie_poster
- movie_year
- movie_quality
- added_at
```

### 4. **watch_history** - Lịch sử xem
```
- id (PK)
- user_id (FK → users)
- movie_slug
- movie_name
- movie_poster
- episode
- watch_time (giây)
- duration (giây)
- watched_at
```

### 5. **ratings** - Đánh giá
```
- id (PK)
- user_id (FK → users)
- movie_slug
- rating (1-5)
- review
- created_at
- updated_at
```

### 6. **password_reset_tokens** - Mã reset password
```
- id (PK)
- email
- token (6 số)
- expires_at
- used (0/1)
- created_at
```

---

## 🔧 NẾU GẶP LỖI:

### Lỗi: "Table already exists"
→ Xóa database cũ trước:
```sql
DROP DATABASE IF EXISTS hthree_film;
```

### Lỗi: "Cannot add foreign key constraint"
→ Chạy lại từ đầu, đảm bảo chạy đúng thứ tự

### Lỗi: "Access denied"
→ Đảm bảo MySQL đang chạy trong AMPPS

---

## 📝 THÊM DỮ LIỆU MẪU:

### Thêm comments mẫu:
```sql
INSERT INTO comments (user_id, movie_slug, content, likes) VALUES
(1, 'avatar-2-dong-chay-cua-nuoc', 'Phim hay quá! Đáng xem!', 234),
(2, 'robin-hood', 'Diễn xuất tuyệt vời!', 189),
(1, 'son-than-di-van-luc', 'Phim Trung Quốc hay nhất năm', 156);
```

### Thêm favorites mẫu:
```sql
INSERT INTO favorites (user_id, movie_slug, movie_name, movie_poster, movie_year, movie_quality) VALUES
(1, 'avatar-2-dong-chay-cua-nuoc', 'Avatar 2: Dòng Chảy Của Nước', 'https://phimimg.com/...', 2022, 'HD'),
(1, 'robin-hood', 'Robin Hood', 'https://phimimg.com/...', 2018, 'FHD');
```

---

## 🔄 BACKUP DATABASE:

### Export database:
1. Chọn database **`hthree_film`**
2. Click tab **"Export"**
3. Chọn **"Quick"** hoặc **"Custom"**
4. Click **"Export"**
5. File `.sql` sẽ được download

### Lưu file backup vào:
```
backend/database/backup_[date].sql
```

---

## ✅ KIỂM TRA KẾT NỐI:

### Test kết nối PHP:
Tạo file `backend/test-db.php`:

```php
<?php
require_once 'config/database.php';

$db = getDBConnection();

if ($db) {
    echo "✅ Kết nối database thành công!<br>";
    
    // Đếm users
    $result = $db->query("SELECT COUNT(*) as total FROM users");
    $row = $result->fetch_assoc();
    echo "👤 Tổng users: " . $row['total'] . "<br>";
    
    // Đếm comments
    $result = $db->query("SELECT COUNT(*) as total FROM comments");
    $row = $result->fetch_assoc();
    echo "💬 Tổng comments: " . $row['total'] . "<br>";
    
    $db->close();
} else {
    echo "❌ Không thể kết nối database!";
}
?>
```

Truy cập: http://localhost/HTHREE_film/backend/test-db.php

---

**🎉 Xong! Database đã được khôi phục!**
