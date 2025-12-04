import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
// import tailwindcss from '@tailwindcss/vite'; // Xóa hoặc comment dòng này
import path from 'path';

export default defineConfig({
  base: './', // Relative path for AMPPS/local deployment
  plugins: [
    vue(), // Sử dụng plugin Vue
    // tailwindcss(), // Xóa hoặc comment dòng này
  ],
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"), // để @ trỏ tới thư mục src
    },
  },
  css: {
    // Thêm cấu hình PostCSS nếu bạn muốn tùy chỉnh thêm,
    // nhưng với file postcss.config.cjs, Vite sẽ tự động tìm và sử dụng nó.
    // postcss: {
    //   plugins: [
    //     require('tailwindcss'),
    //     require('autoprefixer'),
    //   ],
    // },
  },
});