# 🚀 Hướng Dẫn Nhanh - Tính Năng Mới

## 📦 Cài Đặt Dependencies Mới

```bash
npm install
```

Các package mới đã được thêm:

- `vue-i18n` - Đa ngôn ngữ
- `vitest` - Testing framework
- `@vue/test-utils` - Component testing
- `happy-dom` - DOM for testing

---

## 1️⃣ SEO - Tối Ưu Hóa Công Cụ Tìm Kiếm

### Sử dụng trong Pages

```vue
<script setup>
import { useSEO, generatePageMeta } from "@/composables/useSEO";
import { onMounted } from "vue";

// Generate meta tags
const meta = generatePageMeta("home", { path: "/home" });
const { updateMeta, setWebsiteStructuredData } = useSEO(meta);

onMounted(() => {
  setWebsiteStructuredData();
});
</script>
```

### Cho Movie Detail Page

```vue
<script setup>
import { useSEO, generateMovieMeta } from "@/composables/useSEO";

// After loading movie data
const movieMeta = generateMovieMeta(movie);
const { updateMeta, setMovieStructuredData } = useSEO(movieMeta);
updateMeta();
setMovieStructuredData(movie);
</script>
```

### Generate Sitemap

```bash
npm run generate:sitemap
```

File `public/sitemap.xml` sẽ được tạo tự động.

---

## 2️⃣ Analytics - Theo Dõi Người Dùng

### Setup trong App.vue

```vue
<script setup>
import { initAnalytics } from "@/composables/useAnalytics";
import { onMounted } from "vue";

onMounted(() => {
  // Thay YOUR_GA4_ID bằng Google Analytics 4 Measurement ID
  initAnalytics("G-XXXXXXXXXX");
});
</script>
```

### Track Events

```vue
<script setup>
import { useAnalytics } from "@/composables/useAnalytics";

const {
  trackPageView,
  trackMoviePlay,
  trackSearch,
  trackAddToCart,
  trackPurchase,
} = useAnalytics();

// Track page view
trackPageView("/home", "Home Page");

// Track movie play
const playMovie = (movie) => {
  trackMoviePlay(movie);
  // ... play logic
};

// Track search
const search = (query) => {
  trackSearch(query, results.length);
  // ... search logic
};
</script>
```

---

## 3️⃣ Internationalization - Đa Ngôn Ngữ

### Setup trong main.js

```javascript
import { createApp } from "vue";
import App from "./App.vue";
import i18n from "./i18n";

const app = createApp(App);
app.use(i18n);
app.mount("#app");
```

### Sử dụng trong Components

```vue
<template>
  <div>
    <!-- Cách 1: Template syntax -->
    <h1>{{ $t("common.home") }}</h1>
    <p>{{ $t("movie.play") }}</p>

    <!-- Cách 2: With parameters -->
    <p>{{ $t("movie.episodeCount", { count: 10 }) }}</p>
  </div>
</template>

<script setup>
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();

// Change language programmatically
const changeLanguage = (lang) => {
  locale.value = lang; // 'vi' or 'en'
};

// Use in JavaScript
console.log(t("common.home"));
</script>
```

### Thêm Language Switcher

```vue
<template>
  <LanguageSwitcher />
</template>

<script setup>
import LanguageSwitcher from "@/components/LanguageSwitcher.vue";
</script>
```

### Thêm Translations Mới

Edit `src/i18n/locales/vi.json`:

```json
{
  "myFeature": {
    "title": "Tiêu đề",
    "description": "Mô tả"
  }
}
```

Edit `src/i18n/locales/en.json`:

```json
{
  "myFeature": {
    "title": "Title",
    "description": "Description"
  }
}
```

---

## 4️⃣ Testing - Kiểm Thử

### Chạy Tests

```bash
# Run all tests
npm run test

# Run with UI (recommended)
npm run test:ui

# Run with coverage
npm run test:coverage
```

### Viết Test Mới

Tạo file `src/tests/composables/myComposable.test.js`:

```javascript
import { describe, it, expect } from "vitest";
import { myComposable } from "@/composables/myComposable";

describe("myComposable", () => {
  it("should work correctly", () => {
    const { result } = myComposable();
    expect(result.value).toBe("expected");
  });
});
```

### Test Component

```javascript
import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import MyComponent from "@/components/MyComponent.vue";

describe("MyComponent", () => {
  it("renders properly", () => {
    const wrapper = mount(MyComponent, {
      props: { title: "Test" },
    });
    expect(wrapper.text()).toContain("Test");
  });
});
```

---

## 5️⃣ Error Tracking - Theo Dõi Lỗi

### Setup Global Error Handler

Trong `main.js`:

```javascript
import { setupGlobalErrorHandler } from "@/composables/useErrorTracking";

const app = createApp(App);
setupGlobalErrorHandler(app);
```

### Sử dụng trong Components

```vue
<script setup>
import { useErrorTracking } from "@/composables/useErrorTracking";

const { logError, logWarning, logInfo } = useErrorTracking();

const fetchData = async () => {
  try {
    const data = await api.getData();
  } catch (error) {
    logError(error, {
      component: "MyComponent",
      action: "fetchData",
    });
  }
};
</script>
```

---

## 6️⃣ Keyboard Shortcuts - Phím Tắt

### Phím tắt có sẵn:

- `Ctrl+K` - Mở command palette
- `/` - Focus vào search
- `?` - Hiện danh sách shortcuts
- `Esc` - Đóng modals
- `G+H` - Về trang chủ
- `G+L` - Đến thư viện
- `G+P` - Đến bảng giá
- `G+C` - Đến giỏ hàng

### Thêm Shortcuts Mới

```vue
<script setup>
import { useKeyboardShortcuts } from "@/composables/useKeyboardShortcuts";

useKeyboardShortcuts({
  "ctrl+s": (e) => {
    e.preventDefault();
    saveData();
  },
  "ctrl+p": (e) => {
    e.preventDefault();
    print();
  },
});
</script>
```

---

## 7️⃣ API Caching - Cache API

### Sử dụng Cache

```javascript
import { getCachedData, setCachedData } from "@/utils/apiCache";

const fetchMovies = async () => {
  // Check cache first
  const cached = getCachedData("movies-page-1");
  if (cached) {
    return cached;
  }

  // Fetch from API
  const data = await api.getMovies();

  // Cache for 5 minutes
  setCachedData("movies-page-1", data, 5 * 60 * 1000);

  return data;
};
```

---

## 8️⃣ Bulk Actions - Chọn Nhiều

### Sử dụng trong Library

```vue
<script setup>
import { useBulkActions } from "@/composables/useBulkActions";

const { selectedItems, isSelected, toggleItem, selectAll, clearSelection } =
  useBulkActions();

// Toggle item
const handleSelect = (movie) => {
  toggleItem(movie.id);
};

// Delete selected
const deleteSelected = () => {
  selectedItems.value.forEach((id) => {
    deleteMovie(id);
  });
  clearSelection();
};
</script>
```

---

## 9️⃣ Recent Searches - Tìm Kiếm Gần Đây

```vue
<script setup>
import { useRecentSearches } from "@/composables/useRecentSearches";

const { recentSearches, addSearch, removeSearch, clearSearches } =
  useRecentSearches();

const search = (query) => {
  addSearch(query);
  // ... search logic
};
</script>
```

---

## 🔟 Form Auto-save - Tự Động Lưu

```vue
<script setup>
import { useFormAutoSave } from "@/composables/useFormAutoSave";
import { ref } from "vue";

const formData = ref({
  email: "",
  password: "",
});

// Auto-save every 2 seconds
useFormAutoSave("login-form", formData, 2000);
</script>
```

---

## 📚 Tài Liệu Chi Tiết

Xem thêm:

- [DEVELOPER_GUIDE.md](DEVELOPER_GUIDE.md) - Hướng dẫn đầy đủ
- [IMPROVEMENTS_FINAL_SUMMARY.md](IMPROVEMENTS_FINAL_SUMMARY.md) - Tổng kết cải thiện
- [README.md](README.md) - Thông tin dự án

---

## 🆘 Troubleshooting

### Lỗi: Module not found

```bash
rm -rf node_modules package-lock.json
npm install
```

### Lỗi: i18n not working

Kiểm tra `main.js` đã import và use i18n chưa:

```javascript
import i18n from "./i18n";
app.use(i18n);
```

### Lỗi: Tests fail

```bash
# Clear cache
npm run test -- --clearCache

# Update snapshots
npm run test -- -u
```

### Lỗi: Analytics not tracking

Kiểm tra:

1. GA4 Measurement ID đúng chưa
2. Script đã load chưa (check Network tab)
3. Ad blocker có chặn không

---

## ✅ Checklist Trước Khi Deploy

- [ ] Chạy `npm run test` - All tests pass
- [ ] Chạy `npm run build` - Build success
- [ ] Kiểm tra SEO meta tags
- [ ] Test analytics tracking
- [ ] Test đa ngôn ngữ
- [ ] Generate sitemap: `npm run generate:sitemap`
- [ ] Kiểm tra robots.txt
- [ ] Test trên mobile
- [ ] Test keyboard shortcuts
- [ ] Kiểm tra error tracking

---

**Happy Coding! 🎉**
