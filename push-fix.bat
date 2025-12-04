@echo off
echo ========================================
echo   PUSH FIX LEN GITHUB
echo ========================================
echo.

echo [1/3] Them files da sua...
git add .

echo.
echo [2/3] Commit...
git commit -m "Fix image loading errors - Replace img.phimapi.com with phimimg.com"

echo.
echo [3/3] Push len GitHub...
git push

if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo   THANH CONG!
    echo ========================================
    echo.
    echo Vercel se tu dong deploy trong 2-3 phut!
    echo Kiem tra tai: https://vercel.com/dashboard
    echo.
) else (
    echo.
    echo ========================================
    echo   LOI!
    echo ========================================
    echo.
)

pause
