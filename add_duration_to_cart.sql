-- Thêm cột duration_months vào bảng cart
USE `hthree_film`;

-- Kiểm tra và thêm cột duration_months nếu chưa có
SET @query = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'hthree_film' 
     AND TABLE_NAME = 'cart' 
     AND COLUMN_NAME = 'duration_months') = 0,
    'ALTER TABLE `cart` ADD COLUMN `duration_months` INT DEFAULT 1 AFTER `quantity`',
    'SELECT "Column duration_months already exists" AS message'
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Cập nhật giá trị mặc định cho các row hiện có
UPDATE `cart` SET `duration_months` = 1 WHERE `duration_months` IS NULL OR `duration_months` = 0;

SELECT 'Đã thêm cột duration_months vào bảng cart!' as message;
