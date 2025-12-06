# 🎨 Hướng Dẫn Tính Năng Giao Diện Mới

## 📍 Vị Trí Các Tính Năng Trong Giao Diện

### 1. 🌐 Language Switcher (Chuyển Đổi Ngôn Ngữ)

**Vị trí:** Navbar (góc phải, bên cạnh nút Pricing)

**Giao diện:**

```
┌─────────────────────────────────────────────┐
│ HTHREE  [Menu]  [Search]  🇻🇳 [?] [VIP] [👤] │
│                            ↑                 │
│                    Language Switcher         │
└─────────────────────────────────────────────┘
```

**Cách sử dụng:**

1. Click vào icon cờ (🇻🇳 hoặc 🇺🇸)
2. Dropdown hiện ra với 2 options:
   - 🇻🇳 Tiếng Việt
   - 🇺🇸 English
3. Click để chuyển ngôn ngữ
4. Ngôn ngữ được lưu vào localStorage

**Hiển thị:**

- Desktop: Hiển thị đầy đủ (icon + tên ngôn ngữ)
- Mobile: Chỉ hiển thị icon cờ

---

### 2. ⌨️ Keyboard Shortcuts Help (Phím Tắt)

**Vị trí:** Navbar (góc phải, bên cạnh Language Switcher)

**Giao diện:**

```
┌─────────────────────────────────────────────┐
│ HTHREE  [Menu]  [Search]  🇻🇳 [?] [VIP] [👤] │
│                               ↑              │
│                        Shortcuts Button      │
└─────────────────────────────────────────────┘
```

**Cách mở:**

- Click vào nút `?` trong Navbar
- Hoặc nhấn phím `?` trên bàn phím

**Modal hiển thị:**

```
┌──────────────────────────────────────┐
│  ⌨️ Phím Tắt                         │
│  ────────────────────────────────    │
│                                      │
│  Navigation                          │
│  • Ctrl+K  → Mở command palette      │
│  • /       → Focus search            │
│  • ?       → Hiện shortcuts          │
│  • Esc     → Đóng modals             │
│                                      │
│  Quick Navigation                    │
│  • G+H     → Về trang chủ            │
│  • G+L     → Đến thư viện            │
│  • G+P     → Đến bảng giá            │
│  • G+C     → Đến giỏ hàng            │
│                                      │
│           [Đóng]                     │
└──────────────────────────────────────┘
```

---

### 3. 🔍 Command Palette (Ctrl+K)

**Cách mở:**

- Nhấn `Ctrl+K` (Windows/Linux)
- Hoặc `Cmd+K` (Mac)

**Giao diện:**

```
┌──────────────────────────────────────┐
│  🔍 [Tìm kiếm lệnh...]               │
│  ────────────────────────────────    │
│                                      │
│  📍 Navigation                       │
│  → Trang chủ                         │
│  → Thư viện                          │
│  → Bảng giá                          │
│  → Giỏ hàng                          │
│                                      │
│  🎬 Movies                           │
│  → Phim bộ                           │
│  → Phim lẻ                           │
│  → Anime                             │
│                                      │
│  ⚙️ Settings                         │
│  → Tài khoản                         │
│  → Đăng xuất                         │
│                                      │
└──────────────────────────────────────┘
```

**Tính năng:**

- Fuzzy search (tìm kiếm mờ)
- Keyboard navigation (↑↓ để di chuyển)
- Enter để chọn
- Esc để đóng

---

### 4. 📡 Offline Banner (Thông Báo Mất Mạng)

**Vị trí:** Top của màn hình (fixed position)

**Giao diện khi offline:**

```
┌──────────────────────────────────────┐
│ ⚠️ Không có kết nối mạng             │
│ Vui lòng kiểm tra kết nối internet   │
└──────────────────────────────────────┘
```

**Giao diện khi online lại:**

```
┌──────────────────────────────────────┐
│ ✅ Đã kết nối lại                    │
└──────────────────────────────────────┘
```

**Tự động:**

- Hiện khi mất mạng
- Ẩn khi có mạng trở lại
- Không cần tương tác

---

## 🎯 Các Tính Năng Ẩn (Đã Có Nhưng Không Hiển Thị)

### 1. ✅ Password Strength Meter

**Vị trí:** Trang đăng ký/đổi mật khẩu
**Hiển thị:** Khi nhập password

### 2. 💾 Form Auto-save

**Tự động:** Lưu form data mỗi 2 giây
**Không có UI:** Chạy ngầm

### 3. 🔄 Network Retry

**Tự động:** Retry 3 lần khi API fail
**Không có UI:** Chạy ngầm

### 4. 📊 Analytics Tracking

**Tự động:** Track page views, events
**Không có UI:** Chạy ngầm

### 5. 🔍 Recent Searches

**Vị trí:** Search dropdown
**Hiển thị:** Khi click vào search box

### 6. ☑️ Bulk Actions

**Vị trí:** Library page
**Hiển thị:** Checkbox để chọn nhiều phim

---

## 📱 Responsive Design

### Desktop (> 1024px)

- ✅ Language Switcher: Hiển thị đầy đủ
- ✅ Shortcuts Button: Hiển thị
- ✅ Command Palette: Đầy đủ tính năng

### Tablet (768px - 1024px)

- ✅ Language Switcher: Hiển thị icon
- ✅ Shortcuts Button: Hiển thị
- ✅ Command Palette: Đầy đủ tính năng

### Mobile (< 768px)

- ✅ Language Switcher: Ẩn (có thể thêm vào mobile menu)
- ✅ Shortcuts Button: Ẩn
- ✅ Command Palette: Vẫn hoạt động (Ctrl+K)
- ✅ Offline Banner: Hiển thị

---

## 🎨 Theme & Colors

### Language Switcher

- Background: `bg-gray-800/50`
- Hover: `bg-gray-700/50`
- Border: `border-gray-700`
- Text: `text-white`

### Shortcuts Button

- Background: `bg-gray-800/50`
- Hover: `bg-gray-700/50`
- Icon: `text-gray-400`

### Command Palette

- Background: `bg-gray-900`
- Border: `border-gray-700`
- Highlight: `bg-gray-700`

### Offline Banner

- Offline: `bg-red-600`
- Online: `bg-green-600`
- Text: `text-white`

---

## 🔧 Customization

### Thay đổi vị trí Language Switcher

Edit `src/components/NetflixNavbar.vue`:

```vue
<!-- Move to different position -->
<LanguageSwitcher class="order-1" />
```

### Thay đổi phím tắt

Edit `src/App.vue`:

```javascript
useKeyboardShortcuts({
  "ctrl+k": () => (showCommandPalette.value = true),
  "ctrl+/": () => (showShortcutsHelp.value = true), // Custom
});
```

### Thêm ngôn ngữ mới

Edit `src/i18n/index.js`:

```javascript
import ja from "./locales/ja.json"; // Japanese

const i18n = createI18n({
  messages: {
    vi,
    en,
    ja, // Add new language
  },
});
```

---

## 📸 Screenshots

### Language Switcher

```
┌─────────────────┐
│ 🇻🇳 Tiếng Việt  │ ← Current
├─────────────────┤
│ 🇺🇸 English     │
└─────────────────┘
```

### Keyboard Shortcuts Modal

```
┌────────────────────────────────┐
│ ⌨️ Phím Tắt                    │
│ ──────────────────────────     │
│                                │
│ [Ctrl+K] Command Palette       │
│ [/]      Focus Search          │
│ [?]      Show Shortcuts        │
│ [Esc]    Close Modals          │
│                                │
│ [G+H]    Go to Home            │
│ [G+L]    Go to Library         │
│                                │
│         [Close]                │
└────────────────────────────────┘
```

### Command Palette

```
┌────────────────────────────────┐
│ 🔍 Type to search...           │
│ ──────────────────────────     │
│                                │
│ 📍 Trang chủ                   │
│ 📚 Thư viện                    │
│ 💰 Bảng giá                    │
│ 🛒 Giỏ hàng                    │
│                                │
└────────────────────────────────┘
```

---

## ✅ Testing Checklist

Để kiểm tra các tính năng mới:

- [ ] Language Switcher hiển thị trong Navbar
- [ ] Click Language Switcher → Dropdown mở
- [ ] Chuyển đổi ngôn ngữ → UI text thay đổi
- [ ] Shortcuts button hiển thị trong Navbar
- [ ] Click `?` button → Modal mở
- [ ] Press `?` key → Modal mở
- [ ] Press `Ctrl+K` → Command Palette mở
- [ ] Type in Command Palette → Fuzzy search hoạt động
- [ ] Disconnect internet → Offline banner hiện
- [ ] Reconnect internet → Online banner hiện rồi ẩn

---

## 🆘 Troubleshooting

### Language Switcher không hiển thị

**Kiểm tra:**

1. Component đã được import trong Navbar?
2. i18n đã được setup trong main.js?
3. Translation files tồn tại?

**Fix:**

```javascript
// src/main.js
import i18n from "./i18n";
app.use(i18n);
```

### Keyboard Shortcuts không hoạt động

**Kiểm tra:**

1. useKeyboardShortcuts đã được gọi trong App.vue?
2. Có đang focus vào input field không?

**Fix:**

- Shortcuts không hoạt động khi đang typing trong input
- Click ra ngoài input rồi thử lại

### Command Palette không mở

**Kiểm tra:**

1. Press đúng phím `Ctrl+K`?
2. Component đã được import?

**Fix:**

```vue
<!-- src/App.vue -->
<CommandPalette
  :show="showCommandPalette"
  @close="showCommandPalette = false"
/>
```

---

## 📚 Tài Liệu Liên Quan

- [QUICK_START_NEW_FEATURES.md](QUICK_START_NEW_FEATURES.md) - Hướng dẫn code
- [DEVELOPER_GUIDE.md](DEVELOPER_GUIDE.md) - Developer guide
- [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md) - Installation

---

**Enjoy the new features! 🎉**
