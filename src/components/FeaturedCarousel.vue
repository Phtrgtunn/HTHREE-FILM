-<template>
  <div class="featured-section">
    <div class="section-header">
      <h2 class="section-title">🎬 Phim Đề Cử</h2>
      <p class="section-subtitle">Những bộ phim được đề xuất dành riêng cho bạn</p>
    </div>

    <div class="carousel-container">
      <div 
        class="cards-wrapper" 
        ref="cardsWrapper"
        @mousedown="handleMouseDown"
        @touchstart="handleTouchStart"
      >
        <div 
          v-for="(movie, index) in movies" 
          :key="index"
          class="movie-card"
          :style="getCardStyle(index)"
        >
          <div class="card-inner">
            <img 
              :src="movie.poster" 
              :alt="movie.title"
              class="card-poster"
              @error="handleImageError"
            />
            <div class="card-overlay">
              <div class="card-content">
                <h3 class="card-title">{{ movie.title }}</h3>
                <div class="card-meta">
                  <span class="card-year">{{ movie.year }}</span>
                  <span class="card-rating">⭐ {{ movie.rating }}</span>
                </div>
                <button class="card-button">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/>
                  </svg>
                  Xem Ngay
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Controls -->
      <div class="carousel-controls">
        <button @click="prev" class="control-btn prev-btn">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <button @click="next" class="control-btn next-btn">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
      </div>

      <!-- Indicators -->
      <div class="carousel-indicators">
        <button 
          v-for="(movie, index) in movies" 
          :key="index"
          @click="goToSlide(index)"
          :class="['indicator', { active: currentIndex === index }]"
        ></button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const movies = ref([]);
const currentIndex = ref(0); // Sẽ set về giữa sau khi load
const cardsWrapper = ref(null);
const isDragging = ref(false);
const startX = ref(0);
const currentX = ref(0);
const dragOffset = ref(0);

const getCardStyle = (index) => {
  const diff = index - currentIndex.value;
  const absD = Math.abs(diff);
  
  // Hiển thị nhiều cards hơn (5 mỗi bên)
  if (absD > 5) return { display: 'none' };
  
  const zIndex = 30 - absD;
  
  // Card ở giữa to hơn một chút (giảm từ 1.2 xuống 1.05)
  const scale = absD === 0 ? 1.05 : 1 - absD * 0.12;
  const opacity = 1 - absD * 0.2;
  
  // Giảm khoảng cách giữa các cards (từ 120 xuống 70)
  const translateX = diff * 70 + dragOffset.value;
  const translateY = absD * 15;
  const rotateY = diff * 6;
  
  return {
    transform: `translateX(${translateX}%) translateY(${translateY}px) scale(${scale}) rotateY(${rotateY}deg)`,
    zIndex,
    opacity,
    // Tăng thời gian lên 0.7s và dùng easing mượt hơn
    transition: isDragging.value ? 'none' : 'all 0.7s cubic-bezier(0.34, 1.56, 0.64, 1)'
  };
};

const next = () => {
  if (currentIndex.value < movies.value.length - 1) {
    currentIndex.value++;
  } else {
    currentIndex.value = 0;
  }
  goToSlide(currentIndex.value);
};

const prev = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--;
  } else {
    currentIndex.value = movies.value.length - 1;
  }
  goToSlide(currentIndex.value);
};

const goToSlide = (index) => {
  currentIndex.value = index;
  
  // Auto scroll indicators để hiển thị active indicator
  setTimeout(() => {
    const indicatorsContainer = document.querySelector('.carousel-indicators');
    const activeIndicator = document.querySelector('.indicator.active');
    if (indicatorsContainer && activeIndicator) {
      activeIndicator.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
  }, 100);
};

const handleImageError = (e) => {
  e.target.src = 'https://placehold.co/300x450/1a1a1a/fff?text=No+Image';
};

// Drag functionality
const handleMouseDown = (e) => {
  isDragging.value = true;
  startX.value = e.clientX;
  currentX.value = e.clientX;
};

const handleMouseMove = (e) => {
  if (!isDragging.value) return;
  e.preventDefault();
  currentX.value = e.clientX;
  const diff = currentX.value - startX.value;
  // Giới hạn dragOffset để không kéo quá xa
  const maxOffset = 100;
  const calculatedOffset = (diff / window.innerWidth) * 300;
  dragOffset.value = Math.max(-maxOffset, Math.min(maxOffset, calculatedOffset));
};

const handleMouseUp = () => {
  if (!isDragging.value) return;
  
  const diff = currentX.value - startX.value;
  const threshold = 30;
  
  if (diff > threshold) {
    prev();
  } else if (diff < -threshold) {
    next();
  }
  
  isDragging.value = false;
  dragOffset.value = 0;
};

// Touch support
const handleTouchStart = (e) => {
  isDragging.value = true;
  startX.value = e.touches[0].clientX;
  currentX.value = e.touches[0].clientX;
};

const handleTouchMove = (e) => {
  if (!isDragging.value) return;
  e.preventDefault();
  currentX.value = e.touches[0].clientX;
  const diff = currentX.value - startX.value;
  // Giới hạn dragOffset để không kéo quá xa
  const maxOffset = 100;
  const calculatedOffset = (diff / window.innerWidth) * 300;
  dragOffset.value = Math.max(-maxOffset, Math.min(maxOffset, calculatedOffset));
};

const handleTouchEnd = () => {
  handleMouseUp();
};

const fetchFeaturedMovies = async () => {
  try {
    const response = await axios.get('https://phimapi.com/danh-sach/phim-moi-cap-nhat', {
      params: { page: 1 }
    });
    
    if (response.data?.items?.length) {
      // Tăng số lượng phim lên 20
      movies.value = response.data.items.slice(0, 20).map(movie => ({
        title: movie.name,
        year: movie.year || 2024,
        rating: (Math.random() * 2 + 7).toFixed(1),
        poster: movie.poster_url?.startsWith('http') 
          ? movie.poster_url 
          : `https://phimimg.com/${movie.poster_url}`
      }));
      
      // Set currentIndex về giữa để bắt đầu từ giữa
      currentIndex.value = Math.floor(movies.value.length / 2);
    }
  } catch (error) {
    console.error('Failed to fetch movies:', error);
  }
};

let autoPlayInterval = null;

onMounted(() => {
  fetchFeaturedMovies();
  
  // Add drag listeners
  document.addEventListener('mousemove', handleMouseMove);
  document.addEventListener('mouseup', handleMouseUp);
  
  const wrapper = cardsWrapper.value;
  if (wrapper) {
    wrapper.addEventListener('touchmove', handleTouchMove);
    wrapper.addEventListener('touchend', handleTouchEnd);
  }
  
  // Tắt auto play (comment out)
  // autoPlayInterval = setInterval(() => {
  //   if (!isDragging.value) {
  //     next();
  //   }
  // }, 5000);
});

onUnmounted(() => {
  document.removeEventListener('mousemove', handleMouseMove);
  document.removeEventListener('mouseup', handleMouseUp);
  
  const wrapper = cardsWrapper.value;
  if (wrapper) {
    wrapper.removeEventListener('touchmove', handleTouchMove);
    wrapper.removeEventListener('touchend', handleTouchEnd);
  }
  
  if (autoPlayInterval) {
    clearInterval(autoPlayInterval);
  }
});
</script>

<style scoped>
.featured-section {
  padding: 4rem 2rem;
  background: linear-gradient(180deg, #000 0%, #111 100%);
  min-height: 600px;
  overflow: hidden; /* Ẩn phần tràn ra ngoài */
}

.section-header {
  text-align: center;
  margin-bottom: 4rem;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: #fff;
  margin-bottom: 0.5rem;
  text-transform: uppercase;
  letter-spacing: 2px;
}

.section-subtitle {
  font-size: 1.1rem;
  color: #888;
}

.carousel-container {
  position: relative;
  height: 500px;
  perspective: 1500px;
  overflow: hidden; /* Ẩn cards khi kéo ra ngoài */
}

.cards-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: grab;
  user-select: none;
  -webkit-user-drag: none;
  touch-action: pan-y; /* Cho phép scroll dọc nhưng chặn ngang */
}

.cards-wrapper:active {
  cursor: grabbing;
}

.movie-card {
  position: absolute;
  width: 260px;
  height: 390px;
  cursor: pointer;
  transform-style: preserve-3d;
}

.card-inner {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
  transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.movie-card:hover .card-inner {
  transform: translateY(-5px);
}

.card-poster {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.card-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.7) 60%, rgba(0,0,0,0.95) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
  display: flex;
  align-items: flex-end;
  padding: 1.5rem;
}

.movie-card:hover .card-overlay {
  opacity: 1;
}

.card-content {
  width: 100%;
}

.card-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #fff;
  margin-bottom: 0.5rem;
  line-height: 1.3;
}

.card-meta {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  font-size: 0.9rem;
}

.card-year {
  color: #aaa;
}

.card-rating {
  color: #f59e0b;
  font-weight: 600;
}

.card-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.2rem;
  background: #f59e0b;
  color: #000;
  border: none;
  border-radius: 0.5rem;
  font-weight: 700;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.card-button:hover {
  background: #fbbf24;
  transform: scale(1.05);
}

.carousel-controls {
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  transform: translateY(-50%);
  display: flex;
  justify-content: space-between;
  padding: 0 2rem;
  pointer-events: none;
  z-index: 20;
}

.control-btn {
  pointer-events: all;
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.7);
  border: 2px solid rgba(255, 255, 255, 0.2);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.control-btn:hover {
  background: #f59e0b;
  border-color: #f59e0b;
  transform: scale(1.1);
}

.carousel-indicators {
  position: absolute;
  bottom: -3rem;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 0.3rem;
  z-index: 20;
  max-width: 80%;
  padding: 0.5rem 0;
}

.indicator {
  flex-shrink: 0;
  width: 1.5rem;
  height: 0.25rem;
  background: rgba(255, 255, 255, 0.3);
  border: none;
  border-radius: 0.2rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.indicator.active {
  background: #f59e0b;
  width: 2rem;
  height: 0.3rem;
}

.indicator:hover {
  background: rgba(255, 255, 255, 0.5);
}

@media (max-width: 768px) {
  .movie-card {
    width: 250px;
    height: 375px;
  }
  
  .section-title {
    font-size: 2rem;
  }
  
  .carousel-controls {
    padding: 0 1rem;
  }
}
</style>
