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
    async getCategory() {
      this.loading = true;
      this.error = null;
      console.log('CategoryStore: Bắt đầu fetch genres từ bảng movies...');
      try {
        // Lấy tất cả các mảng 'genres' từ bảng 'movies'
        // Chúng ta chỉ cần cột 'genres', Supabase sẽ trả về một mảng các đối tượng { genres: [...] }
        const { data, error } = await supabase
          .from('movies')
          .select('genres'); // Chỉ chọn cột 'genres'

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

      } catch (err) {
        this.error = err.message;
        console.error('CategoryStore: Lỗi khi fetch genres (từ bảng movies):', err.message);
      } finally {
        this.loading = false;
        console.log('CategoryStore: Kết thúc fetch genres. Loading:', this.loading);
      }
    },
  },

  getters: {
    getAllCategories: (state) => state.categories,
    getLoading: (state) => state.loading,
    getError: (state) => state.error,
  },
});
