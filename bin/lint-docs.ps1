# lint-docs.ps1 - Audit Kebersihan Dokumentasi (Anti-AI Slop) untuk Windows PowerShell

$docFile = "tentang.md"

if (-not (Test-Path $docFile)) {
    Write-Host "Berkas $docFile tidak ditemukan!" -ForegroundColor Red
    exit 1
}

Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host "   AUDIT HYGIENE DOKUMENTASI (ANTI-AI SLOP) - $docFile" -ForegroundColor Cyan
Write-Host "=====================================================" -ForegroundColor Cyan

# 1. Em-Dash
$emDashChar = [char]0x2014
$emDashMatches = Select-String -Path $docFile -Pattern $emDashChar
$emDashCount = $emDashMatches.Count
Write-Host "`n1. [PELANGGARAN] Deteksi Karakter Em-Dash:"
if ($emDashCount -gt 0) {
    Write-Host "Ditemukan $emDashCount baris dengan em-dash. Contoh:" -ForegroundColor Yellow
    $emDashMatches | Select-Object -First 10 | ForEach-Object { "$($_.LineNumber): $($_.Line.Trim())" }
} else {
    Write-Host "Nol em-dash terdeteksi." -ForegroundColor Green
}

# 2. Buzzwords
$buzzwords = "transformative|game-changer|seamless|elevate|delve|testament|groundbreaking|revolutionize|unparalleled"
$buzzwordMatches = Select-String -Path $docFile -Pattern $buzzwords
$buzzwordCount = $buzzwordMatches.Count
Write-Host "`n2. [PELANGGARAN] Deteksi Buzzwords AI Slop:"
if ($buzzwordCount -gt 0) {
    Write-Host "Ditemukan $buzzwordCount baris dengan buzzwords. Contoh:" -ForegroundColor Yellow
    $buzzwordMatches | Select-Object -First 10 | ForEach-Object { "$($_.LineNumber): $($_.Line.Trim())" }
} else {
    Write-Host "Nol buzzwords terdeteksi." -ForegroundColor Green
}

# 3. Negative Parallelism
$parallelPattern = "bukan sekadar|bukan hanya|tidak hanya"
$parallelMatches = Select-String -Path $docFile -Pattern $parallelPattern
$parallelCount = $parallelMatches.Count
Write-Host "`n3. [PELANGGARAN] Deteksi Negative Parallelism (Bukan X, tapi Y):"
if ($parallelCount -gt 0) {
    Write-Host "Ditemukan $parallelCount baris dengan negative parallelism. Contoh:" -ForegroundColor Yellow
    $parallelMatches | Select-Object -First 10 | ForEach-Object { "$($_.LineNumber): $($_.Line.Trim())" }
} else {
    Write-Host "Nol negative parallelism terdeteksi." -ForegroundColor Green
}

$totalViolations = $emDashCount + $buzzwordCount + $parallelCount
Write-Host "`n=====================================================" -ForegroundColor Cyan
if ($totalViolations -eq 0) {
    Write-Host "HASIL: DOKUMENTASI BERSIH (0 Pelanggaran Slop)" -ForegroundColor Green
} else {
    Write-Host "HASIL: Ditemukan total $totalViolations indikasi slop." -ForegroundColor Yellow
}
