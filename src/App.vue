<template>
  <!-- Splash Screen -->
  <SplashScreen />
  
  <!-- Command Palette -->
  <CommandPalette :show="showCommandPalette" @close="showCommandPalette = false" />
  
  <!-- Keyboard Shortcuts Help -->
  <KeyboardShortcutsHelp :show="showShortcutsHelp" @close="showShortcutsHelp = false" />
  
  <!-- Offline Banner -->
  <OfflineBanner />
  
  <div class="flex flex-col min-h-screen m-0 p-0 bg-black">
    <!-- Navbar (ẩn khi ở trang admin) -->
    <Navbar v-if="!isAdminPage" />

    <!-- Trạng thái đang kiểm tra đăng nhập -->
    <div
      v-if="user === undefined"
      class="flex flex-col items-center justify-center flex-grow bg-gradient-to-br from-indigo-100 to-gray-50"
    >
      <div class="text-center">
        <svg
          class="animate-spin h-12 w-12 text-indigo-600 mx-auto mb-4"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
        <p class="text-xl font-semibold text-gray-700">Đang kiểm tra đăng nhập...</p>
      </div>
    </div>

    <!-- Nội dung chính -->
    <main v-else class="flex-grow">
      <router-view v-slot="{ Component, route }">
        <transition
          :name="route.meta.transition || 'fade'"
          mode="out-in"
        >
          <component :is="Component" :key="route.path" />
        </transition>
      </router-view>
    </main>

    <!-- Footer (ẩn khi ở trang admin) -->
    <FooterComponent v-if="!isAdminPage" />
  </div>
  
  <!-- Scroll to Top Button (fixed to viewport) -->
  <ScrollToTop />
  
  <!-- Custom Toast -->
  <CustomToast ref="toastRef" />
  
  <!-- Global Components -->
  <ConfirmDialog />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { getAuth, onAuthStateChanged } from 'firebase/auth';
import Navbar from '@/components/NetflixNavbar.vue';
import SplashScreen from '@/components/SplashScreen.vue';
import CustomToast from '@/components/CustomToast.vue';
import FooterComponent from '@/components/FooterComponent.vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import ScrollToTop from '@/components/ScrollToTop.vue';
import CommandPalette from '@/components/CommandPalette.vue';
import KeyboardShortcutsHelp from '@/components/KeyboardShortcutsHelp.vue';
import OfflineBanner from '@/components/OfflineBanner.vue';
import { setToastInstance, useToast } from '@/composables/useToast';
import { useKeyboardShortcuts, isTyping } from '@/composables/useKeyboardShortcuts';

const route = useRoute();
const router = useRouter();
const user = ref(undefined);
const auth = getAuth();
const toastRef = ref(null);
const toast = useToast();

const isHomepage = computed(() => route.path === '/home');
const isAdminPage = computed(() => route.path.startsWith('/admin'));

// Command Palette & Shortcuts
const showCommandPalette = ref(false);
const showShortcutsHelp = ref(false);

// Global keyboard shortcuts
useKeyboardShortcuts({
  'ctrl+k': (e) => {
    if (!isTyping()) {
      showCommandPalette.value = true;
    }
  },
  '/': (e) => {
    if (!isTyping()) {
      const searchInput = document.querySelector('input[placeholder*="Tìm kiếm"]');
      if (searchInput) {
        searchInput.focus();
      }
    }
  },
  '?': (e) => {
    if (!isTyping()) {
      showShortcutsHelp.value = true;
    }
  },
  'escape': () => {
    showCommandPalette.value = false;
    showShortcutsHelp.value = false;
  },
  'g+h': () => {
    if (!isTyping()) router.push('/home');
  },
  'g+l': () => {
    if (!isTyping()) router.push('/library');
  },
  'g+p': () => {
    if (!isTyping()) router.push('/pricing');
  },
  'g+c': () => {
    if (!isTyping()) router.push('/cart');
  }
});

// Set toast instance and auth listener
onMounted(() => {
  if (toastRef.value) {
    setToastInstance(toastRef.value);
  }
  

  // Auth state listener with timeout
  let timeout = null;
  let authInitialized = false;
  
  const unsubscribe = onAuthStateChanged(auth, (currentUser) => {
    authInitialized = true;
    user.value = currentUser;
    if (timeout) {
      clearTimeout(timeout);
      timeout = null;
    }
  });
  
  // Only show error if auth doesn't initialize within 10 seconds
  timeout = setTimeout(() => {
    if (!authInitialized && user.value === undefined) {
      user.value = null;
      toast.error('Không thể kiểm tra trạng thái đăng nhập. Vui lòng thử lại!');
    }
  }, 10000);

  // Cleanup
  return () => {
    unsubscribe();
    if (timeout) {
      clearTimeout(timeout);
    }
  };
});
</script>

<style>
body {
  padding: 0;
  margin: 0;
  overflow-x: hidden;
}

html {
  overflow-x: hidden;
  margin: 0;
  padding: 0;
}

#app {
  margin: 0;
  padding: 0;
}

/* Hide Scrollbar */
::-webkit-scrollbar {
  display: none;
}

/* Firefox scrollbar */
* {
  scrollbar-width: none;
  -ms-overflow-style: none;
}

/* Page transition animations */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter-from {
  opacity: 0;
  transform: translateX(20px);
}

.fade-leave-to {
  opacity: 0;
  transform: translateX(-20px);
}

.slide-left-enter-active,
.slide-left-leave-active {
  transition: opacity 0.4s ease, transform 0.4s ease;
}

.slide-left-enter-from {
  opacity: 0;
  transform: translateX(30px);
}

.slide-left-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}

.slide-up-enter-active,
.slide-up-leave-active {
  transition: opacity 0.4s ease, transform 0.4s ease;
}

.slide-up-enter-from {
  opacity: 0;
  transform: translateY(30px);
}

.slide-up-leave-to {
  opacity: 0;
  transform: translateY(-30px);
}
</style>