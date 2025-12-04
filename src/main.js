// src/main.js
import { createApp } from 'vue';
import App from './App.vue';
import router from './router'; // Import router đã cấu hình tại src/router/index.js
import { createPinia } from 'pinia';
import { initializeApp } from 'firebase/app';
import { getAuth, onAuthStateChanged, setPersistence, browserLocalPersistence } from 'firebase/auth'; // Thêm auth
import './index.css'; // Import CSS toàn cục
import './assets/netflix.css'; // Import Netflix styles

// --- Cấu hình Firebase (lấy từ Firebase Console) ---
const firebaseConfig = {
  apiKey: import.meta.env.VITE_FIREBASE_API_KEY || "AIzaSyAiwMHCsSrqX20x8o90R5hksRxUPVmnwKM",
  authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN || "hthree-94a3e.firebaseapp.com",
  projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID || "hthree-94a3e",
  storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET || "hthree-94a3e.firebasestorage.app",
  messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID || "917487991713",
  appId: import.meta.env.VITE_FIREBASE_APP_ID || "1:917487991713:web:68dd73293f38d7fe2229e0",
  measurementId: import.meta.env.VITE_FIREBASE_MEASUREMENT_ID || "G-9LNBDMVK0M"
};
// Khởi tạo Firebase
const appFirebase = initializeApp(firebaseConfig);
const auth = getAuth(appFirebase); // Lấy instance auth

// Set persistence để lưu session đăng nhập
setPersistence(auth, browserLocalPersistence).catch((error) => {
  console.error('Error setting persistence:', error);
});

// Tạo ứng dụng Vue
const app = createApp(App);

// Sử dụng router và pinia
const pinia = createPinia();
app.use(router);
app.use(pinia);

// Khởi tạo authStore
import { useAuthStore } from './stores/authStore';
const authStore = useAuthStore(pinia);
authStore.initAuth();

// --- Middleware xác thực (router.beforeEach) với Firebase Auth ---
router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
  // Sử dụng auth.currentUser để kiểm tra trạng thái người dùng Firebase
  const user = auth.currentUser; 

  if (requiresAuth && !user) {
    console.log('Chưa đăng nhập (Firebase), chuyển hướng về /auth');
    next('/auth'); // Chuyển hướng về trang đăng nhập nếu chưa đăng nhập
  } else {
    next();
  }
});

// --- Theo dõi trạng thái đăng nhập Firebase và truyền vào ứng dụng ---
// Biến này sẽ được cập nhật khi trạng thái Firebase Auth thay đổi
let isAuthenticated = false; 

// onAuthStateChanged của Firebase sẽ theo dõi trạng thái đăng nhập
onAuthStateChanged(auth, (user) => {
  isAuthenticated = !!user; // Cập nhật trạng thái xác thực
  // Truyền trạng thái vào global property để các component Vue có thể sử dụng
  app.config.globalProperties.$isAuthenticated = isAuthenticated; 

  console.log('Firebase Auth state changed. isAuthenticated:', isAuthenticated);

  // Điều hướng nếu đã đăng nhập và đang ở trang /auth
  // Cần cẩn thận với `to.path` và `next` ở đây, vì `onAuthStateChanged` có thể fire
  // độc lập với router navigation. Logic này nên được xử lý trong router.beforeEach
  // hoặc trong component Auth/Account.
  // Dòng này có thể gây lỗi nếu 'to' và 'next' không được định nghĩa trong phạm vi này.
  // if (user && to.path === '/auth') { 
  //   next('/account'); 
  // }
  // Tôi tạm thời bình luận hoặc bỏ qua logic điều hướng này trong onAuthStateChanged
  // để tránh lỗi, và khuyến nghị xử lý nó trong router.beforeEach hoặc trong component Auth.
  // Hoặc bạn có thể sử dụng một biến cờ để chỉ điều hướng khi onAuthStateChanged fire lần đầu.
});

// Mount ứng dụng vào phần tử #app
app.mount('#app');

// Debug: Để kiểm tra xem Supabase client đã được import và khởi tạo đúng cách chưa
// (Tuy nhiên, đối tượng 'supabase' không được định nghĩa ở đây, nó nằm trong supabaseClient.js)
// Bạn sẽ kiểm tra nó trong các component sử dụng nó.