import { defineStore } from 'pinia';
import { supabase } from '../../supabaseClient';

export const useMovieCategoriesStore = defineStore('movieCategories', {
  state: () => ({
    newMovies: [],
    popularMovies: [],
    topRatedMovies: [],
    actionMovies: [],
    loading: false,
    error: null
  }),

  actions: {
    // Lấy phim mới nhất
    async getNewMovies(limit = 15) {
      this.loading = true;
      try {
        const { data, error } = await supabase
          .from('movies')
          .select('*')
          .order('created_at', { ascending: false })
          .limit(limit);

        if (error) throw error;
        this.newMovies = data || [];
      } catch (err) {
        console.error('Error fetching new movies:', err);
        this.error = err.message;
      } finally {
        this.loading = false;
      }
    },

    // Lấy phim phổ biến (theo vote_count)
    async getPopularMovies(limit = 15) {
      this.loading = true;
      try {
        const { data, error } = await supabase
          .from('movies')
          .select('*')
          .order('vote_count', { ascending: false })
          .limit(limit);

        if (error) throw error;
        this.popularMovies = data || [];
      } catch (err) {
        console.error('Error fetching popular movies:', err);
        this.error = err.message;
      } finally {
        this.loading = false;
      }
    },

    // Lấy phim đánh giá cao
    async getTopRatedMovies(limit = 15) {
      this.loading = true;
      try {
        const { data, error } = await supabase
          .from('movies')
          .select('*')
          .order('vote_average', { ascending: false })
          .limit(limit);

        if (error) throw error;
        this.topRatedMovies = data || [];
      } catch (err) {
        console.error('Error fetching top rated movies:', err);
        this.error = err.message;
      } finally {
        this.loading = false;
      }
    },

    // Lấy phim theo thể loại (giả sử có cột genres là array)
    async getMoviesByGenre(genre, limit = 15) {
      this.loading = true;
      try {
        const { data, error } = await supabase
          .from('movies')
          .select('*')
          .contains('genres', [genre])
          .limit(limit);

        if (error) throw error;
        return data || [];
      } catch (err) {
        console.error(`Error fetching ${genre} movies:`, err);
        this.error = err.message;
        return [];
      } finally {
        this.loading = false;
      }
    },

    // Load tất cả categories cùng lúc
    async loadAllCategories() {
      await Promise.all([
        this.getNewMovies(),
        this.getPopularMovies(),
        this.getTopRatedMovies()
      ]);
    }
  },

  getters: {
    getNewMovies: (state) => state.newMovies,
    getPopularMovies: (state) => state.popularMovies,
    getTopRatedMovies: (state) => state.topRatedMovies,
    getLoading: (state) => state.loading,
    getError: (state) => state.error
  }
});
