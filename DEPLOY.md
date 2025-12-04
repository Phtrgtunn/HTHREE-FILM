# 🚀 Hướng dẫn Deploy lên Vercel

## Bước 1: Xóa project cũ (nếu có)

1. Vào [Vercel Dashboard](https://vercel.com/dashboard)
2. Chọn project cũ
3. Settings → Delete Project

## Bước 2: Import project mới từ GitHub

1. Vào [Vercel](https://vercel.com)
2. Click "Add New..." → "Project"
3. Chọn repository: `Phtrgtunn/tun`
4. Click "Import"

## Bước 3: Configure Project

### Framework Preset

- Chọn: **Vite**

### Build Settings

- Build Command: `npm run build`
- Output Directory: `dist`
- Install Command: `npm install`

### Environment Variables

Thêm các biến sau (copy từ file `.env`):

```
VITE_SUPABASE_URL=https://hganinmpourhfimksydd.supabase.co
VITE_SUPABASE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImhnYW5pbm1wb3VyaGZpbWtzeWRkIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjI2Njk3NDAsImV4cCI6MjA3ODI0NTc0MH0.L0-LLStOhod_M4kKOQGGDJaq_BPAZkyS0af4aiZL8_U
VITE_FIREBASE_API_KEY=AIzaSyAiwMHCsSrqX20x8o90R5hksRxUPVmnwKM
VITE_FIREBASE_AUTH_DOMAIN=hthree-94a3e.firebaseapp.com
VITE_FIREBASE_PROJECT_ID=hthree-94a3e
VITE_FIREBASE_STORAGE_BUCKET=hthree-94a3e.firebasestorage.app
VITE_FIREBASE_MESSAGING_SENDER_ID=917487991713
VITE_FIREBASE_APP_ID=1:917487991713:web:68dd73293f38d7fe2229e0
VITE_FIREBASE_MEASUREMENT_ID=G-9LNBDMVK0M
```

## Bước 4: Deploy

1. Click "Deploy"
2. Đợi vài phút để build
3. Xong! 🎉

## Domain

Sau khi deploy xong, bạn sẽ có domain:

- `https://tun.vercel.app` (hoặc tên khác)

Có thể đổi tên domain trong Settings → Domains

## Lưu ý

- Mỗi lần push code lên GitHub, Vercel sẽ tự động deploy lại
- Nếu có lỗi, check Build Logs trên Vercel
- Environment Variables chỉ cần add 1 lần
