# 🐛 FIX: Giá không đúng khi chọn gói năm

## Vấn đề:
- Khi chọn gói **1 tháng** (100k) → Đúng
- Khi chọn gói **12 tháng** (1,000k) → Vẫn hiển thị 100k ❌

## Nguyên nhân:
Khi add to cart, `duration_months` luôn = 1, không thay đổi theo `billingPeriod`

## Cách fix:

### 1. Trong Pricing.vue
Khi add to cart, cần truyền `duration_months` đúng:

```javascript
const handleAddToCart = (plan) => {
  const durationMonths = billingPeriod.value === 'yearly' ? 12 : 1;
  const price = getDisplayPrice(plan.price);
  
  cartStore.addItem({
    ...plan,
    duration_months: durationMonths,  // ← QUAN TRỌNG
    price: price,
    total: price * 1  // quantity = 1
  });
};
```

### 2. Trong cartStore
Đảm bảo `duration_months` được lưu:

```javascript
addItem(item) {
  const existingItem = this.items.find(i => 
    i.id === item.id && 
    i.duration_months === item.duration_months  // ← Check cả duration
  );
  
  if (existingItem) {
    existingItem.quantity++;
  } else {
    this.items.push({
      ...item,
      duration_months: item.duration_months || 1,  // ← Default = 1
      quantity: 1
    });
  }
}
```

### 3. Trong Checkout/Order
Khi tạo order, truyền `duration_months`:

```javascript
order_items: cartItems.map(item => ({
  plan_id: item.id,
  duration_months: item.duration_months || 1,  // ← Truyền duration
  quantity: item.quantity,
  price: item.price,
  total: item.price * item.quantity
}))
```

## Test:
1. Chọn gói Premium
2. Toggle giữa "1 tháng" và "12 tháng"
3. Xem giá thay đổi:
   - 1 tháng: 100,000đ
   - 12 tháng: 1,020,000đ (12 * 100k * 0.85 discount)
4. Add to cart
5. Kiểm tra trong cart có đúng giá không
6. Checkout và xem order trong database

## Files cần update:
- [ ] `src/pages/Pricing.vue` - Add duration_months khi add to cart
- [ ] `src/stores/cartStore.js` - Lưu duration_months
- [ ] `src/pages/Cart.vue` - Hiển thị duration (1 tháng / 12 tháng)
- [ ] `src/pages/Checkout.vue` - Truyền duration_months khi tạo order
- [ ] `backend/api/orders.php` - Lưu duration_months vào order_items

## Kết quả mong đợi:
✅ Chọn 1 tháng → Giá đúng
✅ Chọn 12 tháng → Giá = price * 12 * 0.85
✅ Cart hiển thị đúng duration
✅ Order lưu đúng duration_months
✅ Subscription được kích hoạt đúng thời gian

---

**Bạn muốn mình fix ngay không?** 🔧
