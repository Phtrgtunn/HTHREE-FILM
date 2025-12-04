// src/config/api.js
// Cấu hình API endpoints tập trung

// Tự động detect base URL dựa trên environment
const getBackendUrl = () => {
  // Trong production (build), dùng URL production
  if (import.meta.env.PROD) {
    return import.meta.env.VITE_BACKEND_URL || 'https://your-production-domain.com/backend/api';
  }
  
  // Trong development, dùng localhost
  return import.meta.env.VITE_BACKEND_URL || 'http://localhost/HTHREE_film/backend/api';
};

export const API_CONFIG = {
  BACKEND_URL: getBackendUrl(),
  MOVIE_API_URL: 'https://phimapi.com/v1/api',
  USE_PHP_BACKEND: false, // Set true nếu muốn dùng PHP backend cho movies
};

export default API_CONFIG;
