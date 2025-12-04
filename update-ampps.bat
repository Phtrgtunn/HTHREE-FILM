@echo off
echo ========================================
echo   Cap nhat code len AMPPS
echo ========================================
echo.

set AMPPS_PATH=C:\Program Files\Ampps\www\HTHREE_film\HTHREE_film

echo [1/3] Copying backend PHP files...
xcopy /Y /I "backend\api\user_subscription.php" "%AMPPS_PATH%\backend\api\"
xcopy /Y /I "backend\api\orders.php" "%AMPPS_PATH%\backend\api\"
xcopy /Y /I "backend\api\cart.php" "%AMPPS_PATH%\backend\api\"
xcopy /Y /I "backend\api\admin\approve_order.php" "%AMPPS_PATH%\backend\api\admin\"
echo Backend files copied!

echo.
echo [2/3] Building frontend...
call npm run build
echo Frontend built!

echo.
echo [3/3] Copying frontend dist to AMPPS...
xcopy /Y /E /I "dist\*" "%AMPPS_PATH%\dist\"
echo Frontend copied!

echo.
echo ========================================
echo   HOAN THANH! Code da duoc cap nhat
echo ========================================
echo.
echo Vao: http://localhost/HTHREE_film/HTHREE_film/dist/
echo.
pause
