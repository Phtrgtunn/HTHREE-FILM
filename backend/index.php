<?php
/**
 * Backend Entry Point
 * Simple API documentation
 */

require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTHREE Film - PHP Backend API</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 { color: #667eea; margin-bottom: 10px; }
        h2 { color: #764ba2; margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }
        .endpoint {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #667eea;
        }
        .method { 
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-right: 10px;
        }
        code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #d63384;
        }
        .status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            margin-top: 10px;
        }
        .status.ok { background: #d4edda; color: #155724; }
        ul { margin-left: 20px; margin-top: 10px; }
        li { margin: 8px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎬 HTHREE Film - PHP Backend API</h1>
        <p><strong>Version:</strong> 1.0.0</p>
        <p><strong>Base URL:</strong> <code>http://localhost/HTHREE_film/backend</code></p>
        <div class="status ok">✓ API is running</div>

        <h2>📚 API Endpoints</h2>

        <div class="endpoint">
            <span class="method">GET</span>
            <strong>/api/movies.php</strong>
            <p>Lấy danh sách phim theo loại</p>
            <p><strong>Parameters:</strong></p>
            <ul>
                <li><code>type</code> - Loại phim: phim-bo, phim-le, hoat-hinh, tv-shows</li>
                <li><code>page</code> - Trang (default: 1)</li>
                <li><code>limit</code> - Số lượng (default: 20)</li>
                <li><code>category</code> - Slug thể loại (optional)</li>
                <li><code>country</code> - Slug quốc gia (optional)</li>
                <li><code>year</code> - Năm phát hành (optional)</li>
            </ul>
            <p><strong>Example:</strong> <code>/api/movies.php?type=phim-bo&page=1&limit=20</code></p>
        </div>

        <div class="endpoint">
            <span class="method">GET</span>
            <strong>/api/search.php</strong>
            <p>Tìm kiếm phim theo từ khóa</p>
            <p><strong>Parameters:</strong></p>
            <ul>
                <li><code>keyword</code> - Từ khóa tìm kiếm (required)</li>
                <li><code>page</code> - Trang (default: 1)</li>
                <li><code>limit</code> - Số lượng (default: 20)</li>
            </ul>
            <p><strong>Example:</strong> <code>/api/search.php?keyword=avatar&limit=5</code></p>
        </div>

        <div class="endpoint">
            <span class="method">GET</span>
            <strong>/api/detail.php</strong>
            <p>Lấy chi tiết phim theo slug</p>
            <p><strong>Parameters:</strong></p>
            <ul>
                <li><code>slug</code> - Slug của phim (required)</li>
            </ul>
            <p><strong>Example:</strong> <code>/api/detail.php?slug=avatar-2-dong-chay-cua-nuoc</code></p>
        </div>

        <h2>⚙️ Configuration</h2>
        <ul>
            <li><strong>Cache:</strong> <?= CACHE_ENABLED ? 'Enabled (' . CACHE_TIME . 's)' : 'Disabled' ?></li>
            <li><strong>API Base:</strong> <?= API_BASE_URL ?></li>
            <li><strong>CDN Image:</strong> <?= API_CDN_IMAGE ?></li>
        </ul>

        <h2>🧪 Test API</h2>
        <p>Click vào các link dưới để test API:</p>
        <ul>
            <li><a href="api/movies.php?type=phim-bo&page=1&limit=5" target="_blank">Test Movies API</a></li>
            <li><a href="api/search.php?keyword=avatar&limit=5" target="_blank">Test Search API</a></li>
            <li><a href="api/detail.php?slug=avatar-2-dong-chay-cua-nuoc" target="_blank">Test Detail API</a></li>
        </ul>

        <h2>📝 Notes</h2>
        <ul>
            <li>CORS đã được cấu hình cho Vue.js frontend (localhost:5173)</li>
            <li>Cache được lưu trong folder <code>cache/</code></li>
            <li>Tất cả response đều ở định dạng JSON</li>
        </ul>
    </div>
</body>
</html>
