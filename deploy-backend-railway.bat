@echo off
echo Deploying backend to Railway...

echo.
echo Step 1: Add all files to git
git add .

echo.
echo Step 2: Commit changes
git commit -m "Fix admin orders API - add PUT method for order updates"

echo.
echo Step 3: Push to Railway
git push origin main

echo.
echo Backend deployed successfully!
echo Check Railway dashboard for deployment status.
pause