/**
 * Image Helper - Xử lý CORS và lỗi load ảnh từ phimapi.com
 */

/**
 * Lấy URL ảnh an toàn, tự động fallback nếu lỗi
 * @param {string} imageUrl - URL ảnh gốc
 * @param {string} type - Loại ảnh: 'poster' hoặc 'thumb'
 * @returns {string} URL ảnh đã xử lý
 */
export function getSafeImageUrl(imageUrl, type = 'poster') {
  if (!imageUrl) {
    return getPlaceholderImage(type);
  }

  // Nếu đã là URL đầy đủ
  if (imageUrl.startsWith('http')) {
    // Thay thế img.phimapi.com bằng phimimg.com (ổn định hơn)
    return imageUrl.replace('img.phimapi.com', 'phimimg.com');
  }

  // Nếu là đường dẫn tương đối
  return `https://phimimg.com/${imageUrl}`;
}

/**
 * Lấy ảnh placeholder khi không có ảnh
 * @param {string} type - Loại ảnh
 * @returns {string} URL placeholder
 */
export function getPlaceholderImage(type = 'poster') {
  const placeholders = {
    poster: 'https://placehold.co/300x450/1f2937/fbbf24?text=No+Poster',
    thumb: 'https://placehold.co/1280x720/1f2937/fbbf24?text=No+Image',
    avatar: 'https://placehold.co/100x100/1f2937/fbbf24?text=User'
  };

  return placeholders[type] || placeholders.poster;
}

/**
 * Xử lý lỗi khi load ảnh
 * @param {Event} event - Event object
 * @param {string} type - Loại ảnh
 */
export function handleImageError(event, type = 'poster') {
  const img = event.target;
  
  // Tránh loop vô hạn
  if (img.src.includes('placehold.co')) {
    return;
  }

  // Thử URL backup trước
  if (img.dataset.backup && !img.dataset.triedBackup) {
    img.dataset.triedBackup = 'true';
    img.src = img.dataset.backup;
    return;
  }

  // Cuối cùng dùng placeholder
  img.src = getPlaceholderImage(type);
}

/**
 * Lấy URL ảnh từ movie object
 * @param {Object} movie - Movie object
 * @param {string} preferType - Ưu tiên loại ảnh: 'poster' hoặc 'thumb'
 * @returns {string} URL ảnh
 */
export function getMovieImage(movie, preferType = 'poster') {
  if (!movie) {
    return getPlaceholderImage(preferType);
  }

  let imageUrl = '';
  
  if (preferType === 'poster') {
    imageUrl = movie.poster_url || movie.thumb_url || movie.poster_path || movie.backdrop_path;
  } else {
    imageUrl = movie.thumb_url || movie.backdrop_path || movie.poster_url || movie.poster_path;
  }

  return getSafeImageUrl(imageUrl, preferType);
}

/**
 * Preload ảnh để tránh flickering
 * @param {string} imageUrl - URL ảnh
 * @returns {Promise} Promise resolve khi ảnh load xong
 */
export function preloadImage(imageUrl) {
  return new Promise((resolve, reject) => {
    const img = new Image();
    img.onload = () => resolve(imageUrl);
    img.onerror = () => reject(new Error(`Failed to load image: ${imageUrl}`));
    img.src = imageUrl;
  });
}

export default {
  getSafeImageUrl,
  getPlaceholderImage,
  handleImageError,
  getMovieImage,
  preloadImage
};
