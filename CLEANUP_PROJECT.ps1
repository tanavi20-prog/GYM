# Project Cleanup Script for Gym Management System
# Run this in PowerShell from the project root directory

Write-Host "=====================================" -ForegroundColor Cyan
Write-Host "  GYM PROJECT CLEANUP SCRIPT" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""

# Safety check
$confirm = Read-Host "This will DELETE unnecessary files. Continue? (yes/no)"
if ($confirm -ne "yes") {
    Write-Host "Cleanup cancelled." -ForegroundColor Yellow
    exit
}

$deletedCount = 0

# Create backup folder
$backupFolder = ".\BACKUP_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
Write-Host "`nCreating backup folder: $backupFolder" -ForegroundColor Green
New-Item -ItemType Directory -Path $backupFolder -Force | Out-Null

# Function to move file to backup
function Backup-AndDelete {
    param($path)
    if (Test-Path $path) {
        $relativePath = $path -replace [regex]::Escape($PWD), ""
        $backupPath = Join-Path $backupFolder $relativePath
        $backupDir = Split-Path $backupPath -Parent
        
        if (-not (Test-Path $backupDir)) {
            New-Item -ItemType Directory -Path $backupDir -Force | Out-Null
        }
        
        Move-Item $path $backupPath -Force
        Write-Host "Moved: $path" -ForegroundColor Yellow
        $script:deletedCount++
    }
}

Write-Host "`n1. Removing dev-tools folder..." -ForegroundColor Cyan
Backup-AndDelete ".\dev-tools"

Write-Host "`n2. Removing test files from root..." -ForegroundColor Cyan
Backup-AndDelete ".\db-test.php"
Backup-AndDelete ".\test-videos.php"
Backup-AndDelete ".\video-test.php"
Backup-AndDelete ".\add-video.php"
Backup-AndDelete ".\add-trainers-fixed.php"
Backup-AndDelete ".\add-indian-trainers.php"
Backup-AndDelete ".\check-trainers-table.php"

Write-Host "`n3. Removing redundant CRUD files..." -ForegroundColor Cyan
Backup-AndDelete ".\crud\admin_crud.php"
Backup-AndDelete ".\crud\multi_crud.php"
Backup-AndDelete ".\crud\crud_api.php"
Backup-AndDelete ".\crud\delete.php"
Backup-AndDelete ".\crud\edit.php"
Backup-AndDelete ".\crud\insert.php"
Backup-AndDelete ".\crud\display.php"

Write-Host "`n4. Removing redundant schema files..." -ForegroundColor Cyan
Backup-AndDelete ".\database\schema.sql"
Backup-AndDelete ".\database\videos_schema.sql"
Backup-AndDelete ".\docs\fitness_db.sql"
Backup-AndDelete ".\docs\index.html"

Write-Host "`n5. Removing unused public folder..." -ForegroundColor Cyan
Backup-AndDelete ".\public"

Write-Host "`n6. Removing test page files..." -ForegroundColor Cyan
Backup-AndDelete ".\pages\test-player.php"
Backup-AndDelete ".\pages\video-player-integrated.php"
Backup-AndDelete ".\pages\crud.php"

Write-Host "`n=====================================" -ForegroundColor Green
Write-Host "  CLEANUP COMPLETE!" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Green
Write-Host ""
Write-Host "Files moved to backup: $deletedCount" -ForegroundColor Green
Write-Host "Backup location: $backupFolder" -ForegroundColor Green
Write-Host ""
Write-Host "If everything works fine, you can delete the backup folder." -ForegroundColor Yellow
Write-Host ""

# Show remaining structure
Write-Host "`nYour clean project structure:" -ForegroundColor Cyan
Get-ChildItem -Directory | Where-Object { $_.Name -notlike "BACKUP_*" } | Format-Table Name, @{Name="Files";Expression={(Get-ChildItem $_.FullName -File -Recurse | Measure-Object).Count}}
