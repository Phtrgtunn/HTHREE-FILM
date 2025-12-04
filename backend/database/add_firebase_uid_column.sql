-- Thêm cột firebase_uid vào bảng users
ALTER TABLE users 
ADD COLUMN firebase_uid VARCHAR(255) NULL AFTER email,
ADD INDEX idx_firebase_uid (firebase_uid);

-- Update existing users (nếu có)
-- Bạn có thể update thủ công sau
