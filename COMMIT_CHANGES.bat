@echo off
echo ========================================
echo COMMIT CHANGES - Auto Payment System
echo ========================================
echo.

echo [1/3] Adding files...
git add .

echo.
echo [2/3] Committing...
git commit -m "Update: Auto payment activation - Remove manual approval"

echo.
echo [3/3] Pushing to GitHub...
git push

echo.
echo ========================================
echo DONE! Changes pushed to GitHub
echo ========================================
pause
