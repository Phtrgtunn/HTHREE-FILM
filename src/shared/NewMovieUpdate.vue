<template>
  <div class="space-y-10">
    <!-- Thông báo nếu chưa đăng nhập -->
    <div v-if="!isAuthenticated" class="text-center text-red-400 p-4 bg-gray-800 rounded-lg shadow-md">
      Vui lòng <router-link to="/auth" class="text-blue-400 hover:text-blue-300">đăng nhập</router-link> để xem hoặc thêm phim.
    </div>
    <template v-else>

      <!-- Phim mới cập nhật -->
      <section class="bg-gray-900 p-6 rounded-xl shadow-lg">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-white hover:text-[#dd6119] transition-colors">Phim Mới Cập Nhật</h2>
          <router-link to="/movies/updated" class="text-blue-400 hover:text-blue-300 text-sm font-medium">Xem thêm →</router-link>
        </div>
        <div class="flex gap-6">
          <div class="flex-[4]">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
              <template v-if="isLoading">
                <div v-for="i in 8" :key="i" class="animate-pulse">
                  <div class="bg-gray-700 h-64 rounded-lg"></div>
                </div>
              </template>
              <!-- Sử dụng updatedMovies từ Supabase -->
              <template v-else-if="updatedMovies?.length">
                <MovieCardRecommended
                  v-for="(item, index) in updatedMovies.slice(0, 8)"
                  :key="item.slug || item.id || index"
                  :movie="item"
                  :trending="false"
                  class="animate-fade-in"
                />
              </template>
              <p v-else class="text-center col-span-full text-gray-400">Không có dữ liệu phim mới.</p>
            </div>
          </div>
          <!-- Bảng xếp hạng với cuộn -->
          <div class="flex-1 bg-gray-700 p-4 rounded-xl shadow-md">
            <h3 class="uppercase text-[#dd6119] text-sm font-semibold border-b-2 border-dashed border-gray-600 pb-2 mb-4">
              Bảng Xếp Hạng
            </h3>
            <div class="max-h-96 min-h-[400px] overflow-y-auto scrollbar-custom pr-2">
              <template v-if="isLoading">
                <div v-for="i in 8" :key="i" class="animate-pulse mb-4">
                  <div class="flex gap-3 items-start">
                    <div class="bg-gray-400 w-8 h-4 rounded"></div>
                    <div class="bg-gray-400 w-[55px] h-[70px] rounded"></div>
                    <div class="flex flex-col flex-1 gap-2">
                      <div class="bg-gray-400 h-4 w-3/4 rounded"></div>
                      <div class="bg-gray-400 h-3 w-1/2 rounded"></div>
                    </div>
                  </div>
                </div>
              </template>
              <!-- Sử dụng recommendedMovies từ Supabase -->
              <template v-else-if="recommendedMovies?.length">
                <Rank
                  v-for="(item, index) in recommendedMovies"
                  :key="item.slug || item.id || index"
                  :trendingMovie="item"
                  :index="index"
                />
              </template>
              <p v-else class="text-gray-400">Không có dữ liệu xếp hạng.</p>
            </div>
          </div>
        </div>
      </section>
      <!-- Phim Bộ -->
      <section class="bg-gray-900 p-6 rounded-xl shadow-lg">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-white hover:text-[#dd6119] transition-colors">Phim Bộ</h2>
          <router-link to="/movies/series" class="text-blue-400 hover:text-blue-300 text-sm font-medium">Xem thêm →</router-link>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
          <template v-if="isLoading">
            <div v-for="i in 10" :key="i" class="animate-pulse">
              <div class="bg-gray-700 h-64 rounded-lg"></div>
            </div>
          </template>
          <!-- Sử dụng seriesMovies từ Supabase -->
          <template v-else-if="seriesMovies?.length">
            <MovieCardRecommended
              v-for="(item, index) in seriesMovies.slice(0, 10)"
              :key="item.slug || item.id || index"
              :movie="item"
              :trending="false"
              class="animate-fade-in"
            />
          </template>
          <p v-else class="text-center col-span-full text-gray-400">Không có dữ liệu phim bộ.</p>
        </div>
      </section>
      <!-- Phim Lẻ -->
      <section class="bg-gray-900 p-6 rounded-xl shadow-lg">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-white hover:text-[#dd6119] transition-colors">Phim Lẻ</h2>
          <router-link to="/movies/single" class="text-blue-400 hover:text-blue-300 text-sm font-medium">Xem thêm →</router-link>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
          <template v-if="isLoading">
            <div v-for="i in 10" :key="i" class="animate-pulse">
              <div class="bg-gray-700 h-64 rounded-lg"></div>
            </div>
          </template>
          <!-- Sử dụng singleMovies từ Supabase -->
          <template v-else-if="singleMovies?.length">
            <MovieCardRecommended
              v-for="(item, index) in singleMovies.slice(0, 10)"
              :key="item.slug || item.id || index"
              :movie="item"
              :trending="false"
              class="animate-fade-in"
            />
          </template>
          <p v-else class="text-center col-span-full text-gray-400">Không có dữ liệu phim lẻ.</p>
        </div>
      </section>
    </template>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { supabase } from '../supabaseClient'; // Đảm bảo đường dẫn này đúng

// Firebase imports cho xác thực
import { getAuth, onAuthStateChanged } from 'firebase/auth';
import { initializeApp } from 'firebase/app';

// Component imports
import MovieCardRecommended from './MovieCardRecommended.vue';
import Rank from './Rank.vue';

// --- Cấu hình Firebase (Lấy từ Firebase Console - Đảm bảo khớp với main.js) ---
const firebaseConfig = {
  apiKey: "AIzaSyAiwMHCsSrqX20x8o90R5hksRxUPVmnwKM",
  authDomain: "hthree-94a3e.firebaseapp.com",
  projectId: "hthree-94a3e",
  storageBucket: "hthree-94a3e.firebasestorage.app",
  messagingSenderId: "917487991713",
  appId: "1:917487991713:web:68dd73293f38d7fe2229e0",
  measurementId: "G-9LNBDMVK0M"
};

// Khởi tạo Firebase App và Auth instance
// Việc khởi tạo lại `appFirebaseInstance` và `auth` ở đây không gây lỗi nếu chúng đã được
// khởi tạo ở `main.js`, Firebase sẽ tự động quản lý singleton.
const appFirebaseInstance = initializeApp(firebaseConfig);
const auth = getAuth(appFirebaseInstance);

// --- Biến Reactive cho Trạng thái Xác thực ---
const isAuthenticated = ref(false); // Ref cục bộ cho trạng thái đăng nhập

// Lắng nghe trạng thái xác thực từ Firebase Auth
onMounted(() => {
  onAuthStateChanged(auth, (user) => {
    isAuthenticated.value = !!user; // Cập nhật giá trị của ref
    console.log('Firebase Auth state updated in NewMovieUpdate.vue:', isAuthenticated.value);
  });
});


// --- Dữ liệu Phim và Trạng thái Loading ---
// Các props này có thể bị loại bỏ nếu dữ liệu hoàn toàn được fetch trong component này.
// Tuy nhiên, giữ lại nếu bạn vẫn muốn có khả năng truyền dữ liệu ban đầu từ component cha.
const props = defineProps({
  update: { type: Array, default: () => [] },
  series: { type: Array, default: () => [] },
  movie: { type: Array, default: () => [] },
  recommended: { type: Array, default: () => [] },
});

// Các ref để lưu trữ dữ liệu phim lấy từ Supabase
const updatedMovies = ref([]);
const seriesMovies = ref([]);
const singleMovies = ref([]); // Đổi tên để tránh trùng với prop 'movie'
const recommendedMovies = ref([]);

const isLoading = ref(true); // Trạng thái loading cho việc fetch data

// --- Hàm lấy dữ liệu phim từ Supabase ---
const fetchMoviesFromSupabase = async () => {
  isLoading.value = true;
  try {
    // Lấy phim mới cập nhật (sắp xếp theo created_at giảm dần)
    const { data: updateData, error: updateError } = await supabase
      .from('movies')
      .select('*')
      .order('created_at', { ascending: false })
      .limit(8); // Lấy 8 phim mới nhất
    if (updateError) throw updateError;
    updatedMovies.value = updateData;

    // Lấy phim bộ (thêm điều kiện .eq('type', 'series') nếu có cột phân loại)
    const { data: seriesData, error: seriesError } = await supabase
      .from('movies')
      .select('*')
       .eq('type', 'series') // Uncomment nếu bạn có cột 'type' để phân loại phim bộ
      .limit(10); // Lấy 10 phim bộ
    if (seriesError) throw seriesError;
    seriesMovies.value = seriesData;

    // Lấy phim lẻ (thêm điều kiện .eq('type', 'single') nếu có cột phân loại)
    const { data: singleData, error: singleError } = await supabase
      .from('movies')
      .select('*')
       .eq('type', 'single') // Uncomment nếu bạn có cột 'type' để phân loại phim lẻ
      .limit(10); // Lấy 10 phim lẻ
    if (singleError) throw singleError;
    singleMovies.value = singleData;

    // Lấy phim đề xuất/bảng xếp hạng (sắp xếp theo vote_average giảm dần)
    const { data: recommendedData, error: recommendedError } = await supabase
      .from('movies')
      .select('*')
      .order('vote_average', { ascending: false })
      .limit(8); // Lấy 8 phim có điểm cao nhất
    if (recommendedError) throw recommendedError;
    recommendedMovies.value = recommendedData;

  } catch (error) {
    console.error("Lỗi khi tải dữ liệu phim từ Supabase:", error.message);
    // Bạn có thể hiển thị thông báo lỗi trên UI tại đây
  } finally {
    isLoading.value = false;
  }
};

// Gọi hàm tải dữ liệu khi component được mount
onMounted(() => {
  fetchMoviesFromSupabase();
});


// --- Dữ liệu Form thêm phim mới ---
const newMovie = reactive({
  title: '', // Tương ứng với cột 'title' trong DB
  original_title: '', // Tương ứng với cột 'original_title'
  overview: '', // Tương ứng với cột 'overview'
  release_date: '', // Tương ứng với cột 'release_date'
  poster_path: '', // Tương ứng với cột 'poster_path'
  backdrop_path: '', // Tương ứng với cột 'backdrop_path'
  vote_average: '', // Tương ứng với cột 'vote_average'
  runtime: '', // Tương ứng với cột 'runtime'
  tagline: '', // Tương ứng với cột 'tagline'
  genresInput: '', // Input dạng chuỗi, sẽ được chuyển thành mảng cho cột 'genres'
});

// Thông báo cho người dùng
const message = ref('');
const messageType = ref('');

// --- Hàm thêm phim mới vào Supabase ---
const addMovie = async () => {
  // Kiểm tra trạng thái xác thực từ ref cục bộ
  if (!isAuthenticated.value) {
    message.value = 'Vui lòng đăng nhập để thêm phim!';
    messageType.value = 'error';
    return;
  }

  // Chuẩn bị dữ liệu để chèn vào bảng 'movies' trong Supabase
  const movieDataToInsert = {
    title: newMovie.title,
    original_title: newMovie.original_title || null, // Có thể null
    overview: newMovie.overview || null, // Có thể null
    release_date: newMovie.release_date || null, // Có thể null
    poster_path: newMovie.poster_path, // Yêu cầu có giá trị
    backdrop_path: newMovie.backdrop_path || null, // Có thể null
    vote_average: newMovie.vote_average ? parseFloat(newMovie.vote_average) : null, // Chuyển đổi sang số, có thể null
    runtime: newMovie.runtime ? parseInt(newMovie.runtime) : null, // Chuyển đổi sang số nguyên, có thể null
    tagline: newMovie.tagline || null, // Có thể null
    // Chuyển đổi chuỗi nhập liệu (ví dụ: "Action, Comedy") thành một mảng chuỗi
    genres: newMovie.genresInput ? newMovie.genresInput.split(',').map(g => g.trim()) : [],
    // Các cột như 'id' và 'created_at' sẽ được Supabase tự động điền nếu bạn đã cấu hình DEFAULT VALUE trong DB
  };

  try {
    const { data, error } = await supabase
      .from('movies') // Tên bảng của bạn trên Supabase
      .insert([movieDataToInsert]) // Chèn một mảng các đối tượng phim
      .select(); // Yêu cầu trả về dữ liệu của bản ghi vừa được chèn

    if (error) {
      console.error("Lỗi khi thêm phim vào Supabase:", error);
      message.value = 'Lỗi khi thêm phim: ' + error.message;
      messageType.value = 'error';
    } else {
      console.log("Phim đã được thêm thành công vào Supabase:", data);
      message.value = 'Thêm phim thành công!';
      messageType.value = 'success';
      // Xóa dữ liệu trong form sau khi thêm thành công
      Object.assign(newMovie, {
        title: '', original_title: '', overview: '', release_date: '',
        poster_path: '', backdrop_path: '', vote_average: '', runtime: '',
        tagline: '', genresInput: ''
      });
      // Tải lại dữ liệu phim để cập nhật UI ngay lập tức
      fetchMoviesFromSupabase();
    }
  } catch (err) {
    console.error("Lỗi không xác định khi thêm phim:", err);
    message.value = 'Lỗi không xác định khi thêm phim: ' + err.message;
    messageType.value = 'error';
  } finally {
    // Ẩn thông báo sau 3 giây
    setTimeout(() => { message.value = ''; messageType.value = ''; }, 3000);
  }
};
</script>

<style scoped>
/* --- CSS Scoped Styles --- */
.animate-fade-in {
  animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 640px) {
  .flex { flex-direction: column; }
  .flex-1 { width: 100%; }
}

/* Tùy chỉnh thanh cuộn */
.scrollbar-custom {
  scrollbar-width: thin;
  scrollbar-color: #dd6119 #2d3748; /* Màu thumb và track */
}

.scrollbar-custom::-webkit-scrollbar {
  width: 8px; /* Tăng chiều rộng để dễ thấy */
}

.scrollbar-custom::-webkit-scrollbar-track {
  background: #2d3748; /* Màu nền track */
  border-radius: 4px;
}

.scrollbar-custom::-webkit-scrollbar-thumb {
  background: #dd6119; /* Màu thumb */
  border-radius: 4px;
  border: 2px solid #2d3748; /* Viền thumb */
}

.scrollbar-custom::-webkit-scrollbar-thumb:hover {
  background: #b45309; /* Màu khi hover */
}
</style>
