import { defineStore } from 'pinia';
import { supabase } from '../../supabaseClient'; // Đảm bảo đường dẫn này đúng với vị trí của file supabaseClient.js của bạn so với file này (src/stores/RecommendedMovies/recommendedMovies.js -> src/supabaseClient.js)

// Định nghĩa Pinia store cho Recommended Movies
// Tên store là 'recommendedMovies'
export const useRecommendedMoviesStore = defineStore('recommendedMovies', {
  // State: dữ liệu của store
  state: () => ({
    recommended: [], // Mảng chứa danh sách phim đề cử
    loading: false,  // Trạng thái loading
    error: null      // Thông báo lỗi
  }),

  // Actions: các phương thức để thay đổi state hoặc thực hiện các thao tác bất đồng bộ
  actions: {
    // Action để lấy danh sách phim đề cử từ Supabase
    async getRecommendedMovies() {
      this.loading = true;
      this.error = null; // Reset lỗi

      try {
        // Truy vấn phim có điểm đánh giá trung bình cao nhất từ bảng 'movies'
        // ĐÃ SỬA: Tăng giới hạn lên 25 để lấy nhiều phim hơn
        const { data, error } = await supabase
          .from('movies')
          .select('*') 
          .order('vote_average', { ascending: false }) // Sắp xếp từ điểm cao nhất
          .limit(25); // GIỚI HẠN MỚI: 25 phim

        if (error) {
          throw error; 
        }
        
        this.recommended = data || []; 
        console.log("Pinia Store 'recommendedMovies': Phim đề cử đã tải từ Supabase:", this.recommended);

      } catch (error) {
        console.error("Lỗi khi lấy phim đề cử từ Supabase (Pinia Store):", error);
        this.error = error.message;
      } finally {
        this.loading = false; 
      }
    },

    // Action này là tùy chọn, chỉ dùng nếu bạn muốn đặt dữ liệu thủ công
    setRecommendedMovies(movies) {
      this.loading = false;
      this.error = null;
      this.recommended = movies;
      console.log("Pinia Store 'recommendedMovies': Dữ liệu phim đề cử đã được đặt thủ công:", this.recommended);
    },

    setLoading(isLoading) {
      this.loading = isLoading;
    },

    setError(errorMessage) {
      this.error = errorMessage;
    }
  },

  // Getters: các phương thức để truy cập dữ liệu state đã được tính toán hoặc lọc
  getters: {
    getRecommended: (state) => state.recommended,
    getLoading: (state) => state.loading,
    getError: (state) => state.error,
  }
});
