@echo off
echo ========================================
echo   CAP NHAT CODE LEN GITHUB
echo ========================================
echo.

set /p COMMIT_MSG="Nhap mo ta thay doi (Enter de dung mac dinh): "
if "%COMMIT_MSG%"=="" set COMMIT_MSG=Update code - %date% %time%

echo.
echo [1/3] Them files thay doi...
git add .

echo.
echo [2/3] Commit: %COMMIT_MSG%
git commit -m "%COMMIT_MSG%"

echo.
echo [3/3] Push len GitHub...
git push

if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo   THANH CONG!
    echo ========================================
    echo.
    echo Code da duoc cap nhat!
    echo Vercel se tu dong deploy trong vai phut.
    echo.
) else (
    echo.
    echo ========================================
    echo   LOI!
    echo ========================================
    echo.
    echo Vui long kiem tra ket noi hoac chay lai:
    echo push-to-github.bat
    echo.
)

pause
