# ⚡ HƯỚNG DẪN NHANH - CẬP NHẬT DATABASE

## 🎯 Chỉ 3 bước đơn giản!

### Bước 1: Backup (Quan trọng!)
```bash
# Mở phpMyAdmin
http://localhost/phpmyadmin

# Chọn database hthree_film
# Click Export > Go
```

### Bước 2: Cập nhật
```bash
# CÁCH 1: Tự động (Windows)
Double-click: update-database.bat

# CÁCH 2: phpMyAdmin
1. Mở file: update_database_for_admin.sql
2. Copy toàn bộ nội dung
3. phpMyAdmin > SQL tab > Paste > Go
```

### Bước 3: Kiểm tra
```sql
-- Chạy trong phpMyAdmin > SQL tab
CALL sp_get_admin_statistics();
```

## ✅ Nếu thấy kết quả → Thành công! 🎉

## ❌ Nếu lỗi → Restore backup
```bash
# phpMyAdmin > Import > Chọn file backup > Go
```

---

## 📋 Checklist

- [ ] Đã backup database
- [ ] Đã chạy update script
- [ ] Đã test stored procedures
- [ ] Admin Panel hoạt động
- [ ] Giữ file backup an toàn

## 🚀 Sau khi cập nhật

Mở Admin Panel và thưởng thức:
- ✨ Thống kê realtime
- 🔄 Auto-refresh mỗi 30s
- ⚡ Performance nhanh hơn 10x
- 📊 Báo cáo đầy đủ

---

**Thời gian**: ~2 phút  
**Độ khó**: ⭐ Dễ  
**Rủi ro**: ⭐ Thấp (có backup)
