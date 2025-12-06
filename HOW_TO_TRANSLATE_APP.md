# 🌐 Hướng Dẫn Translate Toàn Bộ App

## ✅ Tình Trạng Hiện Tại

**Language Switcher:** ✅ HOẠT ĐỘNG

- Click nút → Ngôn ngữ chuyển đổi (vi ↔ en)
- Console logs: ✅ Hiển thị đúng
- localStorage: ✅ Lưu đúng
- Nút hiển thị: VI hoặc EN

**Vấn đề:** UI text vẫn hard-coded bằng Tiếng Việt

---

## 🔧 Cách Translate UI Text

### Bước 1: Thay Hard-coded Text bằng `$t()`

**Trước (Hard-coded):**

```vue
<button>Xem Ngay</button>
<h1>Phim Mới Cập Nhật</h1>
<p>Tìm kiếm phim, diễn viên</p>
```

**Sau (Translated):**

```vue
<button>{{ $t('movie.play') }}</button>
<h1>{{ $t('common.newMovies') }}</h1>
<p>{{ $t('nav.search') }}</p>
```

---

## 📝 Translation Files Đã Có

### `src/i18n/locales/vi.json`

```json
{
  "common": {
    "home": "Trang chủ",
    "movies": "Phim",
    "search": "Tìm kiếm"
  },
  "movie": {
    "play": "Xem ngay",
    "addToList": "Thêm vào danh sách"
  },
  "nav": {
    "search": "Tìm kiếm phim, diễn viên"
  }
}
```

### `src/i18n/locales/en.json`

```json
{
  "common": {
    "home": "Home",
    "movies": "Movies",
    "search": "Search"
  },
  "movie": {
    "play": "Play now",
    "addToList": "Add to list"
  },
  "nav": {
    "search": "Search movies, actors"
  }
}
```

---

## 🎯 Ví Dụ Translate Components

### 1. Homepage.vue

**Trước:**

```vue
<h1>🆕 Phim Mới Cập Nhật</h1>
<button>Xem Ngay</button>
```

**Sau:**

```vue
<h1>🆕 {{ $t('common.newMovies') }}</h1>
<button>{{ $t('movie.play') }}</button>
```

**Thêm vào translation files:**

```json
// vi.json
{
  "common": {
    "newMovies": "Phim Mới Cập Nhật"
  }
}

// en.json
{
  "common": {
    "newMovies": "New Movies"
  }
}
```

---

### 2. NetflixNavbar.vue

**Trước:**

```vue
<input placeholder="Tìm kiếm phim, diễn viên" />
<span>Phim Lẻ</span>
<span>Phim Bộ</span>
```

**Sau:**

```vue
<input :placeholder="$t('nav.search')" />
<span>{{ $t('nav.single') }}</span>
<span>{{ $t('nav.series') }}</span>
```

**Thêm vào translation files:**

```json
// vi.json
{
  "nav": {
    "search": "Tìm kiếm phim, diễn viên",
    "single": "Phim Lẻ",
    "series": "Phim Bộ"
  }
}

// en.json
{
  "nav": {
    "search": "Search movies, actors",
    "single": "Movies",
    "series": "TV Series"
  }
}
```

---

### 3. Pricing.vue

**Trước:**

```vue
<h1>Bảng Giá Gói Dịch Vụ</h1>
<button>Chọn gói</button>
<span>Tháng</span>
```

**Sau:**

```vue
<h1>{{ $t('pricing.title') }}</h1>
<button>{{ $t('pricing.selectPlan') }}</button>
<span>{{ $t('pricing.monthly') }}</span>
```

---

## 🚀 Quick Start - Translate 5 Components Quan Trọng

### 1. NetflixNavbar.vue

```vue
<!-- Search placeholder -->
<input :placeholder="$t('nav.search')" />

<!-- Menu items -->
<span>{{ $t('nav.home') }}</span>
<span>{{ $t('nav.single') }}</span>
<span>{{ $t('nav.series') }}</span>
<span>{{ $t('nav.anime') }}</span>
```

### 2. Homepage.vue

```vue
<!-- Section titles -->
<h2>{{ $t('common.newMovies') }}</h2>
<h2>{{ $t('common.series') }}</h2>
<h2>{{ $t('common.single') }}</h2>

<!-- Buttons -->
<button>{{ $t('movie.play') }}</button>
<button>{{ $t('movie.addToList') }}</button>
```

### 3. MovieDetail.vue

```vue
<!-- Buttons -->
<button>{{ $t('movie.play') }}</button>
<button>{{ $t('movie.trailer') }}</button>
<button>{{ $t('movie.info') }}</button>

<!-- Labels -->
<span>{{ $t('movie.quality') }}</span>
<span>{{ $t('movie.language') }}</span>
<span>{{ $t('movie.year') }}</span>
```

### 4. Pricing.vue

```vue
<!-- Title -->
<h1>{{ $t('pricing.title') }}</h1>

<!-- Plans -->
<span>{{ $t('pricing.free') }}</span>
<span>{{ $t('pricing.basic') }}</span>
<span>{{ $t('pricing.premium') }}</span>

<!-- Buttons -->
<button>{{ $t('pricing.selectPlan') }}</button>
```

### 5. Library.vue

```vue
<!-- Tabs -->
<button>{{ $t('library.favorites') }}</button>
<button>{{ $t('library.watchLater') }}</button>
<button>{{ $t('library.history') }}</button>

<!-- Empty state -->
<p>{{ $t('library.empty') }}</p>
```

---

## 📦 Complete Translation Files

Mình đã tạo sẵn translation files đầy đủ:

- ✅ `src/i18n/locales/vi.json` - 100+ keys
- ✅ `src/i18n/locales/en.json` - 100+ keys

Bạn chỉ cần thay hard-coded text bằng `$t('key')`.

---

## 🎨 Demo Component - Test Translation

Tạo file `src/components/LanguageDemo.vue`:

```vue
<template>
  <div class="p-4 bg-gray-800 rounded-lg">
    <h2 class="text-xl font-bold mb-2">{{ $t("common.home") }}</h2>
    <p>{{ $t("nav.search") }}</p>
    <button class="bg-yellow-400 text-black px-4 py-2 rounded mt-2">
      {{ $t("movie.play") }}
    </button>
  </div>
</template>

<script setup>
// No imports needed, $t is global
</script>
```

Thêm vào Homepage để test:

```vue
<LanguageDemo />
```

---

## ✅ Checklist - Translate App

### Phase 1 - Critical Components (30 phút)

- [ ] NetflixNavbar.vue - Menu items, search placeholder
- [ ] Homepage.vue - Section titles, buttons
- [ ] MovieDetail.vue - Buttons, labels
- [ ] Pricing.vue - Plans, buttons
- [ ] Library.vue - Tabs, empty state

### Phase 2 - Secondary Components (1 giờ)

- [ ] FooterComponent.vue - Links, copyright
- [ ] AuthModal.vue - Form labels, buttons
- [ ] CommentForm.vue - Placeholder, button
- [ ] MovieCard.vue - Badges, tooltips
- [ ] SearchResults.vue - Title, empty state

### Phase 3 - All Components (2 giờ)

- [ ] Tất cả components còn lại
- [ ] Error messages
- [ ] Toast notifications
- [ ] Confirmation dialogs

---

## 🔍 Tìm Hard-coded Text

### Method 1: Search trong VSCode

```
Search: "Xem ngay|Phim mới|Tìm kiếm"
Files to include: src/**/*.vue
```

### Method 2: Grep command

```bash
grep -r "Xem ngay\|Phim mới\|Tìm kiếm" src/
```

---

## 🎯 Kết Quả Mong Đợi

Sau khi translate xong:

**Click nút VI:**

```
🏠 Trang chủ
🎬 Phim Lẻ
📺 Phim Bộ
🔍 Tìm kiếm phim, diễn viên
▶️ Xem Ngay
```

**Click nút EN:**

```
🏠 Home
🎬 Movies
📺 TV Series
🔍 Search movies, actors
▶️ Play Now
```

---

## 💡 Tips

### 1. Organize Translation Keys

```json
{
  "common": {}, // Shared text
  "nav": {}, // Navigation
  "movie": {}, // Movie related
  "auth": {}, // Authentication
  "pricing": {}, // Pricing page
  "library": {}, // Library page
  "errors": {} // Error messages
}
```

### 2. Use Interpolation

```vue
<!-- With variables -->
<p>{{ $t('movie.episodeCount', { count: 10 }) }}</p>

<!-- Translation file -->
{ "movie": { "episodeCount": "{count} tập" // vi "episodeCount": "{count}
episodes" // en } }
```

### 3. Pluralization

```json
{
  "movie": {
    "episodes": "không có tập | 1 tập | {count} tập"
  }
}
```

---

## 🚀 Quick Command

Để translate nhanh 5 components quan trọng nhất:

```bash
# 1. NetflixNavbar.vue
# 2. Homepage.vue
# 3. MovieDetail.vue
# 4. Pricing.vue
# 5. Library.vue
```

Mỗi component ~10 phút = 50 phút total.

---

## ✅ Verification

Sau khi translate xong, test:

1. Click nút VI → Tất cả text tiếng Việt
2. Click nút EN → Tất cả text tiếng Anh
3. Refresh page → Ngôn ngữ được giữ nguyên
4. Check localStorage → `locale: "vi"` hoặc `"en"`

---

**Tóm lại:** Language switcher ĐÃ HOẠT ĐỘNG! Chỉ cần thay hard-coded text bằng `$t()` là xong! 🎉
