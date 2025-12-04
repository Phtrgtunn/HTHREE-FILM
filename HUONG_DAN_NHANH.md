# 🚀 HƯỚNG DẪN PUSH CODE LÊN GITHUB

## ⚠️ Lỗi "git is not recognized"?

Bạn đang dùng PowerShell, cần dùng **Git Bash** thay vì!

---

## ✅ CÁCH 1: Dùng Git Bash (Khuyến nghị)

### Bước 1: Mở Git Bash
1. Click chuột phải vào folder `HTHREE_film`
2. Chọn **"Git Bash Here"**
3. Cửa sổ Git Bash sẽ mở ra

### Bước 2: Chạy lệnh
Copy và paste vào Git Bash:

```bash
# Khởi tạo Git
git init

# Add tất cả files
git add .

# Commit
git commit -m "Initial commit - HTHREE Film Project"

# Đổi branch thành main
git branch -M main

# Kết nối với GitHub (THAY ĐỔI USERNAME VÀ REPO_NAME)
git remote add origin https://github.com/Phtrgtunn/HTHREE-film.git

# Push lên GitHub
git push -u origin main
```

### Bước 3: Nhập thông tin
- **Username**: `Phtrgtunn`
- **Password**: Dùng **Personal Access Token** (không phải password GitHub)

---

## ✅ CÁCH 2: Dùng GitHub Desktop (Dễ nhất)

### Bước 1: Mở GitHub Desktop
1. Mở GitHub Desktop
2. File → **Add Local Repository**
3. Chọn folder `D:\Download\Ampps\www\HTHREE_film`
4. Click **"Add Repository"**

### Bước 2: Publish
1. Click **"Publish repository"**
2. Đặt tên: `HTHREE-film`
3. Bỏ tick "Keep this code private" (để deploy free)
4. Click **"Publish repository"**

### Bước 3: Xong!
Code đã được push lên GitHub!

---

## ✅ CÁCH 3: Thêm Git vào PATH (Để dùng PowerShell)

### Bước 1: Tìm đường dẫn Git
Thường là: `C:\Program Files\Git\cmd`

### Bước 2: Thêm vào PATH
1. Mở **System Properties** (Win + Pause)
2. **Advanced system settings**
3. **Environment Variables**
4. Chọn **Path** → **Edit**
5. **New** → Thêm: `C:\Program Files\Git\cmd`
6. **OK** → **OK** → **OK**
7. **Restart PowerShell**

### Bước 3: Test
```powershell
git --version
```

Nếu hiện version → Thành công!

---

## 🔑 TẠO PERSONAL ACCESS TOKEN

1. Vào: https://github.com/settings/tokens
2. Click **"Generate new token"** → **"Generate new token (classic)"**
3. Đặt tên: `HTHREE Film Deploy`
4. Chọn quyền: **repo** (tất cả)
5. Click **"Generate token"**
6. **COPY TOKEN** (chỉ hiện 1 lần!)
7. Lưu vào Notepad để dùng lại

---

## 📝 SAU KHI PUSH XONG

### Kiểm tra trên GitHub:
1. Vào: https://github.com/Phtrgtunn/HTHREE-film
2. Refresh trang
3. Bạn sẽ thấy tất cả code!

### Deploy lên Vercel:
1. Vào: https://vercel.com/dashboard
2. Click **"New Project"**
3. **Import Git Repository**
4. Chọn **"Phtrgtunn/HTHREE-film"**
5. Click **"Deploy"**
6. Đợi 2-3 phút → Xong!

---

## 🆘 GẶP VẤN ĐỀ?

### Lỗi: "remote origin already exists"
```bash
git remote remove origin
git remote add origin https://github.com/Phtrgtunn/HTHREE-film.git
```

### Lỗi: "failed to push"
```bash
git pull origin main --rebase
git push -u origin main
```

### Lỗi: "authentication failed"
- Đảm bảo dùng **Personal Access Token** làm password
- KHÔNG dùng password GitHub

---

**🎉 Chúc bạn thành công!**
