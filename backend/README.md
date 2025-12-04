# HTHREE Film - PHP Backend API

Backend API cho dự án HTHREE Film, sử dụng PHP thuần để proxy API từ phimapi.com.

## 📁 Cấu trúc

```
backend/
├── api/                    # API endpoints
│   ├── movies.php         # Lấy danh sách phim
│   ├── search.php         # Tìm kiếm phim
│   └── detail.php         # Chi tiết phim
├── config/                # Configuration
│   ├── config.php         # Cấu hình chung
│   └── cors.php           # CORS handler
├── helpers/               # Helper classes
│   └── ApiHelper.php      # API helper với cache
├── cache/                 # Cache folder (auto-created)
├── index.php              # API documentation
└── .htaccess              # Apache config
```

## 🚀 Cài đặt

### 1. Copy vào AMPPS

```bash
# Project đã ở D:\Ampps\www\HTHREE_film\
# Folder backend/ đã được tạo sẵn
```

### 2. Khởi động AMPPS

- Mở AMPPS
- Start Apache
- Start MySQL (nếu cần database sau này)

### 3. Truy cập

- **API Documentation**: http://localhost/HTHREE_film/backend/
- **Test API**: Click vào các link trong trang documentation

## 📡 API Endpoints

### 1. Movies API

```
GET /api/movies.php?type=phim-bo&page=1&limit=20
```

**Parameters:**

- `type`: phim-bo, phim-le, hoat-hinh, tv-shows
- `page`: Số trang (default: 1)
- `limit`: Số lượng (default: 20)
- `category`: Slug thể loại (optional)
- `country`: Slug quốc gia (optional)
- `year`: Năm (optional)

### 2. Search API

```
GET /api/search.php?keyword=avatar&limit=5
```

**Parameters:**

- `keyword`: Từ khóa tìm kiếm (required)
- `page`: Số trang (default: 1)
- `limit`: Số lượng (default: 20)

### 3. Detail API

```
GET /api/detail.php?slug=avatar-2-dong-chay-cua-nuoc
```

**Parameters:**

- `slug`: Slug của phim (required)

## ⚙️ Cấu hình

File `config/config.php`:

- `CACHE_ENABLED`: Bật/tắt cache (default: true)
- `CACHE_TIME`: Thời gian cache (default: 3600s = 1 giờ)
- `ALLOWED_ORIGINS`: Danh sách origin được phép CORS

## 🧪 Test API

### Cách 1: Browser

Truy cập: http://localhost/HTHREE_film/backend/

### Cách 2: Postman

Import các endpoint và test

### Cách 3: Vue.js

Đổi URL trong `src/services/movieApi.js`:

```javascript
const BASE_URL = "http://localhost/HTHREE_film/backend/api";
```

## 📝 Tính năng

✅ **API Proxy** - Gọi phimapi.com qua PHP
✅ **Cache** - Lưu kết quả 1 giờ, giảm tải API
✅ **CORS** - Cho phép Vue.js gọi từ localhost:5173
✅ **Error Handling** - Xử lý lỗi đẹp
✅ **Clean Code** - OOP, dễ đọc, dễ bảo vệ đồ án

## 🔧 Troubleshooting

### Lỗi CORS

- Check `config/cors.php`
- Thêm origin của bạn vào `ALLOWED_ORIGINS`

### Lỗi Cache

- Xóa folder `cache/`
- Check quyền write folder

### API không hoạt động

- Check Apache đã start chưa
- Check URL có đúng không
- Check PHP version >= 7.0

## 📚 Tài liệu tham khảo

- API gốc: https://phimapi.com
- PHP Documentation: https://www.php.net/docs.php
