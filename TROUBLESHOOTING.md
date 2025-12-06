# 🔧 Troubleshooting Guide - HTHREE Film

## 🌐 Network Errors

### ❌ Error: `ERR_CONNECTION_TIMED_OUT`

**Nguyên nhân:**

- Kết nối mạng chậm hoặc không ổn định
- Supabase/API server không phản hồi
- Firewall/Proxy chặn kết nối

**Giải pháp:**

1. **Kiểm tra kết nối mạng:**

```bash
# Ping Google
ping google.com

# Ping Supabase
ping [your-supabase-url]
```

2. **Kiểm tra Supabase status:**

- Truy cập: https://status.supabase.com
- Kiểm tra xem có downtime không

3. **Tăng timeout trong code:**

File đã được cải thiện với retry logic và timeout 10s. Nếu vẫn lỗi, tăng timeout:

```javascript
// src/stores/Category/category.js
const { data, error } = await Promise.race([
  supabase.from("movies").select("genres"),
  new Promise(
    (_, reject) => setTimeout(() => reject(new Error("Request timeout")), 30000) // 30s
  ),
]);
```

4. **Sử dụng fallback data:**

Code đã tự động sử dụng fallback categories khi fetch fail. Kiểm tra console:

```
CategoryStore: Sử dụng fallback genres
```

5. **Kiểm tra .env file:**

```env
VITE_SUPABASE_URL=https://your-project.supabase.co
VITE_SUPABASE_KEY=your-anon-key
```

6. **Restart dev server:**

```bash
# Stop server (Ctrl+C)
# Clear cache
rm -rf node_modules/.vite

# Restart
npm run dev
```

---

## 🧪 Test Errors

### ❌ Error: Tests fail

**Giải pháp:**

```bash
# Clear test cache
npm run test -- --clearCache

# Run tests again
npm run test
```

### ❌ Error: Module not found in tests

**Giải pháp:**

Kiểm tra `vitest.config.js` có alias đúng:

```javascript
resolve: {
  alias: {
    '@': path.resolve(__dirname, './src')
  }
}
```

---

## 🌍 i18n Errors

### ❌ Error: `$t is not a function`

**Nguyên nhân:** i18n chưa được setup trong `main.js`

**Giải pháp:**

```javascript
// src/main.js
import { createApp } from "vue";
import App from "./App.vue";
import i18n from "./i18n"; // Import i18n

const app = createApp(App);
app.use(i18n); // Use i18n
app.mount("#app");
```

### ❌ Error: Translation key not found

**Giải pháp:**

1. Kiểm tra key tồn tại trong `src/i18n/locales/vi.json` và `en.json`
2. Sử dụng đúng syntax: `{{ $t('common.home') }}`
3. Restart dev server

---

## 📊 Analytics Errors

### ❌ Error: gtag is not defined

**Nguyên nhân:** Google Analytics chưa được initialize

**Giải pháp:**

```javascript
// src/App.vue
import { initAnalytics } from "@/composables/useAnalytics";
import { onMounted } from "vue";

onMounted(() => {
  const gaId = import.meta.env.VITE_GA4_MEASUREMENT_ID;
  if (gaId) {
    initAnalytics(gaId);
  }
});
```

### ❌ Error: Analytics not tracking

**Kiểm tra:**

1. GA4 Measurement ID đúng trong `.env`
2. Ad blocker có chặn không
3. Mở Network tab, tìm request đến `google-analytics.com`
4. Kiểm tra GA4 Real-time view

---

## 🏗️ Build Errors

### ❌ Error: Build fails

**Giải pháp:**

```bash
# Clear cache
rm -rf node_modules/.vite
rm -rf dist

# Rebuild
npm run build
```

### ❌ Error: Out of memory

**Giải pháp:**

```bash
# Increase Node memory
export NODE_OPTIONS="--max-old-space-size=4096"
npm run build
```

Windows:

```cmd
set NODE_OPTIONS=--max-old-space-size=4096
npm run build
```

---

## 🔐 Authentication Errors

### ❌ Error: Firebase auth not working

**Kiểm tra:**

1. Firebase config trong `.env` đúng
2. Firebase Authentication enabled trong console
3. Authorized domains configured

```env
VITE_FIREBASE_API_KEY=your-api-key
VITE_FIREBASE_AUTH_DOMAIN=your-app.firebaseapp.com
VITE_FIREBASE_PROJECT_ID=your-project-id
```

---

## 📦 Dependency Errors

### ❌ Error: Module not found

**Giải pháp:**

```bash
# Remove and reinstall
rm -rf node_modules package-lock.json
npm install
```

### ❌ Error: Peer dependency warnings

**Giải pháp:**

```bash
# Install with legacy peer deps
npm install --legacy-peer-deps
```

### ❌ Error: EBADENGINE Unsupported engine

**Nguyên nhân:** Node version không khớp

**Giải pháp:**

1. Kiểm tra Node version:

```bash
node --version
```

2. Cài đặt Node version phù hợp (18+):

```bash
# Using nvm
nvm install 18
nvm use 18
```

---

## 🎨 Styling Errors

### ❌ Error: Tailwind classes not working

**Giải pháp:**

1. Kiểm tra `tailwind.config.js` có content paths đúng:

```javascript
content: [
  "./index.html",
  "./src/**/*.{vue,js,ts,jsx,tsx}",
],
```

2. Restart dev server:

```bash
npm run dev
```

### ❌ Error: CSS not loading

**Giải pháp:**

Kiểm tra `src/main.js` import CSS:

```javascript
import "./index.css";
```

---

## 🚀 Deployment Errors

### ❌ Error: Vercel deployment fails

**Giải pháp:**

1. Kiểm tra build command:

```json
{
  "scripts": {
    "build": "vite build"
  }
}
```

2. Kiểm tra environment variables trong Vercel dashboard

3. Check build logs trong Vercel

### ❌ Error: 404 on refresh

**Nguyên nhân:** SPA routing không được config

**Giải pháp:**

Tạo `vercel.json`:

```json
{
  "rewrites": [{ "source": "/(.*)", "destination": "/index.html" }]
}
```

---

## 🔍 SEO Errors

### ❌ Error: Meta tags not updating

**Giải pháp:**

1. Kiểm tra `useSEO` được gọi trong component:

```javascript
import { useSEO, generatePageMeta } from "@/composables/useSEO";

const meta = generatePageMeta("home");
const { updateMeta } = useSEO(meta);

onMounted(() => {
  updateMeta();
});
```

2. Clear browser cache (Ctrl+Shift+R)

3. View page source (Ctrl+U) để kiểm tra

---

## 🐛 Common Issues

### Issue: Slow performance

**Giải pháp:**

1. Enable production mode:

```bash
npm run build
npm run preview
```

2. Check bundle size:

```bash
npm run build -- --report
```

3. Optimize images (WebP, lazy loading)

### Issue: Memory leak

**Giải pháp:**

1. Check for event listeners not cleaned up
2. Use `onUnmounted` to cleanup:

```javascript
onUnmounted(() => {
  // Cleanup
  window.removeEventListener("scroll", handler);
});
```

### Issue: Console errors in production

**Giải pháp:**

1. Setup error tracking:

```javascript
import { setupGlobalErrorHandler } from "@/composables/useErrorTracking";
setupGlobalErrorHandler(app);
```

2. Check Sentry/error logs

---

## 📞 Getting Help

### 1. Check Documentation

- [DEVELOPER_GUIDE.md](DEVELOPER_GUIDE.md)
- [QUICK_START_NEW_FEATURES.md](QUICK_START_NEW_FEATURES.md)
- [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md)

### 2. Check Console Logs

- Open DevTools (F12)
- Check Console tab
- Check Network tab

### 3. Search Issues

- GitHub Issues
- Stack Overflow
- Vue.js Forum

### 4. Contact Support

- Email: support@hthree.com
- Discord: https://discord.gg/hthree

---

## 🔄 Quick Fixes Checklist

Khi gặp lỗi, thử các bước sau theo thứ tự:

- [ ] Clear browser cache (Ctrl+Shift+R)
- [ ] Restart dev server
- [ ] Clear node_modules và reinstall
- [ ] Check .env file
- [ ] Check console for errors
- [ ] Check network tab
- [ ] Update dependencies
- [ ] Check documentation
- [ ] Search for similar issues
- [ ] Ask for help

---

**Tip:** Luôn check console logs đầu tiên! Hầu hết lỗi đều có thông tin hữu ích trong console. 🔍
