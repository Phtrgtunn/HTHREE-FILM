<template>
  <div class="min-h-screen bg-base-100">
    <!-- Hero Banner -->
    <HeroBanner :featuredMovie="sampleMovies[0]" />
    
    <div class="container mx-auto px-4 py-8">
      <!-- Theme Switcher -->
      <div class="flex justify-end mb-6">
        <label class="swap swap-rotate btn btn-circle btn-ghost">
          <input type="checkbox" @change="toggleTheme" />
          <svg class="swap-on fill-current w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z"/>
          </svg>
          <svg class="swap-off fill-current w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"/>
          </svg>
        </label>
      </div>

      <!-- Stats -->
      <div class="stats stats-vertical lg:stats-horizontal shadow mb-8 w-full">
        <div class="stat">
          <div class="stat-figure text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
            </svg>
          </div>
          <div class="stat-title">Tổng phim</div>
          <div class="stat-value text-primary">25.6K</div>
          <div class="stat-desc">Cập nhật hàng ngày</div>
        </div>
        
        <div class="stat">
          <div class="stat-figure text-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
          </div>
          <div class="stat-title">Lượt xem</div>
          <div class="stat-value text-secondary">2.6M</div>
          <div class="stat-desc">↗︎ 400 (22%)</div>
        </div>
        
        <div class="stat">
          <div class="stat-figure text-accent">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
            </svg>
          </div>
          <div class="stat-title">Thành viên</div>
          <div class="stat-value text-accent">1,200</div>
          <div class="stat-desc">↘︎ 90 (14%)</div>
        </div>
      </div>

      <!-- Movie Carousel -->
      <MovieCarousel 
        title="🔥 Phim Hot Nhất" 
        :movies="sampleMovies"
        viewAllLink="/category/hot/page/1"
      />
      
      <MovieCarousel 
        title="🎬 Phim Bộ Mới" 
        :movies="sampleMovies"
        viewAllLink="/category/phim-bo/page/1"
      />

      <!-- Movie Grid -->
      <div class="mb-8">
        <h2 class="text-2xl font-bold mb-4">📺 Phim Lẻ</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
          <router-link 
            v-for="movie in sampleMovies.slice(0, 10)" 
            :key="movie.slug"
            :to="`/film/${movie.slug}`"
          >
            <MovieCard :movie="movie" />
          </router-link>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex justify-center">
        <div class="join">
          <button class="join-item btn">«</button>
          <button class="join-item btn btn-active">1</button>
          <button class="join-item btn">2</button>
          <button class="join-item btn">3</button>
          <button class="join-item btn">4</button>
          <button class="join-item btn">»</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import HeroBanner from '@/components/HeroBanner.vue';
import MovieCarousel from '@/components/MovieCarousel.vue';
import MovieCard from '@/components/MovieCard.vue';

// Sample data
const sampleMovies = ref([
  {
    slug: 'avatar-2',
    name: 'Avatar: The Way of Water',
    origin_name: 'Avatar 2',
    thumb_url: 'https://image.tmdb.org/t/p/w500/t6HIqrRAclMCA60NsSmeqe9RmNV.jpg',
    poster_url: 'https://image.tmdb.org/t/p/original/t6HIqrRAclMCA60NsSmeqe9RmNV.jpg',
    year: 2024,
    quality: 'HD',
    rating: 8.5,
    genres: ['Hành động', 'Phiêu lưu', 'Khoa học viễn tưởng'],
    description: 'Jake Sully sống cùng gia đình mới của mình trên hành tinh Pandora. Khi một mối đe dọa quen thuộc trở lại để hoàn thành những gì đã bắt đầu trước đây, Jake phải làm việc với Neytiri và quân đội của chủng tộc Na vi để bảo vệ hành tinh của họ.'
  },
  {
    slug: 'one-piece',
    name: 'One Piece',
    origin_name: 'ワンピース',
    thumb_url: 'https://image.tmdb.org/t/p/w500/cMD9Ygz11zjJzAovURpO75Qg7rT.jpg',
    year: 2024,
    quality: 'HD',
    rating: 9.2,
    genres: ['Anime', 'Phiêu lưu', 'Hài'],
    description: 'Monkey D. Luffy và băng hải tặc Mũ Rơm tiếp tục cuộc hành trình tìm kiếm kho báu One Piece huyền thoại.'
  },
  {
    slug: 'squid-game',
    name: 'Squid Game',
    origin_name: '오징어 게임',
    thumb_url: 'https://image.tmdb.org/t/p/w500/dDlEmu3EZ0Pgg93K2SVNLCjCSvE.jpg',
    year: 2024,
    quality: 'HD',
    rating: 8.8,
    genres: ['Hành động', 'Kinh dị', 'Drama'],
    description: 'Hàng trăm người chơi thiếu tiền chấp nhận một lời mời kỳ lạ để tham gia vào các trò chơi trẻ con. Đang chờ đợi họ là một giải thưởng hấp dẫn - và những rủi ro chết người.'
  },
  {
    slug: 'the-last-of-us',
    name: 'The Last of Us',
    origin_name: 'The Last of Us',
    thumb_url: 'https://image.tmdb.org/t/p/w500/uKvVjHNqB5VmOrdxqAt2F7J78ED.jpg',
    year: 2024,
    quality: 'HD',
    rating: 9.0,
    genres: ['Hành động', 'Phiêu lưu', 'Drama'],
    description: 'Hai mươi năm sau khi nền văn minh hiện đại bị phá hủy, Joel, một người sống sót cứng rắn, được thuê để đưa Ellie, một cô gái 14 tuổi, ra khỏi vùng cách ly đàn áp.'
  },
  {
    slug: 'wednesday',
    name: 'Wednesday',
    origin_name: 'Wednesday',
    thumb_url: 'https://image.tmdb.org/t/p/w500/9PFonBhy4cQy7Jz20NpMygczOkv.jpg',
    year: 2024,
    quality: 'HD',
    rating: 8.7,
    genres: ['Hài', 'Bí ẩn', 'Kinh dị'],
    description: 'Wednesday Addams đang cố gắng làm chủ khả năng ngoại cảm của mình, ngăn chặn một cơn thịnh nộ giết người đang khủng bố thị trấn địa phương và giải quyết bí ẩn siêu nhiên mới mà cha mẹ cô đã gặp phải 25 năm trước.'
  }
]);

const toggleTheme = (e) => {
  const html = document.documentElement;
  if (e.target.checked) {
    html.setAttribute('data-theme', 'light');
  } else {
    html.setAttribute('data-theme', 'dark');
  }
};
</script>
