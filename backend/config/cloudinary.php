<?php
/**
 * Cloudinary Configuration
 * 
 * HƯỚNG DẪN:
 * 1. Đăng ký tài khoản free tại: https://cloudinary.com/users/register_free
 * 2. Lấy thông tin từ Dashboard: https://console.cloudinary.com/
 * 3. Thay đổi các giá trị bên dưới
 * 4. Hoặc set environment variables trên Railway:
 *    - CLOUDINARY_CLOUD_NAME
 *    - CLOUDINARY_API_KEY
 *    - CLOUDINARY_API_SECRET
 */

return [
    'cloud_name' => getenv('CLOUDINARY_CLOUD_NAME') ?: 'your_cloud_name_here',
    'api_key' => getenv('CLOUDINARY_API_KEY') ?: 'your_api_key_here',
    'api_secret' => getenv('CLOUDINARY_API_SECRET') ?: 'your_api_secret_here',
];
