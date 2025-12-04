<template>
  <!-- Modal Overlay -->
  <Transition name="modal">               
    <div 
      v-if="modelValue"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
      @click.self="closeModal"
    >
      <!-- Modal Container -->
      <div class="relative w-full max-w-5xl bg-gray-900 rounded-2xl shadow-2xl overflow-hidden transform transition-all">
        <!-- Close Button -->
        <button 
          @click="closeModal"
          class="absolute top-4 right-4 z-10 text-gray-400 hover:text-white transition-colors"
        >
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <!-- 2 Column Layout -->
        <div class="grid md:grid-cols-2 min-h-[600px]">
          <!-- Left: Image -->
          <div class="hidden md:block relative overflow-hidden">
            <img 
              src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=800&q=80"
              alt="Cinema"
              class="absolute inset-0 w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-transparent to-gray-900/50"></div>
            <div class="absolute inset-0 flex flex-col justify-end p-8 text-white">
              <div class="flex items-center gap-2 mb-4">
                <svg class="w-12 h-12 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                </svg>
                <span class="text-3xl font-bold text-yellow-400">HTHREE</span>
              </div>
              <h2 class="text-3xl font-bold mb-2">Chào mừng trở lại!</h2>
              <p class="text-gray-300">Khám phá hàng ngàn bộ phim hay nhất</p>
            </div>
          </div>

          <!-- Right: Form -->
          <div class="p-6 md:p-8 overflow-y-auto max-h-[600px] scrollbar-hide">
            <!-- Logo mobile -->
            <div class="md:hidden flex justify-center mb-6">
              <div class="flex items-center gap-2">
                <svg class="w-10 h-10 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                </svg>
                <span class="text-3xl font-bold text-yellow-400">HTHREE</span>
              </div>
            </div>

            <!-- Title -->
            <h2 class="text-2xl font-bold text-white mb-1">
              {{ isLogin ? 'Đăng Nhập' : 'Đăng Ký' }}
            </h2>
            <p class="text-sm text-gray-400 mb-4">
              {{ isLogin ? 'Chào mừng bạn trở lại!' : 'Tạo tài khoản mới' }}
            </p>

            <!-- Error/Success -->
            <div v-if="error" class="mb-4 p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-red-400 text-sm">
              {{ error }}
            </div>
            <div v-if="success" class="mb-4 p-3 bg-green-500/20 border border-green-500/50 rounded-lg text-green-400 text-sm">
              {{ success }}
            </div>

            <!-- Google Button -->
            <button
              @click="handleGoogleAuth"
              type="button"
              :disabled="loading"
              class="w-full flex items-center justify-center gap-2 bg-white hover:bg-gray-100 text-gray-800 font-medium py-2.5 rounded-lg transition-all mb-4 text-sm"
            >
              <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" class="w-4 h-4" />
              {{ isLogin ? 'Đăng nhập' : 'Đăng ký' }} với Google
            </button>

            <!-- Divider -->
            <div class="relative mb-4">
              <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-700"></div>
              </div>
              <div class="relative flex justify-center text-xs">
                <span class="px-3 bg-gray-900 text-gray-400">Hoặc</span>
              </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleSubmit" class="space-y-3">
              <div v-if="!isLogin">
                <input
                  type="text"
                  placeholder="Tên đăng nhập"
                  v-model="username"
                  class="w-full px-3 py-2.5 text-sm rounded-lg bg-gray-800 text-white placeholder-gray-500 border border-gray-700 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400"
                  required
                />
              </div>

              <div v-if="!isLogin">
                <input
                  type="text"
                  placeholder="Họ và tên"
                  v-model="fullName"
                  class="w-full px-3 py-2.5 text-sm rounded-lg bg-gray-800 text-white placeholder-gray-500 border border-gray-700 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400"
                />
              </div>

              <div>
                <input
                  :type="isLogin ? 'text' : 'email'"
                  :placeholder="isLogin ? 'Tên đăng nhập hoặc Email' : 'Email'"
                  v-model="email"
                  class="w-full px-3 py-2.5 text-sm rounded-lg bg-gray-800 text-white placeholder-gray-500 border border-gray-700 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400"
                  required
                />
              </div>

              <div class="relative">
                <input
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Mật khẩu"
                  v-model="password"
                  class="w-full px-3 py-2.5 text-sm rounded-lg bg-gray-800 text-white placeholder-gray-500 border border-gray-700 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400"
                  required
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
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

              <div v-if="!isLogin" class="relative">
                <input
                  :type="showConfirmPassword ? 'text' : 'password'"
                  placeholder="Nhập lại mật khẩu"
                  v-model="confirmPassword"
                  class="w-full px-3 py-2.5 text-sm rounded-lg bg-gray-800 text-white placeholder-gray-500 border border-gray-700 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400"
                  required
                />
                <button
                  type="button"
                  @click="showConfirmPassword = !showConfirmPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
                >
                  <svg v-if="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                  </svg>
                </button>
              </div>

              <button
                type="submit"
                :disabled="loading"
                class="w-full py-2.5 text-sm bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 text-black font-bold rounded-lg transition-all disabled:opacity-50"
              >
                <span v-if="!loading">{{ isLogin ? 'Đăng Nhập' : 'Đăng Ký' }}</span>
                <span v-else>Đang xử lý...</span>
              </button>
            </form>

            <!-- Toggle -->
            <div class="mt-4 text-center">
              <button @click="toggleMode" class="text-sm text-gray-400 hover:text-yellow-400">
                {{ isLogin ? 'Chưa có tài khoản?' : 'Đã có tài khoản?' }}
                <span class="font-semibold">{{ isLogin ? 'Đăng ký ngay' : 'Đăng nhập' }}</span>
              </button>
            </div>

            <!-- Demo -->
            <div class="mt-4 p-3 bg-gray-800/50 rounded-lg border border-gray-700">
              <p class="text-xs text-gray-400 mb-1.5">Tài khoản demo:</p>
              <div class="space-y-0.5 text-xs text-gray-500">
                <p>👤 admin / 123456</p>
                <p>👤 user1 / 123456</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref } from 'vue';
import { getAuth, signInWithPopup, GoogleAuthProvider, signInWithEmailAndPassword, createUserWithEmailAndPassword, updateProfile } from 'firebase/auth';
import { toast } from 'vue3-toastify';

const props = defineProps({
  modelValue: Boolean
});

const emit = defineEmits(['update:modelValue', 'success']);

const auth = getAuth();
const googleProvider = new GoogleAuthProvider();
googleProvider.setCustomParameters({ prompt: 'select_account' });

const isLogin = ref(true);
const username = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const fullName = ref('');
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const error = ref('');
const success = ref('');
const loading = ref(false);

const closeModal = () => {
  emit('update:modelValue', false);
  // Reset form
  setTimeout(() => {
    error.value = '';
    success.value = '';
    username.value = '';
    email.value = '';
    password.value = '';
    confirmPassword.value = '';
    fullName.value = '';
  }, 300);
};

const toggleMode = () => {
  isLogin.value = !isLogin.value;
  error.value = '';
  success.value = '';
};

const handleSubmit = async () => {
  loading.value = true;
  error.value = '';
  success.value = '';

  try {
    if (isLogin.value) {
      // Firebase Login
      const result = await signInWithEmailAndPassword(auth, email.value, password.value);
      const user = result.user;
      
      // Get Firebase token
      const token = await user.getIdToken();
      
      // Sync to MySQL and save to localStorage
      await syncUserToMySQL(user, token);
      
      // Verify localStorage was saved
      console.log('✅ Saved to localStorage:', {
        user: localStorage.getItem('user'),
        token: localStorage.getItem('token')
      });
      
      success.value = 'Đăng nhập thành công!';
      toast.success('Đăng nhập thành công!');
      
      // Wait a bit before reload to ensure localStorage is saved
      await new Promise(resolve => setTimeout(resolve, 500));
      
      emit('success');
      closeModal();
      window.location.reload();
      
    } else {
      // Validate password confirmation
      if (password.value !== confirmPassword.value) {
        error.value = 'Mật khẩu xác nhận không khớp';
        toast.error('Mật khẩu xác nhận không khớp');
        loading.value = false;
        return;
      }
      
      // Firebase Register
      const result = await createUserWithEmailAndPassword(auth, email.value, password.value);
      const user = result.user;
      
      // Update profile
      await updateProfile(user, {
        displayName: fullName.value || username.value
      });
      
      // Get Firebase token
      const token = await user.getIdToken();
      
      // Sync to MySQL and save to localStorage
      await syncUserToMySQL(user, token);
      
      success.value = 'Đăng ký thành công! Vui lòng đăng nhập lại.';
      toast.success('Đăng ký thành công!');
      
      // Chuyển sang form đăng nhập và giữ email
      const registeredEmail = email.value;
      setTimeout(() => {
        isLogin.value = true;
        email.value = registeredEmail; // Giữ email đã đăng ký
        password.value = ''; // Xóa mật khẩu để nhập lại
        confirmPassword.value = '';
        username.value = '';
        fullName.value = '';
        success.value = '';
        error.value = '';
      }, 1500);
    }
    
  } catch (err) {
    console.error('Firebase auth error:', err);
    
    // Firebase error messages
    const errorMessages = {
      'auth/user-not-found': 'Không tìm thấy tài khoản với email này',
      'auth/wrong-password': 'Mật khẩu không đúng',
      'auth/email-already-in-use': 'Email đã được sử dụng',
      'auth/weak-password': 'Mật khẩu quá yếu (tối thiểu 6 ký tự)',
      'auth/invalid-email': 'Email không hợp lệ',
      'auth/invalid-credential': 'Email hoặc mật khẩu không đúng',
      'auth/too-many-requests': 'Quá nhiều lần thử. Vui lòng thử lại sau'
    };
    
    error.value = errorMessages[err.code] || 'Có lỗi xảy ra. Vui lòng thử lại';
    toast.error(error.value);
  } finally {
    loading.value = false;
  }
};

// Helper function to sync user to MySQL
const syncUserToMySQL = async (user, token) => {
  let mysqlUserId = null;
  
  try {
    const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost/HTHREE_film/HTHREE_film/backend/api';
    const response = await fetch(`${API_URL}/auth/sync_user.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        firebase_uid: user.uid,
        email: user.email,
        username: user.displayName || user.email.split('@')[0],
        full_name: user.displayName
      })
    });
    
    const data = await response.json();
    mysqlUserId = data.success ? data.user_id : null;
    console.log('📊 MySQL sync result:', data);
  } catch (syncError) {
    console.error('Failed to sync user to MySQL:', syncError);
  }
  
  // Always save to localStorage (even if MySQL sync fails)
  const userData = {
    id: mysqlUserId || user.uid,
    firebase_uid: user.uid,
    username: user.displayName || user.email.split('@')[0],
    email: user.email,
    full_name: user.displayName,
    avatar: user.photoURL
  };
  
  localStorage.setItem('user', JSON.stringify(userData));
  localStorage.setItem('token', token);
  
  console.log('💾 Saved to localStorage:', userData);
};

const handleGoogleAuth = async () => {
  loading.value = true;
  error.value = '';
  
  try {
    const result = await signInWithPopup(auth, googleProvider);
    const user = result.user;
    
    // Đồng bộ user vào MySQL database và lấy user_id
    let mysqlUserId = null;
    try {
      const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost/HTHREE_film/HTHREE_film/backend/api';
      const response = await fetch(`${API_URL}/auth/sync_user.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          firebase_uid: user.uid,
          email: user.email,
          username: user.displayName || user.email.split('@')[0],
          full_name: user.displayName,
          avatar: user.photoURL
        })
      });
      const data = await response.json();
      if (data.success && data.user_id) {
        mysqlUserId = data.user_id;
      }
    } catch (syncError) {
      console.error('Failed to sync user to MySQL:', syncError);
    }
    
    localStorage.setItem('user', JSON.stringify({
      id: mysqlUserId || user.uid, // Dùng MySQL user_id nếu có
      firebase_uid: user.uid,
      username: user.displayName,
      email: user.email,
      full_name: user.displayName,
      avatar: user.photoURL
    }));
    localStorage.setItem('token', user.accessToken);
    
    toast.success('Đăng nhập Google thành công!');
    setTimeout(() => {
      emit('success');
      closeModal();
      window.location.reload();
    }, 1000);
  } catch (err) {
    console.error('Google auth error:', err);
    error.value = 'Đăng nhập Google thất bại';
    toast.error(error.value);
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
  transition: transform 0.3s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
  transform: scale(0.95);
}

/* Ẩn scrollbar */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
