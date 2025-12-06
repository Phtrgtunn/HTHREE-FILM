# HTHREE Film - Developer Guide

## 📚 Mục lục

1. [Giới thiệu](#giới-thiệu)
2. [Cấu trúc dự án](#cấu-trúc-dự-án)
3. [Cài đặt](#cài-đặt)
4. [Development](#development)
5. [Testing](#testing)
6. [SEO](#seo)
7. [Analytics](#analytics)
8. [Internationalization](#internationalization)
9. [Best Practices](#best-practices)

## Giới thiệu

HTHREE Film là nền tảng xem phim online được xây dựng với Vue 3, Vite, và Tailwind CSS.

### Tech Stack

- **Frontend**: Vue 3 (Composition API)
- **Build Tool**: Vite
- **Styling**: Tailwind CSS
- **State Management**: Pinia
- **Routing**: Vue Router
- **Backend**: PHP + MySQL
- **Authentication**: Firebase Auth
- **Database**: Supabase
- **Testing**: Vitest + Vue Test Utils
- **i18n**: Vue I18n

## Cấu trúc dự án

```
HTHREE/
├── src/
│   ├── components/       # Vue components
│   ├── composables/      # Composition API composables
│   ├── pages/           # Page components
│   ├── router/          # Vue Router config
│   ├── stores/          # Pinia stores
│   ├── utils/           # Utility functions
│   ├── i18n/            # Internationalization
│   ├── tests/           # Unit tests
│   └── App.vue          # Root component
├── public/              # Static assets
├── backend/             # PHP backend
└── dist/                # Build output
```

## Cài đặt

### Prerequisites

- Node.js >= 18
- npm hoặc yarn
- PHP >= 7.4
- MySQL

### Installation Steps

```bash
# Clone repository
git clone https://github.com/your-repo/hthree-film.git
cd hthree-film

# Install dependencies
npm install

# Copy environment file
cp .env.example .env

# Configure .env file
# Add your Firebase, Supabase, and API keys

# Start development server
npm run dev
```

## Development

### Running Dev Server

```bash
npm run dev
```

Server sẽ chạy tại `http://localhost:5173`

### Building for Production

```bash
npm run build
```

Output sẽ được tạo trong thư mục `dist/`

### Code Style

- Sử dụng Vue 3 Composition API với `<script setup>`
- Tailwind CSS cho styling
- ESLint + Prettier cho code formatting
- Commit messages theo Conventional Commits

## Testing

### Running Tests

```bash
# Run all tests
npm run test

# Run tests with UI
npm run test:ui

# Run tests with coverage
npm run test:coverage
```

### Writing Tests

```javascript
// Example: Component test
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

### Test Coverage

Mục tiêu: >= 80% coverage cho:

- Composables
- Utility functions
- Critical components

## SEO

### Using useSEO Composable

```javascript
import { useSEO, generatePageMeta } from "@/composables/useSEO";

// In component
const meta = generatePageMeta("home", { path: "/home" });
const { updateMeta, setWebsiteStructuredData } = useSEO(meta);

onMounted(() => {
  setWebsiteStructuredData();
});
```

### SEO Checklist

- ✅ Dynamic meta tags (title, description, keywords)
- ✅ Open Graph tags
- ✅ Twitter Card tags
- ✅ Canonical URLs
- ✅ Structured data (JSON-LD)
- ✅ robots.txt
- ⏳ Sitemap.xml (TODO)

## Analytics

### Setup Google Analytics

```javascript
// In main.js or App.vue
import { initAnalytics } from "@/composables/useAnalytics";

initAnalytics("G-XXXXXXXXXX"); // Your GA4 Measurement ID
```

### Tracking Events

```javascript
import { useAnalytics } from "@/composables/useAnalytics";

const { trackPageView, trackMoviePlay, trackSearch } = useAnalytics();

// Track page view
trackPageView("/home", "Home Page");

// Track movie play
trackMoviePlay(movie);

// Track search
trackSearch("action movies", 15);
```

### Available Tracking Methods

- `trackPageView(path, title)` - Track page views
- `trackEvent(name, params)` - Track custom events
- `trackMoviePlay(movie)` - Track movie plays
- `trackSearch(query, count)` - Track searches
- `trackAddToCart(item)` - Track add to cart
- `trackPurchase(transaction)` - Track purchases
- `trackSignup(method)` - Track signups
- `trackLogin(method)` - Track logins

## Internationalization

### Setup

i18n đã được cấu hình sẵn với 2 ngôn ngữ:

- Tiếng Việt (vi) - Default
- English (en)

### Using i18n in Components

```vue
<template>
  <div>
    <h1>{{ $t("common.home") }}</h1>
    <p>{{ $t("movie.play") }}</p>
  </div>
</template>

<script setup>
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();

// Change language
locale.value = "en";
</script>
```

### Adding New Translations

1. Edit `src/i18n/locales/vi.json`
2. Edit `src/i18n/locales/en.json`
3. Use in component: `{{ $t('your.key') }}`

### Language Switcher Component

```vue
<template>
  <LanguageSwitcher />
</template>

<script setup>
import LanguageSwitcher from "@/components/LanguageSwitcher.vue";
</script>
```

## Best Practices

### Component Structure

```vue
<template>
  <!-- Template -->
</template>

<script setup>
// 1. Imports
import { ref, computed, onMounted } from "vue";

// 2. Props & Emits
const props = defineProps({
  title: String,
});

const emit = defineEmits(["update"]);

// 3. Reactive state
const count = ref(0);

// 4. Computed properties
const doubleCount = computed(() => count.value * 2);

// 5. Methods
const increment = () => {
  count.value++;
};

// 6. Lifecycle hooks
onMounted(() => {
  console.log("Component mounted");
});
</script>

<style scoped>
/* Component styles */
</style>
```

### Composables

```javascript
/**
 * Composable description
 * @param {object} options - Options object
 * @returns {object} Composable methods
 */
export function useMyComposable(options = {}) {
  const state = ref(null);

  const doSomething = () => {
    // Implementation
  };

  return {
    state,
    doSomething,
  };
}
```

### Error Handling

```javascript
import { useErrorTracking } from "@/composables/useErrorTracking";

const { logError } = useErrorTracking();

try {
  // Code that might throw
} catch (error) {
  logError(error, {
    component: "MyComponent",
    action: "fetchData",
  });
}
```

### Performance

- Sử dụng `v-memo` cho lists lớn
- Lazy load components với `defineAsyncComponent`
- Code splitting với dynamic imports
- Optimize images (WebP, lazy loading)
- Cache API responses

### Security

- Sanitize user inputs
- Validate data trước khi gửi API
- Sử dụng HTTPS
- Implement CSRF protection
- Rate limiting cho API calls

## API Documentation

### Movie API

```javascript
import { movieApi } from "@/services/movieApi";

// Get movies
const movies = await movieApi.getMovies({ page: 1, limit: 20 });

// Get movie detail
const movie = await movieApi.getMovieDetail(slug);

// Search movies
const results = await movieApi.searchMovies(query);
```

### E-commerce API

```javascript
import { ecommerceApi } from "@/services/ecommerceApi";

// Get products
const products = await ecommerceApi.getProducts();

// Add to cart
await ecommerceApi.addToCart(productId, quantity);

// Checkout
await ecommerceApi.checkout(orderData);
```

## Deployment

### Vercel

```bash
# Install Vercel CLI
npm i -g vercel

# Deploy
vercel
```

### Manual Deployment

```bash
# Build
npm run build

# Upload dist/ folder to server
```

## Troubleshooting

### Common Issues

**Issue**: Module not found

```bash
# Clear cache and reinstall
rm -rf node_modules package-lock.json
npm install
```

**Issue**: Build fails

```bash
# Check for TypeScript errors
npm run build -- --mode development
```

**Issue**: Tests fail

```bash
# Update snapshots
npm run test -- -u
```

## Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## License

MIT License - see LICENSE file for details

## Support

- Email: support@hthree.com
- Discord: https://discord.gg/hthree
- Documentation: https://docs.hthree.com
