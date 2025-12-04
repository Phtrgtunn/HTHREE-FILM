// src/stores/Country/country.js
import { defineStore } from 'pinia';
import { supabase } from '../../supabaseClient'; // Đảm bảo đường dẫn này đúng với vị trí của file supabaseClient.js của bạn

// Định nghĩa Pinia store cho Country
export const useCountryStore = defineStore('country', {
  // State: dữ liệu của store
  state: () => ({
    countries: [], // Giờ đây sẽ chứa các countries duy nhất
    loading: false, // Trạng thái loading
    error: null,    // Thông báo lỗi
  }),

  // Actions: các phương thức để thay đổi state
  actions: {
    /**
     * Lấy danh sách countries duy nhất từ cột 'country' trong bảng 'movies' Supabase.
     */
    async getCountry() {
      this.loading = true;
      this.error = null;

      try {
        // Lấy tất cả các mảng 'country' từ bảng 'movies'
        const { data, error } = await supabase
          .from('movies')
          .select('country'); // Chỉ chọn cột 'country'

        if (error) {
          throw error; // Ném lỗi để bắt ở khối catch
        }

        // Xử lý dữ liệu để lấy các countries duy nhất và tạo slug
        let uniqueCountries = new Set();
        if (data) {
          data.forEach(movie => {
            if (movie.country && Array.isArray(movie.country)) {
              movie.country.forEach(country => {
                if (typeof country === 'string' && country.trim() !== '') {
                  uniqueCountries.add(country.trim());
                }
              });
            }
          });
        }

        // Chuyển Set thành Array, tạo object {name, slug} và sắp xếp
        this.countries = Array.from(uniqueCountries)
          .map(name => ({
            name: name,
            slug: name.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '') // Tạo slug từ tên country
          }))
          .sort((a, b) => a.name.localeCompare(b.name)); // Sắp xếp theo tên country

        console.log('Country Store: Đã tải countries từ Supabase (từ bảng movies):', this.countries);

      } catch (err) {
        console.error('Error fetching countries from Supabase (from movies table):', err.message);
        this.error = err.message;
        this.countries = []; // Xóa dữ liệu khi có lỗi
      } finally {
        this.loading = false; // Luôn tắt trạng thái loading, dù thành công hay thất bại
      }
    },
  },

  // Getters: các phương thức để truy xuất dữ liệu
  getters: {
    // Lấy tất cả countries
    getAllCountries: (state) => state.countries,
    // Lọc countries dựa trên từ khóa tìm kiếm
    filterCountries: (state) => (query) => {
      if (!query) return state.countries; // Trả về tất cả nếu query rỗng
      return state.countries.filter((item) =>
        item.name.toLowerCase().includes(query.toLowerCase())
      );
    },
    getLoading: (state) => state.loading,
    getError: (state) => state.error,
  },
});
