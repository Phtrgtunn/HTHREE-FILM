@echo off
echo Starting backend PHP server...
start "PHP Backend" cmd /k "cd backend && php -S localhost:8000"

timeout /t 2

echo Starting frontend Vite server...
start "Vite Frontend" cmd /k ""C:\Program Files\nodejs\npm.cmd" run dev"

echo.
echo Backend: http://localhost:8000
echo Frontend: http://localhost:5173
echo.
