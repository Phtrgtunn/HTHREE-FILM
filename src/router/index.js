// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';

// Import các component View (trang)
import WelcomePage from '../pages/WelcomePage.vue';
import Homepage from '../pages/Homepage.vue'; // Trang chủ chính
import NetflixHomepage from '../pages/NetflixHomepage.vue'; // Netflix style (backup)
import MovieDetail from '../pages/MovieDetail.vue';
import WatchMovie from '../pages/WatchMovie.vue';
import ListBaseType from '../pages/ListBaseType.vue';
import ListBaseCategory from '../pages/ListBaseCategory.vue';
import ListBaseNational from '../pages/ListBaseNational.vue';
import Contact from '../pages/Contact.vue';
import Account from '../pages/Account.vue';
import DemoUI from '../pages/DemoUI.vue';
import TestImages from '../pages/TestImages.vue';
import SearchResults from '../pages/SearchResults.vue';
import Pricing from '../pages/Pricing.vue';
import Cart from '../pages/Cart.vue';
import Checkout from '../pages/Checkout.vue';
import Admin from '../pages/Admin.vue';

const routes = [
  { path: '/', name: 'Welcome', component: WelcomePage },
  { path: '/home', name: 'Homepage', component: Homepage }, // Trang chủ chính
  { path: '/pricing', name: 'Pricing', component: Pricing }, // Trang giá gói
  { path: '/cart', name: 'Cart', component: Cart }, // Giỏ hàng
  { path: '/checkout', name: 'Checkout', component: Checkout }, // Thanh toán
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

export default router;
