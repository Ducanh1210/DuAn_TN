@echo off
echo ====================================================
echo  DANG KHOI DONG DONG THOI LARAVEL VA VIENEU-TTS 
echo ====================================================

:: Tu dong tat cac tien trinh cu dang chiem port 8000 (Laravel) va port 8001 (VieNeu-TTS)
echo [*] Dang kiem tra va tat cac tien trinh cu dang chay (neu co)...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8000 ^| findstr LISTENING') do taskkill /f /pid %%a 2>nul
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8001 ^| findstr LISTENING') do taskkill /f /pid %%a 2>nul
taskkill /f /im python.exe 2>nul
taskkill /f /im php.exe 2>nul
taskkill /f /im vieneu-stream.exe 2>nul

:: Khoi dong Laravel Server va VieNeu-TTS Server trong Windows Terminal
echo [*] Dang khoi dong cac server trong Windows Terminal...
start wt -d "%~dp0." --title "Laravel Server" cmd /k "php artisan serve" ; new-tab -d "%~dp0VieNeu-TTS" --title "VieNeu-TTS API Server" cmd /k "uv sync && uv run vieneu-stream"

echo ====================================================
echo  Da kich hoat ca hai! Vui long kiem tra 2 cua so moi.
echo ====================================================
timeout /t 3
