# 📤 Hướng dẫn Upload lên GitHub

## Bước 1: Cài đặt Git

### Cách 1: Download Git
1. Truy cập: https://git-scm.com/download/win
2. Download phiên bản 64-bit
3. Chạy file cài đặt
4. Chọn tất cả mặc định, click Next → Install
5. Restart terminal sau khi cài xong

### Cách 2: Dùng GitHub Desktop (Dễ hơn)
1. Download: https://desktop.github.com/
2. Cài đặt và đăng nhập GitHub
3. Kéo thả folder project vào
4. Click "Publish repository"

---

## Bước 2: Tạo Repository trên GitHub

1. Truy cập: https://github.com/new
2. Điền thông tin:
   - **Repository name**: `HTHREE_film` hoặc tên bạn muốn
   - **Description**: "Movie streaming website with Vue.js"
   - **Public** hoặc **Private** (tùy chọn)
   - **KHÔNG** tick "Add a README file"
3. Click **"Create repository"**
4. Copy URL repository (dạng: `https://github.com/username/HTHREE_film.git`)

---

## Bước 3: Upload code lên GitHub

### Mở PowerShell/CMD trong folder project và chạy:

```bash
# 1. Khởi tạo Git
git init

# 2. Cấu hình thông tin (lần đầu tiên)
git config --global user.name "Tên của bạn"
git config --global user.email "email@example.com"

# 3. Thêm tất cả files
git add .

# 4. Commit
git commit -m "Initial commit - HTHREE Film Project"

# 5. Đổi tên branch thành main
git branch -M main

# 6. Kết nối với GitHub (thay YOUR_USERNAME và REPO_NAME)
git remote add origin https://github.com/YOUR_USERNAME/HTHREE_film.git

# 7. Push code lên GitHub
git push -u origin main
```

### Nếu gặp lỗi authentication:
1. Vào GitHub → Settings → Developer settings
2. Personal access tokens → Tokens (classic)
3. Generate new token (classic)
4. Chọn quyền: `repo`, `workflow`
5. Copy token
6. Khi push, dùng token làm password

---

## Bước 4: Kiểm tra

1. Refresh trang GitHub repository
2. Bạn sẽ thấy tất cả code đã được upload
3. Sẵn sàng deploy lên Vercel!

---

## 🔄 Cập nhật code sau này

Mỗi khi có thay đổi:

```bash
git add .
git commit -m "Mô tả thay đổi"
git push
```

---

## ⚠️ Lưu ý

### File `.gitignore` đã có sẵn để bỏ qua:
- `node_modules/` - Dependencies
- `.env` - Environment variables (bảo mật)
- `dist/` - Build output
- `backend/` - PHP backend (deploy riêng)

### Nếu muốn upload backend:
Xóa dòng `backend/` trong file `.gitignore`

---

## 🆘 Troubleshooting

### Lỗi: "fatal: not a git repository"
```bash
git init
```

### Lỗi: "remote origin already exists"
```bash
git remote remove origin
git remote add origin https://github.com/YOUR_USERNAME/HTHREE_film.git
```

### Lỗi: "failed to push"
```bash
git pull origin main --rebase
git push -u origin main
```

### Quên đã commit gì?
```bash
git log --oneline
```

---

## ✅ Sau khi upload xong

Tiếp tục với Vercel:
1. Vào https://vercel.com/dashboard
2. Click "Add New" → "Project"
3. Import từ GitHub
4. Chọn repository `HTHREE_film`
5. Click "Deploy"

**🎉 Xong!**
