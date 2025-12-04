@echo off
echo ========================================
echo   KHOI PHUC DATABASE
echo ========================================
echo.

echo Huong dan:
echo 1. Mo phpMyAdmin: http://localhost/phpMyAdmin
echo 2. Click tab "SQL"
echo 3. Copy noi dung file: backend/database/schema.sql
echo 4. Paste vao va click "Go"
echo.
echo Hoac:
echo 1. Chon database "hthree_film"
echo 2. Click tab "Import"
echo 3. Chon file: backend/database/schema.sql
echo 4. Click "Import"
echo.
echo ========================================
echo   TAI KHOAN MAU
echo ========================================
echo.
echo Admin:
echo   Username: admin
echo   Email: admin@hthree.com
echo   Password: 123456
echo.
echo User Demo:
echo   Username: user1
echo   Email: user1@hthree.com
echo   Password: 123456
echo.
echo ========================================
echo.

echo Ban co muon mo phpMyAdmin khong? (Y/N)
set /p OPEN_PHPMYADMIN=

if /i "%OPEN_PHPMYADMIN%"=="Y" (
    start http://localhost/phpMyAdmin
    echo.
    echo Da mo phpMyAdmin!
)

echo.
echo Xem huong dan chi tiet tai: KHOI_PHUC_DATABASE.md
echo.

pause
