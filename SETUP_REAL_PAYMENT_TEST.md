# Hướng dẫn Setup Thanh toán VietQR Thật

## Vấn đề hiện tại
- Localhost không thể nhận webhook từ Casso
- Cần domain công khai để Casso gửi thông báo khi có giao dịch thật
- Hiện tại chỉ có simulation để test

## Giải pháp 1: Dùng ngrok (Recommended cho test)

### Bước 1: Cài đặt ngrok
1. Tải ngrok: https://ngrok.com/download
2. Đăng ký tài khoản ngrok (free)
3. Cài đặt ngrok

### Bước 2: Expose localhost ra internet
```bash
# Chạy backend trên localhost (AMPPS)
#