# 🖼️ SỬA LỖI LOAD ẢNH

## Nguyên nhân:
- API `img.phimapi.com` bị chặn CORS
- Connection reset khi load nhiều ảnh cùng lúc
- Một số ảnh không tồn tại

## ✅ Giải pháp đã áp dụng:

### 1. Đã tạo file `src/utils/imageHelper.js`
Helper function xử lý ảnh an toàn với fallback

### 2. Thay đổi domain
- **Cũ**: `img.phimapi.com` (hay bị lỗi)
- **Mới**: `phimimg.com` (ổn định hơn)

## 🔧 Cách sửa thủ công:

### Tìm và thay thế trong tất cả files:

```
img.phimapi.com  →  phimimg.com
```

### Hoặc chạy lệnh:

```bash
# PowerShell
Get-ChildItem -Path src -Recurse -Include *.vue,*.js | ForEach-Object { (Get-Content $_.FullName) -replace 'img\.phimapi\.com', 'phimimg.com' | Set-Content $_.FullName }
```

```bash
# Git Bash
find src -type f \( -name "*.vue" -o -name "*.js" \) -exec sed -i 's/img\.phimapi\.com/phimimg.com/g' {} +
```

## 📝 Files cần sửa:

- `src/components/MovieRow.vue`
- `src/components/BannerCarousel.vue`
- `src/components/FeaturedCarousel.vue`
- `src/components/MovieDetailModal.vue`
- `src/pages/MovieDetail.vue`
- `src/shared/MovieCardRecommended.vue`

## ⚠️ Lưu ý:

Nếu vẫn còn lỗi, thêm `onerror` handler vào tất cả `<img>` tags:

```vue
<img 
  :src="imageUrl"
  @error="(e) => e.target.src = 'https://placehold.co/300x450/1f2937/fbbf24?text=No+Image'"
  alt="Movie poster"
/>
```

## 🚀 Sau khi sửa:

```bash
# Build lại
npm run build

# Push lên GitHub
git add .
git commit -m "Fix image loading errors"
git push
```

Vercel sẽ tự động deploy!
