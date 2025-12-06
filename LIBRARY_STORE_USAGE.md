# 📚 HƯỚNG DẪN SỬ DỤNG LIBRARY STORE

## 🎯 Tổng quan

LibraryStore quản lý 3 loại dữ liệu:

1. **Favorites** (Yêu thích)
2. **Watchlist** (Danh sách xem sau)
3. **Continue Watching** (Xem tiếp)

**Lưu trữ:** LocalStorage (hiện tại) → API (tương lai)

---

## 🚀 CÁCH SỬ DỤNG

### 1. Import Store

```javascript
import { useLibraryStore } from "@/stores/libraryStore";

const libraryStore = useLibraryStore();
```

---

## ❤️ FAVORITES (YÊU THÍCH)

### Kiểm tra phim có trong favorites không

```javascript
const isFav = libraryStore.isFavorite(movie.slug);
// Returns: true/false
```

### Thêm vào favorites

```javascript
const success = await libraryStore.addToFavorites({
  id: movie.slug,
  slug: movie.slug,
  name: movie.name,
  poster_url: movie.poster_url,
  year: movie.year,
  quality: movie.quality,
});

if (success) {
  toast.success("✅ Đã thêm vào yêu thích");
}
```

### Xóa khỏi favorites

```javascript
const success = await libraryStore.removeFromFavorites(movie.slug);

if (success) {
  toast.success("✅ Đã xóa khỏi yêu thích");
}
```

### Toggle favorite (thêm/xóa tự động)

```javascript
const success = await libraryStore.toggleFavorite(movie);

// Nếu đã có → Xóa
// Nếu chưa có → Thêm
```

### Lấy danh sách favorites

```javascript
const favorites = libraryStore.favorites;
// Returns: Array of movies

const count = libraryStore.favoritesCount;
// Returns: Number
```

---

## 📋 WATCHLIST (DANH SÁCH XEM SAU)

### Kiểm tra phim có trong watchlist không

```javascript
const isInList = libraryStore.isInWatchlist(movie.slug);
// Returns: true/false
```

### Thêm vào watchlist

```javascript
const success = await libraryStore.addToWatchlist({
  id: movie.slug,
  slug: movie.slug,
  name: movie.name,
  poster_url: movie.poster_url,
  year: movie.year,
  quality: movie.quality,
});

if (success) {
  toast.success("✅ Đã thêm vào danh sách");
}
```

### Xóa khỏi watchlist

```javascript
const success = await libraryStore.removeFromWatchlist(movie.slug);

if (success) {
  toast.success("✅ Đã xóa khỏi danh sách");
}
```

### Toggle watchlist

```javascript
const success = await libraryStore.toggleWatchlist(movie);
```

### Lấy danh sách watchlist

```javascript
const watchlist = libraryStore.watchlist;
const count = libraryStore.watchlistCount;
```

---

## ⏱️ CONTINUE WATCHING (XEM TIẾP)

### Lưu tiến trình xem

```javascript
const success = await libraryStore.saveContinueWatching(
  movie, // Movie object
  65, // Progress % (0-100)
  1234 // Current time in seconds (optional)
);

if (success) {
  console.log("Progress saved");
}
```

**Ví dụ trong video player:**

```javascript
// Mỗi 10 giây
setInterval(() => {
  const progress = (currentTime / duration) * 100;

  libraryStore.saveContinueWatching(movie, progress, currentTime);
}, 10000);
```

### Lấy tiến trình đã xem

```javascript
const data = libraryStore.getContinueProgress(movie.slug);

if (data) {
  console.log("Progress:", data.progress + "%");
  console.log("Current time:", data.currentTime + "s");

  // Seek to saved position
  videoPlayer.currentTime = data.currentTime;
}
```

### Kiểm tra phim có trong continue watching không

```javascript
const isInContinue = libraryStore.isInContinue(movie.slug);
```

### Xóa khỏi continue watching

```javascript
const success = await libraryStore.removeFromContinue(movie.slug);
```

### Lấy danh sách continue watching

```javascript
const continueWatching = libraryStore.continueWatching;
const count = libraryStore.continueWatchingCount;
```

---

## 🔧 TÍCH HỢP VÀO MOVIEDETAIL.VUE

```vue
<template>
  <div>
    <!-- Favorite Button -->
    <button
      @click="toggleFavorite"
      :class="isFavorite ? 'bg-red-500' : 'bg-gray-700'"
    >
      <svg>❤️</svg>
      {{ isFavorite ? "Đã thích" : "Yêu thích" }}
    </button>

    <!-- Watchlist Button -->
    <button
      @click="toggleWatchlist"
      :class="isInWatchlist ? 'bg-yellow-500' : 'bg-gray-700'"
    >
      <svg>➕</svg>
      {{ isInWatchlist ? "Đã thêm" : "Thêm vào danh sách" }}
    </button>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useLibraryStore } from "@/stores/libraryStore";
import { useToast } from "@/composables/useToast";

const props = defineProps(["movie"]);
const libraryStore = useLibraryStore();
const toast = useToast();

// Check status
const isFavorite = computed(() => libraryStore.isFavorite(props.movie.slug));

const isInWatchlist = computed(() =>
  libraryStore.isInWatchlist(props.movie.slug)
);

// Toggle functions
const toggleFavorite = async () => {
  const success = await libraryStore.toggleFavorite(props.movie);

  if (success) {
    if (isFavorite.value) {
      toast.success("✅ Đã thêm vào yêu thích");
    } else {
      toast.success("✅ Đã xóa khỏi yêu thích");
    }
  }
};

const toggleWatchlist = async () => {
  const success = await libraryStore.toggleWatchlist(props.movie);

  if (success) {
    if (isInWatchlist.value) {
      toast.success("✅ Đã thêm vào danh sách");
    } else {
      toast.success("✅ Đã xóa khỏi danh sách");
    }
  }
};
</script>
```

---

## 🎬 TÍCH HỢP VÀO WATCHMOVIE.VUE

```vue
<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useLibraryStore } from "@/stores/libraryStore";

const props = defineProps(["movie"]);
const libraryStore = useLibraryStore();

const videoPlayer = ref(null);
let saveInterval = null;

// Load saved progress
onMounted(() => {
  const saved = libraryStore.getContinueProgress(props.movie.slug);

  if (saved && saved.currentTime > 0) {
    // Ask user if they want to continue
    const resume = confirm(
      `Bạn đã xem ${saved.progress}% phim này. Tiếp tục xem?`
    );

    if (resume && videoPlayer.value) {
      videoPlayer.value.currentTime = saved.currentTime;
    }
  }

  // Auto-save every 10 seconds
  saveInterval = setInterval(() => {
    if (videoPlayer.value) {
      const currentTime = videoPlayer.value.currentTime;
      const duration = videoPlayer.value.duration;
      const progress = (currentTime / duration) * 100;

      libraryStore.saveContinueWatching(props.movie, progress, currentTime);
    }
  }, 10000);
});

// Cleanup
onUnmounted(() => {
  if (saveInterval) {
    clearInterval(saveInterval);
  }

  // Save one last time
  if (videoPlayer.value) {
    const currentTime = videoPlayer.value.currentTime;
    const duration = videoPlayer.value.duration;
    const progress = (currentTime / duration) * 100;

    libraryStore.saveContinueWatching(props.movie, progress, currentTime);
  }
});
</script>
```

---

## 🔄 LOAD DATA

### Load tất cả data khi app khởi động

```javascript
// In App.vue or main component
import { useLibraryStore } from "@/stores/libraryStore";

const libraryStore = useLibraryStore();

onMounted(async () => {
  await libraryStore.loadAll();
});
```

---

## 🗑️ CLEAR ALL DATA

```javascript
libraryStore.clearAll();
// Xóa tất cả favorites, watchlist, continue watching
```

---

## 💾 LƯU TRỮ

### Hiện tại: LocalStorage

```javascript
// Keys
"library_favorites";
"library_watchlist";
"library_continue";
```

### Tương lai: API

```javascript
// Uncomment trong store khi có API

// GET /api/library/all
// POST /api/library/favorites
// DELETE /api/library/favorites/:id
// POST /api/library/watchlist
// DELETE /api/library/watchlist/:id
// POST /api/library/continue
// DELETE /api/library/continue/:id
```

---

## 📊 DATA STRUCTURE

### Favorite/Watchlist Item

```javascript
{
  id: "phim-avengers-endgame",
  slug: "phim-avengers-endgame",
  title: "Avengers: Endgame",
  poster: "https://...",
  year: 2019,
  quality: "HD",
  addedAt: "2024-12-04T10:30:00.000Z"
}
```

### Continue Watching Item

```javascript
{
  id: "phim-inception",
  slug: "phim-inception",
  title: "Inception",
  poster: "https://...",
  year: 2010,
  quality: "HD",
  progress: 65,              // %
  currentTime: 3600,         // seconds
  lastWatched: "2024-12-04T10:30:00.000Z"
}
```

---

## 🎯 BEST PRACTICES

### 1. Always check before adding

```javascript
if (!libraryStore.isFavorite(movie.slug)) {
  await libraryStore.addToFavorites(movie);
}
```

### 2. Use toggle for better UX

```javascript
// Better than add/remove separately
await libraryStore.toggleFavorite(movie);
```

### 3. Show feedback to user

```javascript
const success = await libraryStore.addToFavorites(movie);

if (success) {
  toast.success("✅ Đã thêm vào yêu thích");
} else {
  toast.error("❌ Không thể thêm");
}
```

### 4. Save progress frequently

```javascript
// Every 10 seconds is good
setInterval(() => {
  libraryStore.saveContinueWatching(movie, progress, currentTime);
}, 10000);
```

### 5. Load data on app start

```javascript
// In App.vue
onMounted(async () => {
  await libraryStore.loadAll();
});
```

---

## 🐛 DEBUGGING

### Check localStorage

```javascript
// In browser console
localStorage.getItem("library_favorites");
localStorage.getItem("library_watchlist");
localStorage.getItem("library_continue");
```

### Check store state

```javascript
console.log("Favorites:", libraryStore.favorites);
console.log("Watchlist:", libraryStore.watchlist);
console.log("Continue:", libraryStore.continueWatching);
```

### Clear all data

```javascript
libraryStore.clearAll();
// Or manually
localStorage.clear();
```

---

## ✅ CHECKLIST TÍCH HỢP

- [ ] Import LibraryStore vào component
- [ ] Load data khi app khởi động
- [ ] Thêm buttons vào MovieDetail
- [ ] Tích hợp vào video player
- [ ] Test add/remove functions
- [ ] Test progress saving
- [ ] Test localStorage persistence
- [ ] Add toast notifications
- [ ] Add confirm dialogs
- [ ] Test responsive UI

---

**Tác giả:** Kiro AI  
**Ngày:** 04/12/2024  
**File:** `src/stores/libraryStore.js`
