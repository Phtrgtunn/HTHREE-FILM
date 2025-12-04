<template>
  <div class="comment-form bg-gray-800 rounded-xl p-6 border border-gray-700">
    <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
      <span>💬</span>
      Bình luận của bạn
    </h3>
    
    <!-- User info -->
    <div v-if="user" class="flex items-center gap-3 mb-4">
      <img 
        :src="getUserAvatar()"
        :alt="user.displayName || user.email"
        class="w-10 h-10 rounded-full border-2 border-yellow-400 object-cover"
        @error="handleAvatarError"
      />
      <div>
        <p class="text-white font-medium text-sm">{{ user.displayName || user.email?.split('@')[0] }}</p>
        <p class="text-gray-400 text-xs">{{ user.email }}</p>
      </div>
    </div>
    
    <!-- Comment textarea -->
    <textarea
      v-model="commentText"
      :disabled="!user"
      :placeholder="user ? 'Chia sẻ suy nghĩ của bạn... (Enter để gửi, Shift+Enter để xuống dòng)' : 'Vui lòng đăng nhập để bình luận'"
      class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 transition resize-none"
      rows="4"
      maxlength="500"
      @keydown.enter="handleEnterKey"
    ></textarea>
    
    <!-- Character count -->
    <div class="flex items-center justify-between mt-2">
      <span class="text-xs text-gray-500">{{ commentText.length }}/500 ký tự</span>
      
      <!-- Submit button -->
      <button
        @click="submitComment"
        :disabled="!user || !commentText.trim() || isSubmitting"
        class="px-6 py-2 bg-yellow-400 text-black font-bold rounded-lg hover:bg-yellow-500 disabled:!bg-gray-600 disabled:!text-gray-400 disabled:cursor-not-allowed transition-all duration-300 hover:scale-105 disabled:hover:scale-100 flex items-center gap-2"
      >
        <svg v-if="isSubmitting" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>{{ isSubmitting ? 'Đang gửi...' : 'Gửi bình luận' }}</span>
      </button>
    </div>
    
    <!-- Login prompt -->
    <div v-if="!user" class="mt-4 p-4 bg-gray-900 rounded-lg border border-gray-700">
      <p class="text-gray-400 text-sm mb-3">Bạn cần đăng nhập để bình luận</p>
      <button
        @click="$emit('login')"
        class="w-full px-4 py-2 bg-gradient-to-r from-yellow-400 to-yellow-500 text-black font-bold rounded-lg hover:from-yellow-500 hover:to-yellow-600 transition-all duration-300 hover:scale-105"
      >
        Đăng nhập ngay
      </button>
    </div>
    
    <!-- Success message -->
    <div v-if="showSuccess" class="mt-4 p-4 bg-green-900/50 border border-green-500 rounded-lg flex items-center gap-3 animate-fade-in">
      <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
      </svg>
      <span class="text-green-400 text-sm font-medium">Bình luận của bạn đã được gửi thành công! 🎉</span>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { getAuth, onAuthStateChanged } from 'firebase/auth';
import axios from 'axios';
import { useToast } from '@/composables/useToast';

const toast = useToast();

const props = defineProps({
  movieSlug: {
    type: String,
    default: null
  }
});

const emit = defineEmits(['comment-added', 'login']);

const auth = getAuth();
const user = ref(null);

// Load user from both Firebase and PHP
const loadUser = () => {
  // Check Firebase first
  const firebaseUser = auth.currentUser;
  if (firebaseUser) {
    user.value = firebaseUser;
    console.log('🔐 CommentForm - Firebase user:', firebaseUser.email);
    return;
  }
  
  // Check PHP localStorage as fallback
  const phpUser = localStorage.getItem('user');
  if (phpUser) {
    try {
      user.value = JSON.parse(phpUser);
      console.log('🔐 CommentForm - PHP user:', user.value?.email);
    } catch (e) {
      console.error('Error parsing PHP user:', e);
    }
  }
};

// Listen to auth state changes
onMounted(() => {
  // Load initial user
  loadUser();
  
  // Listen for Firebase auth changes
  onAuthStateChanged(auth, (currentUser) => {
    if (currentUser) {
      user.value = currentUser;
      console.log('🔐 CommentForm - Firebase auth changed:', currentUser.email);
    } else {
      // If Firebase logout, check PHP
      loadUser();
    }
  });
  
  // Listen for localStorage changes (PHP login/logout)
  window.addEventListener('storage', loadUser);
});

const commentText = ref('');
const isSubmitting = ref(false);
const showSuccess = ref(false);
const avatarError = ref(false);

import { API_CONFIG } from '@/config/api';
const API_URL = API_CONFIG.BACKEND_URL;

const getUserAvatar = () => {
  if (avatarError.value || !user.value) {
    const name = user.value?.displayName || user.value?.email?.split('@')[0] || 'User';
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=f59e0b&color=000&size=128`;
  }
  
  if (user.value.photoURL) {
    return user.value.photoURL;
  }
  
  const name = user.value.displayName || user.value.email?.split('@')[0] || 'User';
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=f59e0b&color=000&size=128`;
};

const handleAvatarError = (event) => {
  avatarError.value = true;
  const name = user.value?.displayName || user.value?.email?.split('@')[0] || 'User';
  event.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=f59e0b&color=000&size=128`;
};

const handleEnterKey = (event) => {
  // Nếu chỉ nhấn Enter (không có Shift) thì gửi comment
  if (!event.shiftKey) {
    event.preventDefault(); // Ngăn xuống dòng
    submitComment();
  }
  // Nếu Shift+Enter thì để mặc định (xuống dòng)
};

const submitComment = async () => {
  if (!user.value || !commentText.value.trim()) return;
  
  isSubmitting.value = true;
  
  try {
    // Determine if user is from Firebase or PHP
    const isFirebaseUser = !!user.value.uid;
    const isPhpUser = !!user.value.id;
    
    console.log('🚀 Submitting comment...', {
      type: isFirebaseUser ? 'Firebase' : 'PHP',
      email: user.value.email,
      displayName: user.value.displayName || user.value.full_name,
      uid: user.value.uid,
      id: user.value.id
    });
    
    let userId;
    
    if (isFirebaseUser) {
      // Firebase user - need to register/get from database
      let avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.value.displayName || user.value.email.split('@')[0])}&background=f59e0b&color=000&size=128`;
      
      if (user.value.photoURL) {
        try {
          const uploadResponse = await axios.post(`${API_URL}/upload_avatar.php`, {
            firebase_uid: user.value.uid,
            photo_url: user.value.photoURL
          });
          
          if (uploadResponse.data.success) {
            avatarUrl = uploadResponse.data.avatar_url;
            console.log('✅ Avatar uploaded:', avatarUrl);
          }
        } catch (error) {
          console.log('⚠️ Avatar upload failed, using fallback');
        }
      }
      
      // Register/Get user from database
      const userResponse = await axios.post(`${API_URL}/users.php`, {
        firebase_uid: user.value.uid,
        email: user.value.email,
        username: user.value.email.split('@')[0],
        full_name: user.value.displayName || user.value.email.split('@')[0],
        avatar: avatarUrl
      });
      
      console.log('👤 Firebase user response:', userResponse.data);
      
      if (!userResponse.data.success) {
        throw new Error('Failed to register/get user');
      }
      
      userId = userResponse.data.user.id;
    } else if (isPhpUser) {
      // PHP user - already has ID
      userId = user.value.id;
      console.log('👤 PHP user ID:', userId);
    } else {
      throw new Error('Invalid user type');
    }
    
    // Submit comment
    const response = await axios.post(`${API_URL}/comments.php`, {
      user_id: userId,
      movie_slug: props.movieSlug,
      content: commentText.value.trim()
    });
    
    if (response.data.success) {
      // Show success message
      showSuccess.value = true;
      setTimeout(() => {
        showSuccess.value = false;
      }, 3000);
      
      // Clear form
      commentText.value = '';
      
      // Emit event to refresh comments
      emit('comment-added');
      
      toast.success('Bình luận đã được gửi!');
    } else {
      throw new Error(response.data.message || 'Failed to submit comment');
    }
    
  } catch (error) {
    console.error('Error submitting comment:', error);
    toast.error('Không thể gửi bình luận. Vui lòng thử lại!');
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}
</style>
