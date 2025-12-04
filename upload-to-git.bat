@echo off
echo ========================================
echo   UPLOAD CODE LEN GITHUB
echo ========================================
echo.

REM Kiem tra Git da cai chua
where git >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Git chua duoc cai dat hoac chua them vao PATH!
    echo Vui long:
    echo 1. Restart terminal sau khi cai Git
    echo 2. Hoac mo Git Bash thay vi CMD/PowerShell
    pause
    exit /b 1
)

echo [1/6] Khoi tao Git repository...
git init

echo.
echo [2/6] Cau hinh Git (neu chua co)...
git config user.name >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    set /p U