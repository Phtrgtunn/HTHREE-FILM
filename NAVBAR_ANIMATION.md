# ✨ NAVBAR HOVER ANIMATION

## 🎯 Mô tả

Thêm animation gạch ngang màu vàng khi hover vào các menu items trên navbar.

---

## 🎨 ANIMATION DETAILS

### Hiệu ứng:

- **Gạch ngang màu vàng** (#f59e0b) xuất hiện từ trái sang phải
- **Duration:** 300ms
- **Easing:** ease (mượt mà)
- **Height:** 2px (0.5rem)
- **Position:** Dưới cùng của menu item

### Cách hoạt động:

```
Normal state:
┌─────────┐
│ Phim Lẻ │
└─────────┘

Hover state:
┌─────────┐
│ Phim Lẻ │
└─────────┘
▓▓▓▓▓▓▓▓▓  ← Gạch vàng xuất hiện
```

---

## 💻 CODE IMPLEMENTATION

### HTML Structure:

```vue
<li class="relative group">
  <router-link class="relative">
    Phim Lẻ
    <!-- Underline animation -->
    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-300 group-hover:w-full"></span>
  </router-link>
</li>
```

### CSS Classes:

```css
/* Container */
.relative.group

/* Link */
.relative
.transition-all
.duration-300

/* Underline */
.absolute
.bottom-0
.left-0
.w-0              /* Width 0 khi normal */
.h-0.5            /* Height 2px */
.bg-yellow-400    /* Màu vàng */
.transition-all
.duration-300
.group-hover:w-full  /* Width 100% khi hover */
```

---

## 🎬 ANIMATION TIMELINE

```
0ms:    w-0 (không hiện)
        ▯

150ms:  w-50% (đang xuất hiện)
        ▓▓▓▓▯▯▯▯

300ms:  w-full (hoàn thành)
        ▓▓▓▓▓▓▓▓
```

---

## 📍 ÁP DỤNG CHO CÁC MENU ITEMS

### 1. Phim Lẻ

```vue
<li class="relative group">
  <router-link to="/list/phim-le/page/1" class="relative">
    Phim Lẻ
    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-300 group-hover:w-full"></span>
  </router-link>
</li>
```

### 2. Phim Bộ

```vue
<li class="relative group">
  <router-link to="/list/phim-bo/page/1" class="relative">
    Phim Bộ
    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-300 group-hover:w-full"></span>
  </router-link>
</li>
```

### 3. Thể loại (Dropdown)

```vue
<li class="relative group">
  <button class="relative">
    Thể loại
    <svg>...</svg>
    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-300 group-hover:w-full"></span>
  </button>
</li>
```

### 4. Quốc gia (Dropdown)

```vue
<li class="relative group">
  <button class="relative">
    Quốc gia
    <svg>...</svg>
    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-300 group-hover:w-full"></span>
  </button>
</li>
```

---

## 🎨 VARIANTS (Tùy chọn khác)

### Variant 1: Gạch từ giữa ra

```vue
<span
  class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-0.5 bg-yellow-400 transition-all duration-300 group-hover:w-full"
></span>
```

### Variant 2: Gạch từ phải sang trái

```vue
<span
  class="absolute bottom-0 right-0 w-0 h-0.5 bg-yellow-400 transition-all duration-300 group-hover:w-full"
></span>
```

### Variant 3: Gạch dày hơn

```vue
<span
  class="absolute bottom-0 left-0 w-0 h-1 bg-yellow-400 transition-all duration-300 group-hover:w-full"
></span>
```

### Variant 4: Gạch với shadow

```vue
<span
  class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow-400 shadow-lg shadow-yellow-400/50 transition-all duration-300 group-hover:w-full"
></span>
```

### Variant 5: Gạch với gradient

```vue
<span
  class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-yellow-400 to-yellow-600 transition-all duration-300 group-hover:w-full"
></span>
```

---

## 🎯 BEST PRACTICES

### 1. Consistent Duration

```css
/* Tất cả animations dùng 300ms */
transition-all duration-300
```

### 2. Consistent Color

```css
/* Tất cả underlines dùng yellow-400 */
bg-yellow-400
```

### 3. Consistent Height

```css
/* Tất cả underlines dùng 2px (h-0.5) */
h-0.5
```

### 4. Use Group Hover

```html
<!-- Parent -->
<li class="relative group">
  <!-- Child -->
  <span class="group-hover:w-full"></span>
</li>
```

---

## 🔧 TROUBLESHOOTING

### Vấn đề 1: Underline không hiện

**Nguyên nhân:** Thiếu `relative` ở parent
**Giải pháp:**

```vue
<li class="relative group">  ← Thêm relative
  <a class="relative">        ← Thêm relative
    ...
  </a>
</li>
```

### Vấn đề 2: Animation không mượt

**Nguyên nhân:** Thiếu transition
**Giải pháp:**

```vue
<span class="transition-all duration-300">  ← Thêm transition
```

### Vấn đề 3: Underline bị lệch

**Nguyên nhân:** Position không đúng
**Giải pháp:**

```vue
<span class="absolute bottom-0 left-0">  ← Đảm bảo bottom-0 left-0
```

---

## 📊 TRƯỚC VÀ SAU

### Trước:

```vue
<li>
  <router-link class="hover:text-yellow-400">
    Phim Lẻ
  </router-link>
</li>
```

**Hiệu ứng:** Chỉ đổi màu text

### Sau:

```vue
<li class="relative group">
  <router-link class="relative">
    Phim Lẻ
    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-300 group-hover:w-full"></span>
  </router-link>
</li>
```

**Hiệu ứng:** Đổi màu text + gạch ngang xuất hiện

---

## 🎉 KẾT QUẢ

✅ **Professional** - Trông chuyên nghiệp hơn
✅ **Smooth** - Animation mượt mà
✅ **Consistent** - Nhất quán trên tất cả menu items
✅ **Modern** - Theo trend hiện đại
✅ **Subtle** - Không quá phô trương

---

## 🚀 TƯƠNG LAI

Có thể áp dụng cho:

- [ ] Footer links
- [ ] Sidebar menu
- [ ] Breadcrumbs
- [ ] Tab navigation
- [ ] Pagination

---

**Tác giả:** Kiro AI  
**Ngày:** 04/12/2024  
**Animation:** Underline hover effect
