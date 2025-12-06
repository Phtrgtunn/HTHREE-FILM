@echo off
echo ========================================
echo UPDATE: Hide Cart When Not Logged In
echo ========================================
echo.

echo [1/3] Adding changes...
git add .

echo.
echo [2/3] Committing...
git commit -m "Update: Hide cart and pricing button when not logged in

- Cart icon only shows when user is logged in and not admin
- Pricing button only shows when user is logged in and not admin
- Better UX: Don't show features that require login"

echo.
echo [3/3] Pushing to GitHub...
git push

echo.
echo ========================================
echo DONE! Changes pushed successfully
echo ========================================
echo.
echo Changes:
echo - src/components/NetflixNavbar.vue
echo   + Added 'user &&' condition to cart link
echo   + Added 'user &&' condition to pricing link
echo.
pause
