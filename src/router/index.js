// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';

// ⚡ Eager load: Critical pages (loaded immediately)
import WelcomePage from '../pages/WelcomePage.vue';
import Homepage from '../pages/Homepage.vue'; // Trang chủ chính

// 📦 Lazy load: Other pages (loaded on demand for better performance)
const NetflixHomepage = () => import(/* webpackChunkName: "netflix" */ '../pages/NetflixHomepage.vue');
const MovieDetail = () => import(/* webpackChunkName: "movie" */ '../pages/MovieDetail.vue');
const WatchMovie = () => import(/* webpackChunkName: "watch" */ '../pages/WatchMovie.vue');
const ListBaseType = () => import(/* webpackChunkName: "list" */ '../pages/ListBaseType.vue');
const ListBaseCategory = () => import(/* webpackChunkName: "list" */ '../pages/ListBaseCategory.vue');
const ListBaseNational = () => import(/* webpackChunkName: "list" */ '../pages/ListBaseNational.vue');
const Contact = () => import(/* webpackChunkName: "contact" */ '../pages/Contact.vue');
const Account = () => import(/* webpackChunkName: "account" */ '../pages/Account.vue');
const DemoUI = () => import(/* webpackChunkName: "demo" */ '../pages/DemoUI.vue');
const TestImages = () => import(/* webpackChunkName: "test" */ '../pages/TestImages.vue');
const SearchResults = () => import(/* webpackChunkName: "search" */ '../pages/SearchResults.vue');
const Pricing = () => import(/* webpackChunkName: "pricing" */ '../pages/Pricing.vue');
const Cart = () => import(/* webpackChunkName: "cart" */ '../pages/Cart.vue');
const Checkout = () => import(/* webpackChunkName: "checkout" */ '../pages/Checkout.vue');
const PaymentProcessing = () => import(/* webpackChunkName: "payment" */ '../pages/PaymentProcessing.vue');
const Admin = () => import(/* webpackChunkName: "admin" */ '../pages/Admin.vue');
const Categories = () => import(/* webpackChunkName: "categories" */ '../pages/Categories.vue');
const Library = () => import(/* webpackChunkName: "library" */ '../pages/Library.vue');

const routes = [
  { path: '/', name: 'Welcome', component: WelcomePage },
  { path: '/home', name: 'Homepage', component: Homepage }, // Trang chủ chính
  { path: '/categories', name: 'Categories', component: Categories }, // Tất cả thể loại
  { path: '/library', name: 'Library', component: Library }, // Thư viện của tôi
  { path: '/pricing', name: 'Pricing', component: Pricing }, // Trang giá gói
  { path: '/cart', name: 'Cart', component: Cart }, // Giỏ hàng
  { path: '/checkout', name: 'Checkout', component: Checkout }, // Thanh toán
  { path: '/payment-processing', name: 'PaymentProcessing', component: PaymentProcessing }, // Xử lý thanh toán
  { path: '/admin', name: 'Admin', component: Admin }, // Trang quản trị
  { path: '/netflix', name: 'NetflixHome', component: NetflixHomepage }, // Netflix style (backup)
  { path: '/demo', name: 'DemoUI', component: DemoUI }, // Trang demo DaisyUI
  { path: '/test-images', name: 'TestImages', component: TestImages }, // Test images page
  { path: '/film/:filmName', name: 'MovieDetail', component: MovieDetail, props: true },
  { path: '/film/:filmName/tap/:tap', name: 'WatchMovie', component: WatchMovie, props: true },
  { 
    path: '/list/:type/page/:pageNumber', 
    name: 'ListBaseType', 
    component: ListBaseType, 
    props: true 
  },
  { 
    path: '/category/:categorySlug?/page/:pageNumber', // Slug tùy chọn
    name: 'ListBaseCategory', 
    component: ListBaseCategory, 
    props: true 
  },
  { 
    path: '/country/:countrySlug?/page/:pageNumber', // Slug tùy chọn
    name: 'ListBaseNational', 
    component: ListBaseNational, 
    props: true 
  },
  { path: '/contact', name: 'Contact', component: Contact },
  { path: '/account', name: 'Account', component: Account }, // Thêm route cho trang tài khoản
  { path: '/search', name: 'Search', component: SearchResults }, // Trang tìm kiếm
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL), // Đảm bảo tương thích Vite
  routes,
  // Luôn cuộn lên đầu trang khi chuyển route
  scrollBehavior(to, from, savedPosition) {
    return { top: 0, behavior: 'smooth' };
  },
});

// Route guard: Chặn admin truy cập vào trang mua gói
router.beforeEach((to, from, next) => {
  // Các route không cho phép admin truy cập
  const restrictedForAdmin = ['Pricing', 'Cart', 'Checkout'];
  
  if (restrictedForAdmin.includes(to.name)) {
    // Kiểm tra xem user có phải admin không
    const user = JSON.parse(localStorage.getItem('user') || 'null');
    const adminEmails = ['hient7182@gmail.com', 'admin@hthree.com'];
    
    const isAdmin = user && (user.role === 'admin' || adminEmails.includes(user.email));
    
    if (isAdmin) {
      // Nếu là admin, chuyển về trang chủ
      console.log('⛔ Admin không thể truy cập trang:', to.name);
      next({ name: 'Homepage' });
      return;
    }
  }
  
  next();
});

export default router;
