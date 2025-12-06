# 🎬 HTHREE Film - Movie Streaming Platform

A modern movie streaming website built with Vue.js 3, featuring a Netflix-inspired UI with smooth animations and real-time comments.

![Vue.js](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=flat&logo=vue.js)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-4.1-38B2AC?style=flat&logo=tailwind-css)
![Firebase](https://img.shields.io/badge/Firebase-11.9-FFCA28?style=flat&logo=firebase)
![License](https://img.shields.io/badge/License-MIT-blue.svg)

## ✨ Features

### 🎥 Movie Features

- **Browse Movies**: Phim bộ, phim lẻ, hoạt hình, TV shows
- **Search**: Tìm kiếm phim theo tên, thể loại, quốc gia
- **Filter**: Lọc theo năm, chất lượng, ngôn ngữ
- **Watch Online**: Xem phim trực tuyến với nhiều server
- **Episode Navigation**: Chuyển tập dễ dàng cho phim bộ
- **Movie Details**: Thông tin chi tiết, trailer, diễn viên
- **Library Management**: Yêu thích, xem sau, lịch sử
- **Bulk Actions**: Chọn nhiều phim cùng lúc

### 🎨 UI/UX

- **Netflix-style Design**: Giao diện hiện đại, mượt mà
- **Responsive**: Tối ưu cho mobile, tablet, desktop
- **Smooth Animations**: Fade-in, slide-up, hover effects
- **Dark Theme**: Giao diện tối dễ nhìn
- **Hero Banner**: Backdrop image với gradient overlay
- **Keyboard Shortcuts**: Ctrl+K, /, ?, G+H/L/P/C
- **Command Palette**: Quick navigation và actions
- **Accessibility**: WCAG 2.1 AA compliant

### 💬 Social Features

- **Comments System**: Bình luận theo phim
- **User Authentication**: Đăng nhập với Firebase
- **User Profiles**: Quản lý tài khoản, avatar
- **Community**: Top commenters, trending movies

### 🚀 Performance & SEO

- **Fast Loading**: Lazy loading, code splitting
- **API Caching**: Stale-while-revalidate strategy
- **Optimized Images**: CDN, lazy load, WebP
- **SEO Optimized**: Dynamic meta tags, Open Graph, Twitter Card
- **Structured Data**: JSON-LD schema.org markup
- **Sitemap & Robots.txt**: Search engine friendly

### 🌐 Internationalization

- **Multi-language**: Tiếng Việt, English
- **Language Switcher**: Easy language switching
- **RTL Support**: Ready for RTL languages

### 📊 Analytics & Monitoring

- **Google Analytics 4**: Page views, events, conversions
- **Error Tracking**: Comprehensive error logging
- **User Behavior**: Track movie plays, searches, purchases
- **Performance Monitoring**: Real-time performance metrics

### 🧪 Testing

- **Unit Tests**: Vitest + Vue Test Utils
- **Component Tests**: Isolated component testing
- **Coverage Reports**: 80%+ test coverage
- **CI/CD Ready**: Automated testing pipeline

## 🛠️ Tech Stack

### Frontend

- **Vue.js 3** - Progressive JavaScript framework
- **Vue Router** - Official router
- **Pinia** - State management
- **Tailwind CSS 4** - Utility-first CSS
- **Axios** - HTTP client
- **Vite** - Build tool

### Backend

- **PHP 8+** - Server-side scripting
- **MySQL** - Database
- **PHPMailer** - Email service

### Services

- **Firebase** - Authentication, hosting
- **Supabase** - Database (optional)
- **PhimAPI** - Movie data API
- **Vercel** - Deployment

## 📦 Installation

### Prerequisites

- Node.js 18+
- npm or yarn
- PHP 8+ (for backend)
- MySQL

### Clone Repository

```bash
git clone https://github.com/YOUR_USERNAME/HTHREE_film.git
cd HTHREE_film
```

### Install Dependencies

```bash
npm install
```

### Environment Variables

Create `.env` file:

```env
VITE_SUPABASE_URL=your_supabase_url
VITE_SUPABASE_KEY=your_supabase_key
VITE_FIREBASE_API_KEY=your_firebase_api_key
VITE_FIREBASE_AUTH_DOMAIN=your_firebase_auth_domain
VITE_FIREBASE_PROJECT_ID=your_firebase_project_id
VITE_FIREBASE_STORAGE_BUCKET=your_firebase_storage_bucket
VITE_FIREBASE_MESSAGING_SENDER_ID=your_firebase_messaging_sender_id
VITE_FIREBASE_APP_ID=your_firebase_app_id
VITE_FIREBASE_MEASUREMENT_ID=your_firebase_measurement_id
```

### Run Development Server

```bash
npm run dev
```

Open http://localhost:5173

### Build for Production

```bash
npm run build
```

### Run Tests

```bash
# Run all tests
npm run test

# Run tests with UI
npm run test:ui

# Run tests with coverage
npm run test:coverage
```

## 🗂️ Project Structure

```
HTHREE_film/
├── src/
│   ├── assets/          # Images, icons
│   ├── components/      # Reusable components
│   │   ├── BannerCarousel.vue
│   │   ├── CommentForm.vue
│   │   ├── MovieDetailModal.vue
│   │   └── NetflixNavbar.vue
│   ├── pages/           # Page components
│   │   ├── Homepage.vue
│   │   ├── MovieDetail.vue
│   │   ├── WatchMovie.vue
│   │   └── SearchResults.vue
│   ├── router/          # Vue Router config
│   ├── services/        # API services
│   ├── stores/          # Pinia stores
│   ├── shared/          # Shared components
│   ├── config/          # Configuration
│   └── main.js          # Entry point
├── backend/             # PHP backend
│   ├── api/             # API endpoints
│   ├── config/          # Config files
│   └── database/        # Database migrations
├── public/              # Static files
└── dist/                # Build output
```

## 🎯 Key Features Implementation

### Movie Detail Page

- Hero section with backdrop image
- Video player integration
- Episode list with thumbnails
- Comments section
- Related movies

### Watch Movie Page

- Full-screen video player
- Episode navigation grid
- Auto-load next episode
- Smooth animations

### Comments System

- Real-time comments
- User avatars
- Like functionality
- Time ago format

## 🚀 Deployment

### Deploy to Vercel

```bash
# Install Vercel CLI
npm install -g vercel

# Login
vercel login

# Deploy
vercel --prod
```

Or connect GitHub repository to Vercel Dashboard.

See [HUONG_DAN_UPLOAD_GIT.md](HUONG_DAN_UPLOAD_GIT.md) for detailed instructions.

## 📝 API Documentation

### Movie API (PhimAPI)

- Base URL: `https://phimapi.com/v1/api`
- Endpoints:
  - `/danh-sach/phim-moi-cap-nhat` - Latest movies
  - `/phim/{slug}` - Movie details
  - `/tim-kiem` - Search movies

### Backend API

- Base URL: `/backend/api`
- Endpoints:
  - `/comments.php` - Comments CRUD
  - `/users.php` - User management
  - `/auth/login.php` - Authentication

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License.

## 👨‍💻 Author

**Your Name**

- GitHub: [@yourusername](https://github.com/yourusername)
- Email: your.email@example.com

## 🙏 Acknowledgments

- [PhimAPI](https://phimapi.com) - Movie data provider
- [Vue.js](https://vuejs.org) - The Progressive JavaScript Framework
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [Firebase](https://firebase.google.com) - Backend services
- [Vercel](https://vercel.com) - Deployment platform

## 📸 Screenshots

### Homepage

![Homepage](screenshots/homepage.png)

### Movie Detail

![Movie Detail](screenshots/movie-detail.png)

### Watch Movie

![Watch Movie](screenshots/watch-movie.png)

---

**⭐ If you like this project, please give it a star!**
