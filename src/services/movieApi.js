import axios from 'axios';
import { apiCache } from '@/utils/apiCache';

// Chuyển đổi giữa API trực tiếp và PHP backend
const USE_PHP_BACKEND = false; // Dùng API trực tiếp từ phimapi.com

const BASE_URL = USE_PHP_BACKEND 
  ? 'http://localhost/HTHREE_film/backend/api'
  : 'https://phimapi.com/v1/api';

/**
 * Lấy danh sách phim từ phimapi.com
 * @param {Object} params - Query parameters
 * @param {string} params.type_list - Loại phim: 'phim-bo', 'phim-le', 'hoat-hinh', 'tv-shows'
 * @param {number} params.page - Trang (default: 1)
 * @param {string} params.sort_field - Field để sort: '_id', 'modified.time', 'year'
 * @param {string} params.sort_type - Kiểu sort: 'asc', 'desc'
 * @param {string} params.sort_lang - Ngôn ngữ: 'vi', 'en', 'cn'
 * @param {string} params.category - Slug thể loại: 'hanh-dong', 'tinh-cam', etc.
 * @param {string} params.country - Slug quốc gia: 'trung-quoc', 'han-quoc', etc.
 * @param {number} params.year - Năm phát hành
 * @param {number} params.limit - Số lượng phim (default: 20)
 * @returns {Promise} Response data
 */
export const getMovieList = async (params = {}) => {
  const {
    type_list = 'phim-bo',
    page = 1,
    sort_field = '_id',
    sort_type = 'desc',
    sort_lang,
    category,
    country,
    year,
    limit = 20
  } = params;

  try {
    if (USE_PHP_BACKEND) {
      // Gọi PHP backend
      const queryParams = new URLSearchParams({
        type: type_list,
        page: page.toString(),
        sort_field,
        sort_type,
        limit: limit.toString()
      });

      if (sort_lang) queryParams.append('sort_lang', sort_lang);
      if (category) queryParams.append('category', category);
      if (country) queryParams.append('country', country);
      if (year) queryParams.append('year', year.toString());

      const url = `${BASE_URL}/movies.php?${queryParams.toString()}`;
      console.log('📡 Fetching movies (PHP):', url);

      // Use cache with 5 minutes TTL
      return await apiCache.get(url, async () => {
        const response = await axios.get(url);
        return response.data;
      }, 5 * 60 * 1000);
    } else {
      // Gọi API trực tiếp
      const queryParams = new URLSearchParams({
        page: page.toString(),
        sort_field,
        sort_type,
        limit: limit.toString()
      });

      if (sort_lang) queryParams.append('sort_lang', sort_lang);
      if (category) queryParams.append('category', category);
      if (country) queryParams.append('country', country);
      if (year) queryParams.append('year', year.toString());

      const url = `${BASE_URL}/danh-sach/${type_list}?${queryParams.toString()}`;
      console.log('📡 Fetching movies:', url);

      // Use cache with 5 minutes TTL
      return await apiCache.get(url, async () => {
        const response = await axios.get(url);
        return response.data;
      }, 5 * 60 * 1000);
    }
  } catch (error) {
    console.error('❌ Error fetching movies:', error);
    throw error;
  }
};

/**
 * Chuyển đổi ảnh sang WEBP
 * @param {string} imageUrl - Liên kết ảnh từ KKPhim
 * @returns {string} URL ảnh đã chuyển đổi sang WEBP
 */
export const convertToWebP = (imageUrl) => {
  if (!imageUrl) return 'https://placehold.co/400x600/1a1a1a/fff?text=No+Image';
  
  // Nếu đã là URL đầy đủ, encode và chuyển đổi
  if (imageUrl.startsWith('http')) {
    return `https://phimapi.com/image.php?url=${encodeURIComponent(imageUrl)}`;
  }
  
  // Nếu là relative path, thêm domain trước
  const fullUrl = `https://phimimg.com/${imageUrl}`;
  return `https://phimapi.com/image.php?url=${encodeURIComponent(fullUrl)}`;
};

/**
 * Map movie data từ API sang format component
 */
export const mapMovieData = (item) => ({
  ...item,
  backdrop_path: item.thumb_url || 'https://placehold.co/400x600/1a1a1a/fff?text=No+Image',
  poster_path: item.poster_url || item.thumb_url,
  title: item.name,
  original_title: item.origin_name
});

/**
 * Lấy phim bộ
 */
export const getPhimBo = (params = {}) => {
  return getMovieList({ ...params, type_list: 'phim-bo' });
};

/**
 * Lấy phim lẻ
 */
export const getPhimLe = (params = {}) => {
  return getMovieList({ ...params, type_list: 'phim-le' });
};

/**
 * Lấy hoạt hình
 */
export const getHoatHinh = (params = {}) => {
  return getMovieList({ ...params, type_list: 'hoat-hinh' });
};

/**
 * Lấy TV shows
 */
export const getTVShows = (params = {}) => {
  return getMovieList({ ...params, type_list: 'tv-shows' });
};

/**
 * Lấy phim theo thể loại
 */
export const getMoviesByCategory = (category, params = {}) => {
  return getMovieList({ ...params, category });
};

/**
 * Lấy phim theo quốc gia
 */
export const getMoviesByCountry = (country, params = {}) => {
  return getMovieList({ ...params, country });
};

/**
 * Lấy phim theo năm
 */
export const getMoviesByYear = (year, params = {}) => {
  return getMovieList({ ...params, year });
};

/**
 * Lấy phim mới cập nhật (endpoint khác)
 * @param {number} page - Trang
 * @returns {Promise} Response data
 */
export const getPhimMoiCapNhat = async (page = 1) => {
  try {
    const url = USE_PHP_BACKEND
      ? `${BASE_URL}/movies.php?type=phim-moi-cap-nhat&page=${page}`
      : `https://phimapi.com/danh-sach/phim-moi-cap-nhat?page=${page}`;
    
    console.log('📡 Fetching phim mới cập nhật:', url);
    
    const response = await axios.get(url);
    return response.data;
  } catch (error) {
    console.error('❌ Error fetching phim mới cập nhật:', error);
    throw error;
  }
};

/**
 * Lấy chi tiết phim theo slug
 * @param {string} slug - Slug của phim (vd: 'avatar-2', 'squid-game')
 * @returns {Promise} Response data với thông tin đầy đủ của phim
 */
export const getMovieDetail = async (slug) => {
  try {
    const url = USE_PHP_BACKEND
      ? `${BASE_URL}/detail.php?slug=${slug}`
      : `https://phimapi.com/phim/${slug}`;
    
    console.log('📡 Fetching movie detail:', url);
    
    const response = await axios.get(url);
    return response.data;
  } catch (error) {
    console.error('❌ Error fetching movie detail:', error);
    throw error;
  }
};

/**
 * Lấy danh sách tất cả thể loại
 * @returns {Promise} Response data với danh sách thể loại
 */
export const getCategories = async () => {
  try {
    const url = 'https://phimapi.com/the-loai';
    console.log('📡 Fetching categories:', url);
    
    const response = await axios.get(url);
    return response.data;
  } catch (error) {
    console.error('❌ Error fetching categories:', error);
    throw error;
  }
};

/**
 * Lấy chi tiết phim theo thể loại
 * @param {string} type_list - Slug thể loại (vd: 'hanh-dong', 'tinh-cam', 'hai-huoc')
 * @param {Object} params - Query parameters
 * @param {number} params.page - Trang (default: 1)
 * @param {string} params.sort_field - Field để sort: '_id', 'modified.time', 'year'
 * @param {string} params.sort_type - Kiểu sort: 'asc', 'desc'
 * @param {string} params.sort_lang - Ngôn ngữ: 'vi', 'en', 'cn'
 * @param {string} params.country - Slug quốc gia
 * @param {number} params.year - Năm phát hành
 * @param {number} params.limit - Số lượng phim (default: 20)
 * @returns {Promise} Response data với danh sách phim theo thể loại
 */
export const getCategoryDetail = async (type_list, params = {}) => {
  try {
    const {
      page = 1,
      sort_field,
      sort_type,
      sort_lang,
      country,
      year,
      limit = 20
    } = params;

    // Build query string
    const queryParams = new URLSearchParams({
      page: page.toString(),
      limit: limit.toString()
    });

    // Add optional params
    if (sort_field) queryParams.append('sort_field', sort_field);
    if (sort_type) queryParams.append('sort_type', sort_type);
    if (sort_lang) queryParams.append('sort_lang', sort_lang);
    if (country) queryParams.append('country', country);
    if (year) queryParams.append('year', year.toString());

    const url = `https://phimapi.com/v1/api/the-loai/${type_list}?${queryParams.toString()}`;
    console.log('📡 Fetching category detail:', url);
    
    const response = await axios.get(url);
    return response.data;
  } catch (error) {
    console.error('❌ Error fetching category detail:', error);
    throw error;
  }
};

/**
 * Lấy danh sách tất cả quốc gia
 * @returns {Promise} Response data với danh sách quốc gia
 */
export const getCountries = async () => {
  try {
    const url = 'https://phimapi.com/quoc-gia';
    console.log('📡 Fetching countries:', url);
    
    const response = await axios.get(url);
    return response.data;
  } catch (error) {
    console.error('❌ Error fetching countries:', error);
    throw error;
  }
};

/**
 * Lấy chi tiết phim theo quốc gia
 * @param {string} type_list - Slug quốc gia (vd: 'han-quoc', 'trung-quoc', 'nhat-ban')
 * @param {Object} params - Query parameters
 * @param {number} params.page - Trang (default: 1)
 * @param {string} params.sort_field - Field để sort: '_id', 'modified.time', 'year'
 * @param {string} params.sort_type - Kiểu sort: 'asc', 'desc'
 * @param {string} params.sort_lang - Ngôn ngữ: 'vi', 'en', 'cn'
 * @param {string} params.category - Slug thể loại
 * @param {number} params.year - Năm phát hành
 * @param {number} params.limit - Số lượng phim (default: 20)
 * @returns {Promise} Response data với danh sách phim theo quốc gia
 */
export const getCountryDetail = async (type_list, params = {}) => {
  try {
    const {
      page = 1,
      sort_field,
      sort_type,
      sort_lang,
      category,
      year,
      limit = 20
    } = params;

    // Build query string
    const queryParams = new URLSearchParams({
      page: page.toString(),
      limit: limit.toString()
    });

    // Add optional params
    if (sort_field) queryParams.append('sort_field', sort_field);
    if (sort_type) queryParams.append('sort_type', sort_type);
    if (sort_lang) queryParams.append('sort_lang', sort_lang);
    if (category) queryParams.append('category', category);
    if (year) queryParams.append('year', year.toString());

    const url = `https://phimapi.com/v1/api/quoc-gia/${type_list}?${queryParams.toString()}`;
    console.log('📡 Fetching country detail:', url);
    
    const response = await axios.get(url);
    return response.data;
  } catch (error) {
    console.error('❌ Error fetching country detail:', error);
    throw error;
  }
};

/**
 * Lấy chi tiết phim theo năm
 * @param {string|number} type_list - Năm (vd: '2024', '2023', 2024)
 * @param {Object} params - Query parameters
 * @param {number} params.page - Trang (default: 1)
 * @param {string} params.sort_field - Field để sort: '_id', 'modified.time', 'year'
 * @param {string} params.sort_type - Kiểu sort: 'asc', 'desc'
 * @param {string} params.sort_lang - Ngôn ngữ: 'vi', 'en', 'cn'
 * @param {string} params.category - Slug thể loại
 * @param {string} params.country - Slug quốc gia
 * @param {number} params.limit - Số lượng phim (default: 20)
 * @returns {Promise} Response data với danh sách phim theo năm
 */
export const getYearDetail = async (type_list, params = {}) => {
  try {
    const {
      page = 1,
      sort_field,
      sort_type,
      sort_lang,
      category,
      country,
      limit = 20
    } = params;

    // Build query string
    const queryParams = new URLSearchParams({
      page: page.toString(),
      limit: limit.toString()
    });

    // Add optional params
    if (sort_field) queryParams.append('sort_field', sort_field);
    if (sort_type) queryParams.append('sort_type', sort_type);
    if (sort_lang) queryParams.append('sort_lang', sort_lang);
    if (category) queryParams.append('category', category);
    if (country) queryParams.append('country', country);

    const url = `https://phimapi.com/v1/api/nam/${type_list}?${queryParams.toString()}`;
    console.log('📡 Fetching year detail:', url);
    
    const response = await axios.get(url);
    return response.data;
  } catch (error) {
    console.error('❌ Error fetching year detail:', error);
    throw error;
  }
};

/**
 * Tìm kiếm phim theo từ khóa
 * @param {string} keyword - Từ khóa tìm kiếm
 * @param {Object} params - Query parameters (page, limit, category, country, year, etc.)
 * @returns {Promise} Response data với danh sách phim tìm được
 */
export const searchMovies = async (keyword, params = {}) => {
  try {
    const {
      page = 1,
      limit = 20,
      sort_field,
      sort_type,
      sort_lang,
      category,
      country,
      year
    } = params;

    // Build query string
    const queryParams = new URLSearchParams({
      keyword: keyword,
      page: page.toString(),
      limit: limit.toString()
    });

    // Add optional params
    if (sort_field) queryParams.append('sort_field', sort_field);
    if (sort_type) queryParams.append('sort_type', sort_type);
    if (sort_lang) queryParams.append('sort_lang', sort_lang);
    if (category) queryParams.append('category', category);
    if (country) queryParams.append('country', country);
    if (year) queryParams.append('year', year.toString());

    const url = USE_PHP_BACKEND
      ? `${BASE_URL}/search.php?${queryParams.toString()}`
      : `https://phimapi.com/v1/api/tim-kiem?${queryParams.toString()}`;
    
    console.log('🔍 Searching movies:', url);

    // Use cache with 2 minutes TTL for search results
    return await apiCache.get(url, async () => {
      const response = await axios.get(url);
      return response.data;
    }, 2 * 60 * 1000);
  } catch (error) {
    console.error('❌ Error searching movies:', error);
    throw error;
  }
};

export default {
  getMovieList,
  mapMovieData,
  convertToWebP,
  getPhimBo,
  getPhimLe,
  getHoatHinh,
  getTVShows,
  getMoviesByCategory,
  getMoviesByCountry,
  getMoviesByYear,
  getPhimMoiCapNhat,
  getMovieDetail,
  searchMovies,
  getCategories,
  getCategoryDetail,
  getCountries,
  getCountryDetail,
  getYearDetail
};
