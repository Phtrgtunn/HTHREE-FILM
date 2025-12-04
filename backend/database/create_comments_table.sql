-- Tạo bảng comments
USE hthree_film;

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_slug VARCHAR(255) DEFAULT NULL,
    parent_id INT DEFAULT NULL,
    content TEXT NOT NULL,
    likes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
    INDEX idx_movie_slug (movie_slug),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert dữ liệu mẫu
INSERT INTO comments (user_id, movie_slug, content, likes) VALUES
(1, 'hoc-vien-sieu-anh-hung-mua-8', 'Phim hay quá! Đáng xem! Cảnh chiến đấu rất hoành tráng', 234),
(2, 'robin-hood', 'Diễn xuất tuyệt vời, cảnh quay đẹp mắt. Rất thích phong cách làm phim này', 189),
(1, 'son-than-di-van-luc', 'Phim Trung Quốc hay nhất năm nay theo mình', 156),
(2, 'hiep-si-mat-ng-zzt7', 'Tokusatsu đỉnh cao! Nostalgia quá đi', 142),
(1, NULL, 'Trang web này rất tốt, giao diện đẹp! Cảm ơn team đã làm ra', 128),
(2, 'colorful-stage-mot-miku-khong-the-hat', 'Miku chan kawaii quá! Anime tuyệt vời', 98),
(1, 'thong-linh-phi-phan-2-ban-anime', 'Phần 2 hay hơn phần 1 nhiều', 87),
(2, 'vuong-quyen-david-phan-2', 'Đang chờ phần 3 nè!', 76);

-- Kiểm tra
SELECT 'Comments table created successfully!' as message;
SELECT COUNT(*) as total_comments FROM comments;
