# 📚 THƯ VIỆN CỦA TÔI - HƯỚNG DẪN

## 🎯 Tổng quan

"Thư viện của tôi" là tính năng gom nhóm 3 chức năng:

1. **Yêu thích** ❤️ - Phim bạn thích
2. **Danh sách** 📋 - Phim muốn xem sau
3. **Xem tiếp** ⏱️ - Phim đang xem dở

## 🚀 Cách hoạt động

### 1. Truy cập

**Từ Navbar:**

- Click vào avatar/user icon
- Chọn "📚 Thư viện của tôi"
- Hoặc truy cập trực tiếp: `/library`

### 2. Giao diện

```
┌─────────────────────────────────────────┐
│  Thư viện của tôi                       │
│  Quản lý phim yêu thích, danh sách...   │
├─────────────────────────────────────────┤
│  [❤️ Yêu thích 5] [📋 Danh sách 3] [⏱️ Xem tiếp 2] │
├─────────────────────────────────────────┤
│  ┌───┐ ┌───┐ ┌───┐ ┌───┐ ┌───┐        │
│  │   │ │   │ │   │ │   │ │   │        │
│  │ 🎬│ │ 🎬│ │ 🎬│ │ 🎬│ │ 🎬│        │
│  │   │ │   │ │   │ │   │ │   │        │
│  └───┘ └───┘ └───┘ └───┘ └───┘        │
└─────────────────────────────────────────┘
```

### 3. Tabs

#### Tab 1: Yêu thích ❤️

**Chức năng:**

- Hiển thị tất cả phim đã thêm vào yêu thích
- Hover vào phim → Hiện nút "Xem ngay" và "Xóa"
- Click "Xem ngay" → Chuyển đến trang xem phim
- Click "Xóa" → Xóa khỏi danh sách yêu thích

**Empty State:**

```
🤍 Chưa có phim yêu thích
Thêm phim vào danh sách yêu thích để xem lại sau
[Khám phá phim →]
```

#### Tab 2: Danh sách 📋

**Chức năng:**

- Hiển thị phim đã thêm vào "Xem sau"
- Tương tự tab Yêu thích
- Có thể tạo nhiều danh sách (playlist) - tính năng mở rộng

**Empty State:**

```
📋 Danh sách trống
Thêm phim vào danh sách để xem sau
[Tìm phim hay →]
```

#### Tab 3: Xem tiếp ⏱️

**Chức năng:**

- Hiển thị phim đang xem dở
- Có progress bar (thanh tiến trình)
- Hiển thị % đã xem
- Click "Xem tiếp" → Tiếp tục từ vị trí đã dừng

**Progress Bar:**

```
┌─────────────────┐
│     Poster      │
│                 │
│                 │
└─────────────────┘
▓▓▓▓▓▓▓▓░░░░░░░░  65% đã xem
```

**Empty State:**

```
⏱️ Chưa có lịch sử xem
Bắt đầu xem phim để theo dõi tiến trình
[Bắt đầu xem ▶️]
```

## 💾 Lưu trữ dữ liệu

### Hiện tại (Mock data):

```javascript
const favorites = ref([]);
const watchlist = ref([]);
const continueWatching = ref([]);
```

### Tương lai (API):

```javascript
// Favorites API
GET /api/user/favorites
POST /api/user/favorites/{movieId}
DELETE /api/user/favorites/{movieId}

// Watchlist API
GET /api/user/watchlist
POST /api/user/watchlist/{movieId}
DELETE /api/user/watchlist/{movieId}

// Continue Watching API
GET /api/user/continue-watching
POST /api/user/continue-watching/{movieId}
  - body: { progress: 65, timestamp: 1234567890 }
DELETE /api/user/continue-watching/{movieId}
```

## 🎨 UI/UX Features

### 1. Tabs với Badge Count

```html
❤️ Yêu thích [5] ← Badge hiện số lượng 📋 Danh sách [3] ⏱️ Xem tiếp [2]
```

### 2. Active Tab Indicator

- Tab đang chọn: Màu vàng + underline
- Tab khác: Màu xám + hover effect

### 3. Movie Card Hover

```
Normal:
┌─────┐
│     │
│ 🎬  │
│     │
└─────┘

Hover:
┌─────┐
│ 🎬  │
│Title│
│[Xem]│
│ [X] │
└─────┘
```

### 4. Progress Bar (Xem tiếp)

```css
.progress-bar {
  height: 4px;
  background: gray;
}

.progress-fill {
  height: 100%;
  background: yellow;
  width: 65%; /* Dynamic */
}
```

### 5. Empty States

- Icon lớn (24x24)
- Tiêu đề rõ ràng
- Mô tả ngắn gọn
- CTA button nổi bật

## 🔧 Tích hợp với các trang khác

### MovieDetail.vue

Thêm 3 nút:

```html
<button @click="addToFavorites">❤️ Yêu thích</button>

<button @click="addToWatchlist">➕ Thêm vào danh sách</button>

<button @click="removeFromContinue">✓ Đã xem</button>
```

### WatchMovie.vue

Tự động lưu progress:

```javascript
// Mỗi 10 giây
setInterval(() => {
  saveContinueWatching({
    movieId: movie.id,
    progress: (currentTime / duration) * 100,
    timestamp: Date.now(),
  });
}, 10000);
```

## 📊 Dữ liệu mẫu

```javascript
// Favorites
{
  id: 1,
  title: "Avengers: Endgame",
  poster: "https://...",
  addedAt: "2024-12-04"
}

// Watchlist
{
  id: 2,
  title: "Inception",
  poster: "https://...",
  addedAt: "2024-12-03"
}

// Continue Watching
{
  id: 3,
  title: "The Dark Knight",
  poster: "https://...",
  progress: 65, // %
  lastWatched: "2024-12-04",
  timestamp: 1234567890 // seconds
}
```

## 🎯 Lợi ích UX

### 1. Giảm Cognitive Load

- Gom 3 tính năng → 1 trang
- Dễ tìm, dễ quản lý

### 2. Áp dụng Hick's Law

- Từ 5 menu items → 3 items
- Giảm thời gian quyết định 23%

### 3. Self-explanatory

- Icons rõ ràng (❤️📋⏱️)
- Badge count hiển thị số lượng
- Empty states hướng dẫn rõ ràng

### 4. Progressive Disclosure

- Chỉ hiện tab đang chọn
- Không overwhelm người dùng

## 🚀 Roadmap

### Phase 1 (Hiện tại):

- ✅ UI/UX hoàn chỉnh
- ✅ 3 tabs với empty states
- ✅ Responsive design
- ⏳ Mock data

### Phase 2 (Tiếp theo):

- [ ] Tích hợp API backend
- [ ] LocalStorage fallback
- [ ] Sync với database
- [ ] Real-time updates

### Phase 3 (Tương lai):

- [ ] Multiple playlists
- [ ] Share watchlist
- [ ] Collaborative lists
- [ ] Export/Import

## 💡 Cách sử dụng cho Developer

### 1. Thêm phim vào Favorites:

```javascript
import { useLibraryStore } from "@/stores/libraryStore";

const library = useLibraryStore();

library.addToFavorites({
  id: movie.id,
  title: movie.name,
  poster: movie.poster_url,
});
```

### 2. Thêm vào Watchlist:

```javascript
library.addToWatchlist({
  id: movie.id,
  title: movie.name,
  poster: movie.poster_url,
});
```

### 3. Lưu Continue Watching:

```javascript
library.saveContinueWatching({
  id: movie.id,
  title: movie.name,
  poster: movie.poster_url,
  progress: 65, // %
  timestamp: currentTime, // seconds
});
```

### 4. Xóa khỏi Library:

```javascript
library.removeFromFavorites(movieId);
library.removeFromWatchlist(movieId);
library.removeFromContinue(movieId);
```

## 📝 Tóm tắt

**"Thư viện của tôi"** là trang tổng hợp 3 tính năng:

1. ❤️ **Yêu thích** - Phim bạn thích
2. 📋 **Danh sách** - Phim xem sau
3. ⏱️ **Xem tiếp** - Phim đang xem dở

**Truy cập:** Navbar → User Menu → "📚 Thư viện của tôi"

**URL:** `/library`

**Lợi ích:**

- Giảm menu items từ 5 → 3
- Gom nhóm chức năng liên quan
- Dễ quản lý, dễ sử dụng
- Áp dụng Hick's Law và KISS

---

**Tác giả:** Kiro AI  
**Ngày:** 04/12/2024  
**File:** `src/pages/Library.vue`
