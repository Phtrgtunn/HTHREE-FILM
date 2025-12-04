<template>
  <div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 z-50 flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
    
    <!-- Animated background -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute w-96 h-96 bg-blue-500/10 rounded-full blur-3xl -top-48 -left-48 animate-pulse"></div>
      <div class="absolute w-96 h-96 bg-purple-500/10 rounded-full blur-3xl -bottom-48 -right-48 animate-pulse delay-1000"></div>
    </div>

    <div class="relative z-10 w-full max-w-md p-6 max-h-screen overflow-y-auto">
      <div class="bg-gray-900/80 backdrop-blur-xl rounded-2xl shadow-2xl p-8 border border-gray-700/50">
        <!-- Close button -->
        <button 
          @click="router.push('/home')" 
          class="absolute top-4 right-4 text-gray-400 hover:text-white text-2xl transition-colors"
        >
          ×
        </button>

        <!-- Logo -->
        <div class="flex justify-center mb-6">
          <div class="flex items-center gap-2">
            <svg class="w-10 h-10 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
              <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
            </svg>
            <span class="text-3xl font-bold text-yellow-400">HTHREE</span>
          </div>
        </div>

        <!-- Title -->
        <h2 class="text-3xl font-bold text-center text-white mb-2">
          {{ isLogin ? 'Đăng Nhập' : 'Đăng Ký' }}
        </h2>
        <p class="text-center text-gray-400 mb-8">
          {{ isLogin ? 'Chào mừng bạn trở lại!' : 'Tạo tài khoản mới' }}
        </p>

        <!-- Error message -->
        <div v-if="error" class="mb-4 p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-red-400 text-sm">
          {{ error }}
        </div>

        <!-- Success message -->
        <div v-if="success" class="mb-4 p-3 bg-green-500/20 border border-green-500/50 rounded-lg text-green-400 text-sm">
          {{ success }}
        </div>

        <!-- Social Login Button -->
        <div class="mb-6">
          <button
            @click="handleGoogleAuth"
            type="button"
            :disabled="loading"
            class="w-full flex items-center justify-center gap-3 bg-white hover:bg-gray-100 text-gray-800 font-semibold py-3 rounded-lg transition-all transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-md"
          >
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" class="w-5 h-5" />
            {{ isLogin ? 'Đăng nhập' : 'Đăng ký' }} với Google
          </button>
        </div>

        <!-- Divider -->
        <div class="relative mb-6">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-700"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-gray-900 text-gray-400">Hoặc {{ isLogin ? 'đăng nhập' : 'đăng ký' }} với email</span>
          </div>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- Username (chỉ hiện khi đăng ký) -->
          <div v-if="!isLogin" class="relative">
            <div class="absolute top-3 left-3 text-gray-400">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <input
              type="text"
              placeholder="Tên đăng nhập"
              v-model="username"
              class="w-full pl-10 pr-4 py-3 rounded-lg bg-gray-800/50 text-white placeholder-gray-500 border border-gray-700 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 transition-all"
              required
            />
          </div>

          <!-- Full name (chỉ hiện khi đăng ký) -->
          <div v-if="!isLogin" class="relative">
            <div class="absolute top-3 left-3 text-gray-400">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <input
              type="text"
              placeholder="Họ và tên"
              v-model="fullName"
              class="w-full pl-10 pr-4 py-3 rounded-lg bg-gray-800/50 text-white placeholder-gray-500 border border-gray-700 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 transition-all"
            />
          </div>

          <!-- Email (hoặc Username khi đăng nhập) -->
          <div class="relative">
            <div class="absolute top-3 left-3 text-gray-400">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
              </svg>
            </div>
            <input
              :type="isLogin ? 'text' : 'email'"
              :placeholder="isLogin ? 'Tên đăng nhập hoặc Email' : 'Email'"
              v-model="email"
              class="w-full pl-10 pr-4 py-3 rounded-lg bg-gray-800/50 text-white placeholder-gray-500 border border-gray-700 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 transition-all"
              required
            />
          </div>

          <!-- Password -->
          <div class="relative">
            <div class="absolute top-3 left-3 text-gray-400">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
            </div>
            <input
              :type="showPassword ? 'text' : 'password'"
              placeholder="Mật khẩu"
              v-model="password"
              class="w-full pl-10 pr-12 py-3 rounded-lg bg-gray-800/50 text-white placeholder-gray-500 border border-gray-700 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 transition-all"
              required
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute top-3 right-3 text-gray-400 hover:text-white transition-colors"
            >
              <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
              </svg>
            </button>
          </div>

          <!-- Submit button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3 bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 text-black font-bold rounded-lg transition-all transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
          >
            <span v-if="!loading">{{ isLogin ? 'Đăng Nhập' : 'Đăng Ký' }}</span>
            <span v-else class="flex items-center justify-center gap-2">
              <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Đang xử lý...
            </span>
          </button>
        </form>

        <!-- Toggle mode -->
        <div class="mt-6 text-center">
          <button
            @click="toggleMode"
            class="text-gray-400 hover:text-yellow-400 transition-colors"
          >
            {{ isLogin ? 'Chưa có tài khoản?' : 'Đã có tài khoản?' }}
            <span class="font-semibold">{{ isLogin ? 'Đăng ký ngay' : 'Đăng nhập' }}</span>
          </button>
        </div>

        <!-- Demo accounts -->
        <div class="mt-6 p-4 bg-gray-800/50 rounded-lg border border-gray-700">
          <p class="text-xs text-gray-400 mb-2">Tài khoản demo:</p>
          <div class="space-y-1 text-xs text-gray-500">
            <p>👤 admin / 123456</p>
            <p>👤 user1 / 123456</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import authService from '@/services/authService';
import { useToast } from '@/composables/useToast';
import { 
  getAuth, 
  signInWithPopup, 
  GoogleAuthProvider
} from 'firebase/auth';

const router = useRouter();
const auth = getAuth();
const googleProvider = new GoogleAuthProvider();
const toast = useToast();

googleProvider.setCustomParameters({ prompt: 'select_account' });

const isLogin = ref(true);
const username = ref('');
const email = ref('');
const password = ref('');
const fullName = ref('');
const showPassword = ref(false);
const error = ref('');
const success = ref('');
const loading = ref(false);

// Lock body scroll and hide scrollbar when component mounts
onMounted(() => {
  document.body.style.overflow = 'hidden';
});

// Unlock body scroll when component unmounts
onUnmounted(() => {
  document.body.style.overflow = '';
});

const toggleMode = () => {
  isLogin.value = !isLogin.value;
  error.value = '';
  success.value = '';
  username.value = '';
  email.value = '';
  password.value = '';
  fullName.value = '';
};

const handleSubmit = async () => {
  loading.value = true;
  error.value = '';
  success.value = '';

  try {
    if (isLogin.value) {
      // Đăng nhập (dùng email field vì nó chứa username hoặc email)
      const response = await authService.login(email.value, password.value);
      
      if (response.status) {
        success.value = response.message;
        toast.success('🎉 Đăng nhập thành công!');
        setTimeout(() => {
          router.push('/home');
        }, 1000);
      } else {
        error.value = response.message;
        toast.error('❌ ' + response.message);
      }
    } else {
      // Đăng ký
      const response = await authService.register(
        username.value,
        email.value,
        password.value,
        fullName.value
      );
      
      if (response.status) {
        success.value = response.message + ' - Đang chuyển sang đăng nhập...';
        toast.success('🎉 Đăng ký thành công! Chào mừng bạn!');
        setTimeout(() => {
          isLogin.value = true;
          username.value = response.user.username;
          password.value = '';
        }, 1500);
      } else {
        error.value = response.message;
        toast.error('❌ ' + response.message);
      }
    }
  } catch (err) {
    error.value = 'Có lỗi xảy ra. Vui lòng thử lại.';
    toast.error('❌ Có lỗi xảy ra. Vui lòng thử lại!');
    console.error('Auth error:', err);
  } finally {
    loading.value = false;
  }
};

// Đăng nhập với Google
const handleGoogleAuth = async () => {
  loading.value = true;
  error.value = '';
  
  try {
    const result = await signInWithPopup(auth, googleProvider);
    const user = result.user;
    
    // Lưu user vào localStorage (giống PHP auth)
    localStorage.setItem('user', JSON.stringify({
      id: user.uid,
      username: user.displayName,
      email: user.email,
      full_name: user.displayName,
      avatar: user.photoURL
    }));
    localStorage.setItem('token', user.accessToken);
    
    toast.success('🎉 Đăng nhập Google thành công!');
    setTimeout(() => {
      router.push('/home');
    }, 1000);
  } catch (err) {
    console.error('Google auth error:', err);
    if (err.code === 'auth/popup-closed-by-user') {
      error.value = 'Bạn đã đóng cửa sổ đăng nhập';
    } else if (err.code === 'auth/popup-blocked') {
      error.value = 'Trình duyệt chặn popup. Vui lòng cho phép popup.';
    } else {
      error.value = 'Đăng nhập Google thất bại';
    }
    toast.error('❌ ' + error.value);
  } finally {
    loading.value = false;
  }
};


</script>

<style scoped>
.delay-1000 {
  animation-delay: 1s;
}
</style>


<style scoped>
/* Hide scrollbar for the inner container */
.overflow-y-auto::-webkit-scrollbar {
  display: none;
}

.overflow-y-auto {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
