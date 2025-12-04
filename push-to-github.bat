@echo off
echo ========================================
echo   PUSH CODE LEN GITHUB
echo ========================================
echo.

REM Kiem tra git da cai chua
where git >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] Git chua duoc cai dat!
    echo Vui long cai Git tai: https://git-scm.com/download/win
    pause
    exit /b 1
)

echo [1/6] Khoi tao Git repository...
git init
if %errorlevel% neq 0 (
    echo Git repository da ton tai, bo qua buoc nay.
)

echo.
echo [2/6] Them tat ca files...
git add .

echo.
echo [3/6] Commit code...
git commit -m "Update: HTHREE Film Project - %date% %time%"

echo.
echo [4/6] Doi branch thanh main...
git branch -M main

echo.
echo [5/6] Ket noi voi GitHub...
echo.
echo ========================================
echo   NHAP THONG TIN GITHUB CUA BAN
echo ========================================
set /p GITHUB_USERNAME="Nhap GitHub username (Phtrgtunn): "
if "%GITHUB_USERNAME%"=="" set GITHUB_USERNAME=Phtrgtunn

set /p REPO_NAME="Nhap ten repository (HTHREE-film): "
if "%REPO_NAME%"=="" set REPO_NAME=HTHREE-film

echo.
echo Repository URL: https://github.com/%GITHUB_USERNAME%/%REPO_NAME%.git
echo.

REM Xoa remote cu neu co
git remote remove origin 2>nul

REM Them remote moi
git remote add origin https://github.com/%GITHUB_USERNAME%/%REPO_NAME%.git

echo.
echo [6/6] Push code len GitHub...
echo.
echo ========================================
echo   LUU Y: Khi hoi password
echo   - Username: %GITHUB_USERNAME%
echo   - Password: Dung PERSONAL ACCESS TOKEN
echo     (Khong phai password GitHub!)
echo.
echo   Tao token tai:
echo   https://github.com/settings/tokens
echo ========================================
echo.
pause

git push -u origin main

if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo   THANH CONG!
    echo ========================================
    echo.
    echo Code da duoc push len:
    echo https://github.com/%GITHUB_USERNAME%/%REPO_NAME%
    echo.
    echo Tiep theo:
    echo 1. Vao https://vercel.com/dashboard
    echo 2. Click "New Project"
    echo 3. Import tu GitHub
    echo 4. Chon repository "%REPO_NAME%"
    echo 5. Click "Deploy"
    echo.
) else (
    echo.
    echo ========================================
    echo   LOI!
    echo ========================================
    echo.
    echo Neu gap loi authentication:
    echo 1. Tao Personal Access Token:
    echo    https://github.com/settings/tokens
    echo 2. Chon quyen: repo (tat ca)
    echo 3. Copy token
    echo 4. Dung token lam password khi push
    echo.
)

pause
