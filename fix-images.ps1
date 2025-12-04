# Script tự động sửa lỗi load ảnh
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  SUA LOI LOAD ANH TU PHIMAPI" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$files = @(
    "src/pages/Homepage.vue",
    "src/pages/NetflixHomepage.vue",
    "src/components/MovieDetailModal.vue",
    "src/components/MovieRow.vue",
    "src/components/FeaturedCarousel.vue",
    "src/components/CommunitySection.vue",
    "src/components/BannerCarousel.vue"
)

$count = 0

foreach ($file in $files) {
    if (Test-Path $file) {
        Write-Host "[+] Dang sua file: $file" -ForegroundColor Yellow
        
        $content = Get-Content $file -Raw
        $newContent = $content -replace 'img\.phimapi\.com', 'phimimg.com'
        
        if ($content -ne $newContent) {
            Set-Content -Path $file -Value $newContent -NoNewline
            $count++
            Write-Host "    -> Da thay the!" -ForegroundColor Green
        } else {
            Write-Host "    -> Khong can thay the" -ForegroundColor Gray
        }
    } else {
        Write-Host "[!] Khong tim thay file: $file" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  HOAN THANH!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Da sua $count files" -ForegroundColor Green
Write-Host ""
Write-Host "Tiep theo:" -ForegroundColor Yellow
Write-Host "1. Build lai: npm run build" -ForegroundColor White
Write-Host "2. Push len GitHub:" -ForegroundColor White
Write-Host "   git add ." -ForegroundColor Gray
Write-Host "   git commit -m 'Fix image loading errors'" -ForegroundColor Gray
Write-Host "   git push" -ForegroundColor Gray
Write-Host ""

pause
