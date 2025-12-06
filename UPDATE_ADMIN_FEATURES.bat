@echo off
echo ========================================
echo UPDATE: Admin Features
echo ========================================
echo.
echo [1] Auto Payment Activation
echo [2] Hide Pricing for Admin
echo ========================================
echo.

echo [1/3] Adding all changes...
git add .

echo.
echo [2/3] Committing changes...
git commit -m "Update: Auto payment + Hide pricing for admin

- Auto activate subscription after payment success
- Remove manual approval in admin panel
- Hide pricing/cart/checkout for admin users
- Add route guard to block admin from payment pages
- Admin can watch movies without subscription"

echo.
echo [3/3] Pushing to GitHub...
git push

echo.
echo ========================================
echo DONE! All changes pushed successfully
echo ========================================
echo.
echo Changes:
echo - backend/api/payment/verify.php (NEW)
echo - backend/api/payment/activate_subscription.php
echo - backend/api/admin/orders.php
echo - backend/api/admin/approve_order.php (DELETED)
echo - src/pages/Admin.vue
echo - src/components/NetflixNavbar.vue
echo - src/router/index.js
echo.
pause
