@echo off
echo ====================================================
echo  DANG KHOI DONG DONG THOI LARAVEL VA VIENEU-TTS 
echo ====================================================

:: Khoi dong Laravel Server o cua so moi
echo [*] Dang khoi dong Laravel Server (port 8000)...
start "Laravel Server" cmd /k "php artisan serve"

:: Di chuyen vao va khoi dong VieNeu-TTS Server o cua so moi
echo [*] Dang khoi dong VieNeu-TTS API Server (port 8001)...
start "VieNeu-TTS API Server" cmd /k "cd /d "%~dp0VieNeu-TTS" && uv run vieneu-stream"

echo ====================================================
echo  Da kich hoat ca hai! Vui long kiem tra 2 cua so moi.
echo ====================================================
timeout /t 3
