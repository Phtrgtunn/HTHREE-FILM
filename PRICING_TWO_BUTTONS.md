# 🛒 Pricing Page - Two Action Buttons

## ✨ Cập nhật mới

### Trước:
- ❌ Click card → Modal thanh toán trực tiếp
- ❌ Không có giỏ hàng

### Sau:
- ✅ **2 buttons riêng biệt**
- ✅ Button 1: "Thêm vào giỏ" (outline style)
- ✅ Button 2: "Mua ngay" (solid style)

## 🎯 User Flow

### Flow 1: Thêm vào giỏ hàng
```
Click "Thêm vào giỏ" 
  → Thêm vào cart
  → Toast success
  → User tiếp tục xem các gói khác
  → Vào /cart để checkout
```

### Flow 2: Mua ngay
```
Click "Mua ngay"
  → Modal thanh toán hiện ngay
  → Điền form
  → Submit
  → Đơn hàng tạo
```

## 🎨 Button Design

### Button 1: "Thêm vào giỏ" (Secondary)
- **Style:** Outline/Border
- **Colors:**
  - Basic: Blue border + text
  - Premium: Red border + text
  - VIP: Purple border + text
- **Icon:** Shopping cart
- **Hover:** Fill background + white text

### Button 2: "Mua ngay" (Primary)
- **Style:** Solid gradient
- **Colors:**
  - Basic: Blue → Cyan gradient
  - Premium: Red → Yellow gradient
  - VIP: Purple → Pink gradient
- **Icon:** Lightning bolt
- **Hover:** Scale + shine effect

## 💡 UX Benefits

### 1. Flexibility
- User có thể so sánh nhiều gói
- Thêm nhiều gói vào giỏ
- Checkout một lần

### 2. Clear Intent
- "Thêm vào giỏ" → Xem thêm
- "Mua ngay" → Quyết định ngay

### 3. Conversion Optimization
- 2 CTAs tăng conversion
- Primary CTA nổi bật hơn
- Secondary CTA cho người chưa chắc chắn

## 🎨 Visual Hierarchy

```
┌─────────────────────────┐
│   [Icon] Plan Name      │
│   Quality Badge         │
│   Price                 │
│   Features List         │
│                         │
│  ┌───────────────────┐  │ ← Secondary (Outline)
│  │ 🛒 Thêm vào giỏ   │  │
│  └───────────────────┘  │
│                         │
│  ┌───────────────────┐  │ ← Primary (Solid)
│  │ ⚡ Mua ngay       │  │
│  └───────────────────┘  │
└─────────────────────────┘
```

## 🔧 Technical Details

### State Management
```javascript
const addingToCart = ref(null); // Track loading state
```

### Functions
```javascript
// Add to cart
handleAddToCart(plan) {
  → Check login
  → cartStore.addItem()
  → Toast success
}

// Buy now
handleBuyNow(plan) {
  → Check login
  → Show payment modal
  → Direct checkout
}
```

## 📱 Responsive

### Desktop
- 2 buttons stacked vertically
- Full width
- Clear spacing

### Mobile
- Same layout
- Touch-friendly size
- Easy to tap

## 🎯 A/B Test Ideas

### Variant A (Current):
- 2 buttons stacked
- Outline + Solid

### Variant B:
- 2 buttons side by side
- Both solid, different colors

### Variant C:
- 1 primary button
- 1 text link below

## 💰 Pricing Strategy

### Cart Flow:
- Good for: Comparing multiple plans
- Conversion: Medium
- AOV: Higher (multiple items)

### Direct Checkout:
- Good for: Quick decision
- Conversion: Higher
- AOV: Lower (single item)

## 🎉 Expected Results

### Metrics to track:
- **Add to Cart Rate:** % users click "Thêm vào giỏ"
- **Buy Now Rate:** % users click "Mua ngay"
- **Cart Abandonment:** % users add but don't checkout
- **Conversion Rate:** % users complete purchase
- **AOV:** Average order value

### Hypothesis:
- ✅ Add to Cart: 30-40% of users
- ✅ Buy Now: 15-20% of users
- ✅ Overall conversion: +25%

## 🚀 Next Steps

### Phase 1: ✅ Done
- [x] 2 buttons design
- [x] Add to cart function
- [x] Buy now function
- [x] Loading states
- [x] Toast notifications

### Phase 2: Future
- [ ] Cart badge counter in navbar
- [ ] "View Cart" link after add
- [ ] Sticky "Checkout" button
- [ ] Cart preview dropdown
- [ ] Saved for later feature

## 📝 Usage

### Add to Cart:
```
Click "Thêm vào giỏ" → Item added → Continue shopping
```

### Buy Now:
```
Click "Mua ngay" → Modal → Fill form → Submit → Done
```

### View Cart:
```
Navbar → Cart icon (with badge) → /cart page
```

---

**Status:** ✅ IMPLEMENTED - Ready to test!

**Test URL:** http://localhost:5174/pricing
