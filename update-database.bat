@echo off
chcp 65001 >nul
echo ========================================
echo   CẬP NHẬT DATABASE CHO ADMIN PANEL
echo ========================================
echo.

REM Lấy đường dẫn thư mục chứa script
set SCRIPT_DIR=%~dp0
cd /d "%SCRIPT_DIR%"

REM Cấu hình
set DB_NAME=hthree_film
set DB_USER=root
set DB_PASS=
set BACKUP_FILE=backup_before_admin_update_%date:~-4,4%%date:~-7,2%%date:~-10,2%_%time:~0,2%%time:~3,2%%time:~6,2%.sql
set UPDATE_FILE=update_database_for_admin.sql

echo Thư mục hiện tại: %CD%
echo.

echo [1/4] Kiểm tra file cập nhật...
if not exist "%UPDATE_FILE%" (
    echo ❌ Không tìm thấy file %UPDATE_FILE%
    echo Vui lòng đảm bảo file tồn tại trong thư mục: %CD%
    dir *.sql
    pause
    exit /b 1
)
echo ✅ File cập nhật tồn tại: %UPDATE_FILE%

echo.
echo [2/4] Tạo backup database...
echo Đang backup database %DB_NAME%...

REM Tìm đường dẫn MySQL
set MYSQL_PATH=
if exist "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" (
    set MYSQL_PATH=C:\Program Files\MySQL\MySQL Server 8.0\bin
)
if exist "C:\xampp\mysql\bin\mysql.exe" (
    set MYSQL_PATH=C:\xampp\mysql\bin
)
if exist "C:\ampps\mysql\bin\mysql.exe" (
    set MYSQL_PATH=C:\ampps\mysql\bin
)

if "%MYSQL_PATH%"=="" (
    echo ⚠️  Không tìm thấy MySQL
    echo Vui lòng cài đặt MySQL hoặc XAMPP/AMPPS
    echo.
    echo Bạn có muốn tiếp tục không có backup? (Y/N)
    set /p CONTINUE=
    if /i not "%CONTINUE%"=="Y" (
        echo Đã hủy
        pause
        exit /b 1
    )
) else (
    echo Đang tạo backup...
    "%MYSQL_PATH%\mysqldump.exe" -u %DB_USER% %DB_NAME% > "%BACKUP_FILE%" 2>nul
    if errorlevel 1 (
        echo ⚠️  Không thể tạo backup
        echo Có thể do:
        echo - Sai thông tin database
        echo - MySQL chưa chạy
        echo - Không có quyền truy cập
        echo.
        echo Bạn có muốn tiếp tục không có backup? (Y/N)
        set /p CONTINUE=
        if /i not "%CONTINUE%"=="Y" (
            echo Đã hủy
            pause
            exit /b 1
        )
    ) else (
        echo ✅ Backup thành công: %BACKUP_FILE%
    )
)

echo.
echo [3/4] Cập nhật database...
echo.
echo ⚠️  CẢNH BÁO: Sắp cập nhật database %DB_NAME%
echo.
echo Bạn có chắc chắn muốn tiếp tục? (Y/N)
set /p CONFIRM=
if /i not "%CONFIRM%"=="Y" (
    echo Đã hủy cập nhật
    pause
    exit /b 0
)

echo.
echo Đang cập nhật database...

if "%MYSQL_PATH%"=="" (
    echo.
    echo ❌ Không thể tự động cập nhật
    echo.
    echo Vui lòng thực hiện thủ công:
    echo 1. Mở phpMyAdmin
    echo 2. Chọn database %DB_NAME%
    echo 3. Click tab SQL
    echo 4. Copy nội dung file %UPDATE_FILE%
    echo 5. Paste và click Go
    echo.
    pause
    exit /b 1
)

"%MYSQL_PATH%\mysql.exe" -u %DB_USER% %DB_NAME% < "%UPDATE_FILE%" 2>error.log
if errorlevel 1 (
    echo.
    echo ❌ Cập nhật thất bại!
    echo.
    echo Chi tiết lỗi:
    type error.log
    echo.
    echo Bạn có muốn restore backup? (Y/N)
    set /p RESTORE=
    if /i "%RESTORE%"=="Y" (
        if exist "%BACKUP_FILE%" (
            echo Đang restore backup...
            "%MYSQL_PATH%\mysql.exe" -u %DB_USER% %DB_NAME% < "%BACKUP_FILE%"
            echo ✅ Đã restore backup thành công
        ) else (
            echo ❌ Không tìm thấy file backup
        )
    )
    pause
    exit /b 1
)

echo ✅ Cập nhật database thành công!

echo.
echo [4/4] Kiểm tra kết quả...
echo.

REM Kiểm tra stored procedures
echo Kiểm tra Stored Procedures...
"%MYSQL_PATH%\mysql.exe" -u %DB_USER% %DB_NAME% -e "SHOW PROCEDURE STATUS WHERE Db = '%DB_NAME%' AND Name LIKE 'sp_%%';" 2>nul
if errorlevel 1 (
    echo ⚠️  Không thể kiểm tra procedures
) else (
    echo ✅ Procedures đã được tạo
)

echo.
echo Kiểm tra Views...
"%MYSQL_PATH%\mysql.exe" -u %DB_USER% %DB_NAME% -e "SHOW FULL TABLES WHERE Table_type = 'VIEW';" 2>nul
if errorlevel 1 (
    echo ⚠️  Không thể kiểm tra views
) else (
    echo ✅ Views đã được tạo
)

echo.
echo ========================================
echo   CẬP NHẬT HOÀN TẤT!
echo ========================================
echo.
echo ✅ Database đã được cập nhật thành công
echo ✅ Admin Panel sẵn sàng sử dụng
echo.
if exist "%BACKUP_FILE%" (
    echo 📁 File backup: %BACKUP_FILE%
    echo    (Giữ file này để restore nếu cần)
)
echo.
echo 🚀 Bây giờ bạn có thể:
echo    1. Mở trang Admin
echo    2. Xem thống kê realtime
echo    3. Quản lý đơn hàng
echo    4. Xác nhận thanh toán
echo.

REM Cleanup
if exist error.log del error.log

pause
