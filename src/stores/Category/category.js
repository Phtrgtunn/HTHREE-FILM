// src/stores/Category/category.js
import { defineStore } from 'pinia';
import { supabase } from '../../supabaseClient'; // Đảm bảo đường dẫn này đúng với vị trí của file supabaseClient.js của bạn

// Định nghĩa Pinia store cho Genres (thể loại)
export const useCategoryStore = defineStore('category', {
  state: () => ({
    categories: [], // Sẽ chứa các genres duy nhất
    loading: false, // Trạng thái loading
    error: null,    // Thông báo lỗi
  }),

  actions: {
    /**
     * Lấy danh sách genres duy nhất từ cột 'genres' trong bảng 'movies' của Supabase.
     */
    async getCategory(retries = 3) {
      this.loading = true;
      this.error = null;
      console.log('CategoryStore: Bắt đầu fetch genres từ bảng movies...');
      
      let lastError = null;
      
      // Retry logic
      for (let attempt = 1; attempt <= retries; attempt++) {
        try {
          console.log(`CategoryStore: Attempt ${attempt}/${retries}`);
          
          // Lấy tất cả các mảng 'genres' từ bảng 'movies'
          // Chúng ta chỉ cần cột 'genres', Supabase sẽ trả về một mảng các đối tượng { genres: [...] }
          const { data, error } = await Promise.race([
            supabase.from('movies').select('genres'),
            new Promise((_, reject) => 
              setTimeout(() => reject(new Error('Request timeout')), 10000)
            )
          ]);

          if (error) {
            throw error; // Ném lỗi để bắt ở khối catch
          }

        // Xử lý dữ liệu để lấy các genres duy nhất và tạo slug
        let uniqueGenres = new Set();
        if (data) {
          data.forEach(movie => {
            // Đảm bảo movie.genres tồn tại và là một mảng
            if (movie.genres && Array.isArray(movie.genres)) {
              movie.genres.forEach(genre => {
                // Đảm bảo mỗi genre là một chuỗi và không rỗng
                if (typeof genre === 'string' && genre.trim() !== '') {
                  uniqueGenres.add(genre.trim());
                }
              });
            }
          });
        }

        // Chuyển Set thành Array, tạo object {name, slug} và sắp xếp theo tên
        this.categories = Array.from(uniqueGenres)
          .map(name => ({
            name: name,
            slug: name.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '') // Tạo slug từ tên genre, loại bỏ ký tự đặc biệt
          }))
          .sort((a, b) => a.name.localeCompare(b.name)); // Sắp xếp theo tên genre (alphabetical)

          console.log('CategoryStore: Genres fetched thành công (từ bảng movies). Số lượng:', this.categories.length, 'Dữ liệu:', this.categories);
          
          // Success - break retry loop
          this.loading = false;
          return;

        } catch (err) {
          lastError = err;
          console.error(`CategoryStore: Lỗi attempt ${attempt}/${retries}:`, err.message);
          
          // If not last attempt, wait before retry
          if (attempt < retries) {
            const delay = attempt * 1000; // Exponential backoff: 1s, 2s, 3s
            console.log(`CategoryStore: Retry sau ${delay}ms...`);
            await new Promise(resolve => setTimeout(resolve, delay));
          }
        }
      }
      
      // All retries failed
      this.error = lastError?.message || 'Failed to fetch genres after multiple attempts';
      this.loading = false;
      
      // Use fallback data if available
      if (this.categories.length === 0) {
        console.warn('CategoryStore: Sử dụng fallback genres');
        this.categories = this.getFallbackCategories();
      }
      
      console.log('CategoryStore: Kết thúc fetch genres. Loading:', this.loading);
    },
    
    /**
     * Fallback categories khi không thể fetch từ Supabase
     */
    getFallbackCategories() {
      return [
        { name: 'Hành Động', slug: 'hanh-dong' },
        { name: 'Hài Hước', slug: 'hai-huoc' },
        { name: 'Tình Cảm', slug: 'tinh-cam' },
        { name: 'Kinh Dị', slug: 'kinh-di' },
        { name: 'Khoa Học Viễn Tưởng', slug: 'khoa-hoc-vien-tuong' },
        { name: 'Phiêu Lưu', slug: 'phieu-luu' },
        { name: 'Hoạt Hình', slug: 'hoat-hinh' },
        { name: 'Tài Liệu', slug: 'tai-lieu' },
        { name: 'Gia Đình', slug: 'gia-dinh' },
        { name: 'Bí Ẩn', slug: 'bi-an' }
      ].sort((a, b) => a.name.localeCompare(b.name));
    },
  },

  getters: {
    getAllCategories: (state) => state.categories,
    getLoading: (state) => state.loading,
    getError: (state) => state.error,
  },
});
