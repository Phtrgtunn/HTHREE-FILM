// src/config/api.js
// Cấu hình API endpoints tập trung

// Tự động detect base URL dựa trên environment
const getBackendUrl = () => {
  // Trong production (build), dùng Railway URL
  if (import.meta.env.PROD) {
    return import.meta.env.VITE_BACKEND_URL || 'https://hthree-film-production.up.railway.app/api';
  }
  
  // Trong development, dùng localhost
  return import.meta.env.VITE_BACKEND_URL || import.meta.env.VITE_API_BASE_URL || 'http://localhost/HTHREE_film/backend/api';
};

export const API_CONFIG = {
  BACKEND_URL: getBackendUrl(),
  MOVIE_API_URL: 'https://phimapi.com/v1/api',
  USE_PHP_BACKEND: false, // Set true nếu muốn dùng PHP backend cho movies
};

export default API_CONFIG;
