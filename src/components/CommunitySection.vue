<template>
  <div class="community-section bg-black py-12 px-4 md:px-12">
    <div class="max-w-[1920px] mx-auto">
      <!-- Comment Form -->
      <div class="mb-10">
        <CommentForm @comment-added="loadCommunityData" />
      </div>
      
      <!-- Top Bình Luận - Horizontal Carousel -->
      <div class="mb-8">
        <div class="flex items-center gap-2 mb-4">
          <div class="w-8 h-8 bg-yellow-400/10 rounded-lg flex items-center justify-center">
            <span class="text-lg">🏆</span>
          </div>
          <h3 class="text-base font-bold text-white uppercase tracking-wide">TOP BÌNH LUẬN</h3>
        </div>
        
        <div class="relative group/scroll">
          <button @click="scrollTopLeft" class="absolute left-1 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-gray-900/90 hover:bg-yellow-400 rounded-full flex items-center justify-center transition-all opacity-0 group-hover/scroll:opacity-100">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          </button>
          <button @click="scrollTopRight" class="absolute right-1 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-gray-900/90 hover:bg-yellow-400 rounded-full flex items-center justify-center transition-all opacity-0 group-hover/scroll:opacity-100">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </button>
          
          <div ref="topScrollRef" class="flex gap-4 overflow-x-auto scrollbar-hide py-4 px-2 scroll-smooth">
            <div 
              v-for="(comment, index) in topComments" 
              :key="comment.id" 
              class="flex-none w-[300px] rounded-xl p-4 border border-gray-700/30 hover:border-yellow-400/50 transition-all duration-300 cursor-pointer hover:scale-[1.01] hover:shadow-lg hover:shadow-yellow-400/20 relative overflow-hidden group"
            >
              <!-- Background Poster với blur -->
              <div 
                class="absolute inset-0 bg-cover bg-center transition-all duration-300"
                :style="{ backgroundImage: `url(${comment.moviePoster})` }"
              ></div>
              
              <!-- Overlay mờ đen -->
              <div class="absolute inset-0 bg-black/70 backdrop-blur-sm group-hover:bg-black/60 transition-all duration-300"></div>
              
              <!-- Content -->
              <div class="relative z-10">
                <!-- Avatar & Poster -->
                <div class="flex gap-3 mb-3 justify-between">
                  <div class="relative flex-shrink-0">
                    <img :src="comment.avatar" :alt="comment.name" class="w-14 h-14 rounded-full border-3 border-gray-700/50 group-hover:border-yellow-400 transition-all shadow-lg" />
                    <div v-if="index === 0" class="absolute -bottom-0.5 left-1/2 -translate-x-1/2 bg-yellow-400 text-black text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-lg">∞</div>
                  </div>
                  <div class="flex-shrink-0">
                    <img 
                      :src="comment.moviePoster" 
                      :alt="comment.movieTitle" 
                      class="w-16 h-24 rounded-md object-cover border border-gray-700/50 group-hover:border-yellow-400/50 transition-all shadow-lg" 
                      @error="(e) => e.target.src = 'https://placehold.co/64x96/1a1a1a/fff?text=No+Image'"
                    />
                  </div>
                </div>
                
                <!-- User Info -->
                <div class="mb-2">
                  <div class="flex items-center gap-1.5">
                    <h4 class="text-white font-bold text-sm drop-shadow-lg">{{ comment.name }}</h4>
                    <span v-if="index === 0" class="text-yellow-400 text-xs">∞</span>
                  </div>
                </div>
                
                <!-- Comment Text -->
                <p class="text-gray-200 text-xs leading-relaxed mb-3 line-clamp-2 drop-shadow-md">{{ comment.text }}</p>
                
                <!-- Stats -->
                <div class="flex items-center gap-4 text-gray-300">
                  <button class="flex items-center gap-1.5 hover:text-green-400 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/></svg>
                    <span class="text-xs font-medium">{{ comment.likes }}</span>
                  </button>
                  <button class="flex items-center gap-1.5 hover:text-red-400 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v6zM14 9.667v-5.43a2 2 0 00-1.105-1.79l-.05-.025A4 4 0 0011.055 2H5.64a2 2 0 00-1.962 1.608l-1.2 6A2 2 0 004.44 12H8v4a2 2 0 002 2 1 1 0 001-1v-.667a4 4 0 01.8-2.4l1.4-1.866a4 4 0 00.8-2.4z"/></svg>
                    <span class="text-xs font-medium">{{ comment.dislikes }}</span>
                  </button>
                  <button class="flex items-center gap-1.5 hover:text-blue-400 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/></svg>
                    <span class="text-xs font-medium">{{ comment.replies }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Grid 3 Columns -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Sôi Nổi Nhất -->
        <div class="bg-gray-900/30 rounded-lg p-4 border border-gray-800">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-base">🔥</span>
            <h3 class="text-sm font-bold text-white uppercase">SÔI NỔI NHẤT</h3>
          </div>
          <div class="space-y-2 mb-3">
            <div v-if="trendingMovies.length === 0" class="text-center py-4 text-gray-500 text-xs">
              Đang tải phim...
            </div>
            <div v-for="(movie, i) in trendingMovies" :key="i" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-800/50 transition cursor-pointer">
              <span class="text-yellow-400 font-bold text-xs w-5">{{ i + 1 }}</span>
              <img :src="movie.poster" class="w-8 h-11 rounded object-cover" @error="(e) => e.target.src = 'https://placehold.co/32x44/1a1a1a/fff?text=?'" />
              <div class="flex-1 min-w-0">
                <p class="text-white text-xs font-medium truncate">{{ movie.title }}</p>
                <p class="text-gray-400 text-[10px]">{{ movie.year }}</p>
              </div>
            </div>
          </div>
          <button @click="showTrendingModal = true" class="w-full py-2 text-xs text-yellow-400 hover:text-yellow-300 font-medium transition-colors border-t border-gray-800 pt-3">
            Xem thêm →
          </button>
        </div>

        <!-- Yêu Thích Nhất -->
        <div class="bg-gray-900/30 rounded-lg p-4 border border-gray-800">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-base">💛</span>
            <h3 class="text-sm font-bold text-white uppercase">YÊU THÍCH NHẤT</h3>
          </div>
          <div class="space-y-2 mb-3">
            <div v-if="favoriteMovies.length === 0" class="text-center py-4 text-gray-500 text-xs">
              Đang tải phim...
            </div>
            <div v-for="(movie, i) in favoriteMovies" :key="i" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-800/50 transition cursor-pointer">
              <span class="text-yellow-400 font-bold text-xs w-5">{{ i + 1 }}</span>
              <img :src="movie.poster" class="w-8 h-11 rounded object-cover" @error="(e) => e.target.src = 'https://placehold.co/32x44/1a1a1a/fff?text=?'" />
              <div class="flex-1 min-w-0">
                <p class="text-white text-xs font-medium truncate">{{ movie.title }}</p>
                <p class="text-gray-400 text-[10px]">{{ movie.year }}</p>
              </div>
            </div>
          </div>
          <button @click="showFavoriteModal = true" class="w-full py-2 text-xs text-yellow-400 hover:text-yellow-300 font-medium transition-colors border-t border-gray-800 pt-3">
            Xem thêm →
          </button>
        </div>

        <!-- Bình Luận Mới -->
        <div class="bg-gray-900/30 rounded-lg p-4 border border-gray-800">
          <div class="flex items-center gap-2 mb-3">
            <span class="text-base">⚡</span>
            <h3 class="text-sm font-bold text-white uppercase">BÌNH LUẬN MỚI</h3>
          </div>
          <div class="space-y-3 max-h-[400px] overflow-hidden">
            <TransitionGroup name="comment-list">
              <div v-for="comment in recentComments.slice(0, 5)" :key="comment.id" class="p-2 rounded-lg hover:bg-gray-800/50 transition cursor-pointer">
                <div class="flex items-start gap-2 mb-1.5">
                  <img :src="comment.avatar" class="w-7 h-7 rounded-full" />
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5">
                      <p class="text-white text-xs font-medium">{{ comment.name }}</p>
                      <span v-if="comment.time === 'vừa xong'" class="text-green-400 text-[10px] animate-pulse">●</span>
                    </div>
                    <p class="text-gray-400 text-[10px]">{{ comment.time }}</p>
                  </div>
                </div>
                <p class="text-gray-300 text-[11px] line-clamp-2 leading-relaxed">{{ comment.text }}</p>
              </div>
            </TransitionGroup>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Sôi Nổi Nhất -->
    <Transition name="modal">
      <div v-if="showTrendingModal" @click="showTrendingModal = false" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.stop class="bg-gray-900 rounded-xl max-w-4xl w-full max-h-[80vh] overflow-hidden border border-gray-800 modal-content">
        <div class="flex items-center justify-between p-4 border-b border-gray-800">
          <div class="flex items-center gap-2">
            <span class="text-2xl">🔥</span>
            <h2 class="text-xl font-bold text-white">SÔI NỘI NHẤT</h2>
          </div>
          <button @click="showTrendingModal = false" class="text-gray-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="p-4 overflow-y-auto max-h-[calc(80vh-80px)] scrollbar-hide">
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div v-for="(movie, i) in trendingMoviesAll" :key="i" class="group cursor-pointer">
              <div class="relative aspect-[2/3] rounded-lg overflow-hidden mb-2">
                <img :src="movie.poster" :alt="movie.title" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" @error="(e) => e.target.src = 'https://placehold.co/200x300/1a1a1a/fff?text=No+Image'" />
                <div class="absolute top-2 left-2 bg-yellow-400 text-black text-xs font-bold px-2 py-1 rounded-full">{{ i + 1 }}</div>
              </div>
              <p class="text-white text-sm font-medium line-clamp-2">{{ movie.title }}</p>
              <p class="text-gray-400 text-xs">{{ movie.year }}</p>
            </div>
          </div>
        </div>
        </div>
      </div>
    </Transition>

    <!-- Modal Yêu Thích Nhất -->
    <Transition name="modal">
      <div v-if="showFavoriteModal" @click="showFavoriteModal = false" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.stop class="bg-gray-900 rounded-xl max-w-4xl w-full max-h-[80vh] overflow-hidden border border-gray-800 modal-content">
        <div class="flex items-center justify-between p-4 border-b border-gray-800">
          <div class="flex items-center gap-2">
            <span class="text-2xl">💛</span>
            <h2 class="text-xl font-bold text-white">YÊU THÍCH NHẤT</h2>
          </div>
          <button @click="showFavoriteModal = false" class="text-gray-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="p-4 overflow-y-auto max-h-[calc(80vh-80px)] scrollbar-hide">
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div v-for="(movie, i) in favoriteMoviesAll" :key="i" class="group cursor-pointer">
              <div class="relative aspect-[2/3] rounded-lg overflow-hidden mb-2">
                <img :src="movie.poster" :alt="movie.title" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" @error="(e) => e.target.src = 'https://placehold.co/200x300/1a1a1a/fff?text=No+Image'" />
                <div class="absolute top-2 left-2 bg-yellow-400 text-black text-xs font-bold px-2 py-1 rounded-full">{{ i + 1 }}</div>
              </div>
              <p class="text-white text-sm font-medium line-clamp-2">{{ movie.title }}</p>
              <p class="text-gray-400 text-xs">{{ movie.year }}</p>
            </div>
          </div>
        </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import CommentForm from './CommentForm.vue';
import { API_CONFIG } from '@/config/api';

const API_URL = API_CONFIG.BACKEND_URL;
const topScrollRef = ref(null);

const topComments = ref([]);

const trendingMovies = ref([]);
const trendingMoviesAll = ref([]);
const favoriteMovies = ref([]);
const favoriteMoviesAll = ref([]);
const showTrendingModal = ref(false);
const showFavoriteModal = ref(false);

const recentComments = ref([
  { id: 1, name: 'Hoang Gang', avatar: 'https://ui-avatars.com/api/?name=Hoang&background=f59e0b&color=000', time: 'vừa xong', text: 'luôn là bộ phim tuyệt vời nhất mà tôi từng xem' },
  { id: 2, name: 'Margaret Qualley', avatar: 'https://ui-avatars.com/api/?name=Margaret&background=ef4444&color=fff', time: '2 phút trước', text: 'Tuyệt vời! Diễn xuất quá đỉnh' },
  { id: 3, name: 'Tsukii', avatar: 'https://ui-avatars.com/api/?name=Tsukii&background=3b82f6&color=fff', time: '5 phút trước', text: 'Cảm ơn đã chia sẻ!' }
]);

// Danh sách bình luận mẫu để auto-load (50+ mẫu)
const sampleComments = [
  { name: 'Minh Anh', text: 'Phim hay quá! Tôi đã xem 3 lần rồi 😍' },
  { name: 'Tuấn Kiệt', text: 'CGI đỉnh cao, xứng đáng 5 sao ⭐⭐⭐⭐⭐' },
  { name: 'Linh Chi', text: 'Cảm động quá, tôi đã khóc khi xem 😢' },
  { name: 'Hoàng Long', text: 'Diễn viên diễn xuất rất tự nhiên 👏' },
  { name: 'Phương Anh', text: 'Cốt truyện hấp dẫn từ đầu đến cuối!' },
  { name: 'Đức Anh', text: 'Nhạc phim hay lắm, nghe mãi không chán 🎵' },
  { name: 'Mai Ly', text: 'Phim này đáng xem, recommend mọi người!' },
  { name: 'Quang Huy', text: 'Đạo diễn tài năng thật! Mỗi cảnh đều ý nghĩa' },
  { name: 'Thảo Nguyên', text: 'Tuyệt vời! Không thể bỏ lỡ bộ phim này' },
  { name: 'Văn Toàn', text: 'Phim hay nhưng hơi dài, nên chia làm 2 phần xem' },
  { name: 'Bảo Trâm', text: 'Cảnh quay đẹp mắt, màu sắc rực rỡ 🌈' },
  { name: 'Hải Nam', text: 'Nội dung sâu sắc, đáng suy ngẫm 🤔' },
  { name: 'Khánh Linh', text: 'Phim này làm tôi nhớ về tuổi thơ...' },
  { name: 'Duy Khánh', text: 'Xem xong muốn xem lại ngay! 🔄' },
  { name: 'Thu Hà', text: 'Diễn viên phụ cũng diễn rất hay!' },
  { name: 'Minh Tuấn', text: 'Phim Việt ngày càng chất lượng 🇻🇳' },
  { name: 'Ngọc Anh', text: 'Cảm ơn đã chia sẻ phim hay! ❤️' },
  { name: 'Trung Hiếu', text: 'Phim này đáng giá từng phút xem!' },
  { name: 'Lan Hương', text: 'Tôi đã giới thiệu cho bạn bè xem rồi 👍' },
  { name: 'Quốc Bảo', text: 'Phim hay, nhưng cần phụ đề tốt hơn' },
  { name: 'Anh Tuấn', text: 'Xem lúc 2h sáng vẫn không buồn ngủ! 😱' },
  { name: 'Hương Giang', text: 'Phim này viral trên TikTok rồi 🔥' },
  { name: 'Bảo Ngọc', text: 'Mình đã khóc hết cả hộp khăn giấy 😭' },
  { name: 'Thanh Tùng', text: 'Phim xuất sắc! Oscar phải trao giải cho phim này' },
  { name: 'Kim Ngân', text: 'Nhạc nền quá đỉnh, ai biết tên bài không?' },
  { name: 'Việt Anh', text: 'Phim này phải xem ở rạp mới đã!' },
  { name: 'Thùy Linh', text: 'Cảnh cuối làm tôi sốc nặng 😲' },
  { name: 'Đăng Khoa', text: 'Phim hay nhất năm nay luôn!' },
  { name: 'Phương Thảo', text: 'Tôi đã mua vé xem 2 lần rồi 🎬' },
  { name: 'Hữu Nghĩa', text: 'Diễn viên chính quá đẹp trai! 😍' },
  { name: 'Thanh Hà', text: 'Phim này đáng để chờ đợi!' },
  { name: 'Minh Quân', text: 'Xem xong muốn đi du lịch ngay! ✈️' },
  { name: 'Tú Anh', text: 'Phim này làm tôi suy nghĩ nhiều về cuộc sống' },
  { name: 'Hoài Nam', text: 'Cảnh hành động mãn nhãn quá! 💥' },
  { name: 'Bích Ngọc', text: 'Tôi đã recommend cho cả công ty xem rồi' },
  { name: 'Quang Minh', text: 'Phim này xứng đáng trending #1! 📈' },
  { name: 'Hạnh Nhân', text: 'Xem đi xem lại vẫn thấy hay!' },
  { name: 'Tuấn Vũ', text: 'Phim này phải vào top 10 năm!' },
  { name: 'Ngọc Trinh', text: 'Cảm xúc lên xuống như tàu lượn! 🎢' },
  { name: 'Đức Thịnh', text: 'Phim này làm tôi cười và khóc!' },
  { name: 'Mai Phương', text: 'Đạo diễn thiên tài! Respect! 🙏' },
  { name: 'Hồng Nhung', text: 'Phim này sẽ là huyền thoại!' },
  { name: 'Trọng Đại', text: 'Xem xong tôi đã trở thành fan cứng!' },
  { name: 'Yến Nhi', text: 'Phim này phải có phần 2 thôi! 🙏' },
  { name: 'Công Phượng', text: 'Tôi đã xem trailer 10 lần rồi!' },
  { name: 'Thảo My', text: 'Phim này đáng để đợi cả năm!' },
  { name: 'Xuân Trường', text: 'Cảnh chiến đấu quá hoành tráng! ⚔️' },
  { name: 'Diệu Hương', text: 'Tôi sẽ mua đĩa Bluray về sưu tầm!' },
  { name: 'Minh Hằng', text: 'Phim này làm tôi tin vào tình yêu! 💕' },
  { name: 'Tuấn Hưng', text: 'Âm thanh Dolby Atmos quá đỉnh! 🔊' }
];

const colors = ['f59e0b', 'ef4444', '3b82f6', '10b981', 'ec4899', '8b5cf6', '14b8a6', 'f97316'];
let commentIdCounter = 4;
let autoLoadInterval = null;

const scrollTopLeft = () => topScrollRef.value?.scrollBy({ left: -280, behavior: 'smooth' });
const scrollTopRight = () => topScrollRef.value?.scrollBy({ left: 280, behavior: 'smooth' });

// Lock/unlock body scroll when modal opens/closes
watch([showTrendingModal, showFavoriteModal], ([trending, favorite]) => {
  if (trending || favorite) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
});

const fetchMoviesFromAPI = async () => {
  try {
    // Fetch nhiều phim hơn từ 3 pages để có đủ cho tất cả sections
    const [page1, page2, page3] = await Promise.all([
      axios.get('https://phimapi.com/danh-sach/phim-moi-cap-nhat', { params: { page: 1 } }),
      axios.get('https://phimapi.com/danh-sach/phim-moi-cap-nhat', { params: { page: 2 } }),
      axios.get('https://phimapi.com/danh-sach/phim-moi-cap-nhat', { params: { page: 3 } })
    ]);
    
    const allMovies = [...(page1.data?.items || []), ...(page2.data?.items || []), ...(page3.data?.items || [])];
    console.log('🎬 API Response - Total movies:', allMovies.length);
    
    if (allMovies.length > 0) {
      const movies = allMovies;
      
      // Update Top Comments với 8 phim đầu
      const sampleComments = [
        { name: 'N VL', avatar: 'https://ui-avatars.com/api/?name=NVL&background=f59e0b&color=000', text: 'Tập một bức mình nam chính thật sự, trả lời câu hỏi mà cứ ngơ ngơ...', likes: 1, dislikes: 0, replies: 4 },
        { name: 'Khuong Nguyen', avatar: 'https://ui-avatars.com/api/?name=Khuong&background=3b82f6&color=fff', text: 'Cái bé này thật sự là hay quá, mình đã xem 3 lần rồi mà vẫn thấy hay!', likes: 5, dislikes: 0, replies: 2 },
        { name: 'Doss Nguyen', avatar: 'https://ui-avatars.com/api/?name=Doss&background=ef4444&color=fff', text: 'Phim hay lắm, diễn xuất tuyệt vời, cảnh quay đẹp mắt!', likes: 8, dislikes: 1, replies: 6 },
        { name: 'toni_nguyen', avatar: 'https://ui-avatars.com/api/?name=Toni&background=10b981&color=fff', text: 'Anime đỉnh cao! Miku chan kawaii quá đi mất!', likes: 12, dislikes: 0, replies: 8 },
        { name: 'Mai Anh', avatar: 'https://ui-avatars.com/api/?name=Mai&background=ec4899&color=fff', text: 'Cảm động quá! Tôi đã khóc khi xem phần cuối...', likes: 15, dislikes: 0, replies: 10 },
        { name: 'Hoang Tuan', avatar: 'https://ui-avatars.com/api/?name=Hoang&background=8b5cf6&color=fff', text: 'CGI đỉnh cao, xứng đáng 5 sao! Recommend mọi người xem', likes: 20, dislikes: 2, replies: 12 },
        { name: 'Linh Chi', avatar: 'https://ui-avatars.com/api/?name=Linh&background=14b8a6&color=fff', text: 'Phim hay nhưng hơi dài, nên chia làm 2 phần xem cho đỡ mệt', likes: 7, dislikes: 1, replies: 5 },
        { name: 'Minh Khoa', avatar: 'https://ui-avatars.com/api/?name=Minh&background=f97316&color=fff', text: 'Đạo diễn tài năng thật! Mỗi cảnh quay đều ý nghĩa', likes: 18, dislikes: 0, replies: 9 }
      ];
      
      topComments.value = movies.slice(0, 8).map((movie, index) => {
        const posterUrl = movie.poster_url || movie.thumb_url || '';
        const fullPosterUrl = posterUrl.startsWith('http') ? posterUrl : `https://phimimg.com/${posterUrl}`;
        
        return {
          id: index + 1,
          ...sampleComments[index],
          movieTitle: movie.name,
          moviePoster: fullPosterUrl
        };
      });
      
      // Lấy phim cho Trending (8-17 = 10 phim, hiển thị 5)
      const trendingStart = 8;
      const trendingCount = 10;
      trendingMoviesAll.value = movies.slice(trendingStart, trendingStart + trendingCount).map(movie => {
        const posterUrl = movie.poster_url || movie.thumb_url || '';
        return {
          title: movie.name,
          year: movie.year || 2024,
          poster: posterUrl.startsWith('http') ? posterUrl : `https://phimimg.com/${posterUrl}`
        };
      });
      trendingMovies.value = trendingMoviesAll.value.slice(0, 5);
      
      // Lấy phim cho Favorites (18-27 = 10 phim, hiển thị 5)
      const favoriteStart = trendingStart + trendingCount;
      const favoriteCount = 10;
      favoriteMoviesAll.value = movies.slice(favoriteStart, favoriteStart + favoriteCount).map(movie => {
        const posterUrl = movie.poster_url || movie.thumb_url || '';
        return {
          title: movie.name,
          year: movie.year || 2024,
          poster: posterUrl.startsWith('http') ? posterUrl : `https://phimimg.com/${posterUrl}`
        };
      });
      favoriteMovies.value = favoriteMoviesAll.value.slice(0, 5);
      
      console.log('🎬 Total movies from API:', movies.length);
      console.log('🔥 Trending Movies (display):', trendingMovies.value.length, trendingMovies.value);
      console.log('🔥 Trending Movies (all):', trendingMoviesAll.value.length);
      console.log('💛 Favorite Movies (display):', favoriteMovies.value.length, favoriteMovies.value);
      console.log('💛 Favorite Movies (all):', favoriteMoviesAll.value.length);
      
      console.log('✅ Top Comments:', topComments.value);
      console.log('✅ Trending:', trendingMovies.value);
      console.log('✅ Favorites:', favoriteMovies.value);
    }
  } catch (error) {
    console.log('⚠️ Could not load movies:', error.message);
  }
};

const loadCommunityData = async () => {
  try {
    const response = await axios.get(`${API_URL}/community.php`);
    console.log('📊 Community data:', response.data);
    
    if (response.data.success) {
      const data = response.data.data;
      
      // Update recent comments
      if (data.recent_comments?.length) {
        recentComments.value = data.recent_comments.map(c => {
          // Ensure avatar is valid, fallback to UI Avatars if needed
          let avatar = c.avatar;
          if (!avatar || avatar.includes('ui-avatars.com')) {
            const name = c.full_name || c.username || 'User';
            avatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=f59e0b&color=000&size=128`;
          }
          
          return {
            id: c.id,
            name: c.full_name || c.username,
            avatar: avatar,
            time: c.time_ago,
            text: c.content
          };
        });
        console.log('✅ Updated recent comments:', recentComments.value.length);
        recentComments.value.forEach((c, i) => {
          console.log(`Comment ${i + 1}:`, {
            name: c.name,
            avatar: c.avatar,
            text: c.text.substring(0, 30)
          });
        });
      }
    }
  } catch (error) {
    console.log('⚠️ Could not load community data:', error.message);
  }
};

// Hàm thêm bình luận mới tự động
const addAutoComment = () => {
  const randomComment = sampleComments[Math.floor(Math.random() * sampleComments.length)];
  const randomColor = colors[Math.floor(Math.random() * colors.length)];
  
  const newComment = {
    id: commentIdCounter++,
    name: randomComment.name,
    avatar: `https://ui-avatars.com/api/?name=${encodeURIComponent(randomComment.name)}&background=${randomColor}&color=fff`,
    time: 'vừa xong',
    text: randomComment.text
  };
  
  // Thêm vào đầu danh sách
  recentComments.value.unshift(newComment);
  
  // Giữ tối đa 10 bình luận
  if (recentComments.value.length > 10) {
    recentComments.value.pop();
  }
  
  // Cập nhật thời gian của các bình luận cũ
  recentComments.value.forEach((comment, index) => {
    if (index === 0) {
      comment.time = 'vừa xong';
    } else if (index === 1) {
      comment.time = '1 phút trước';
    } else if (index === 2) {
      comment.time = '2 phút trước';
    } else {
      comment.time = `${index + 1} phút trước`;
    }
  });
};

// Bắt đầu auto-load khi component mount
const startAutoLoad = () => {
  // Load bình luận mới mỗi 2-4 giây (random) - nhanh hơn
  const loadComment = () => {
    addAutoComment();
    const randomDelay = Math.floor(Math.random() * 2000) + 2000; // 2-4 giây
    autoLoadInterval = setTimeout(loadComment, randomDelay);
  };
  
  // Bắt đầu ngay sau 2 giây
  autoLoadInterval = setTimeout(loadComment, 2000);
};

// Dừng auto-load khi component unmount
const stopAutoLoad = () => {
  if (autoLoadInterval) {
    clearTimeout(autoLoadInterval);
    autoLoadInterval = null;
  }
};

onMounted(() => {
  loadCommunityData();
  fetchMoviesFromAPI();
  startAutoLoad();
});

// Cleanup khi component bị destroy
import { onUnmounted } from 'vue';
onUnmounted(() => {
  stopAutoLoad();
});
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

/* Comment list animations */
.comment-list-enter-active {
  animation: comment-slide-in 0.5s ease-out;
}

.comment-list-leave-active {
  animation: comment-slide-out 0.3s ease-in;
}

.comment-list-move {
  transition: all 0.5s ease;
}

@keyframes comment-slide-in {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes comment-slide-out {
  from {
    opacity: 1;
    transform: translateX(0);
  }
  to {
    opacity: 0;
    transform: translateX(20px);
  }
}

/* Modal animations */
.modal-enter-active {
  animation: modal-fade-in 0.3s ease-out;
}

.modal-leave-active {
  animation: modal-fade-out 0.25s ease-in;
}

@keyframes modal-fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes modal-fade-out {
  from {
    opacity: 1;
  }
  to {
    opacity: 0;
  }
}

.modal-enter-active .modal-content {
  animation: modal-slide-up 0.3s ease-out;
}

.modal-leave-active .modal-content {
  animation: modal-slide-down 0.25s ease-in;
}

@keyframes modal-slide-up {
  from {
    transform: translateY(50px) scale(0.95);
    opacity: 0;
  }
  to {
    transform: translateY(0) scale(1);
    opacity: 1;
  }
}

@keyframes modal-slide-down {
  from {
    transform: translateY(0) scale(1);
    opacity: 1;
  }
  to {
    transform: translateY(30px) scale(0.98);
    opacity: 0;
  }
}
</style>
