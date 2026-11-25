@echo off
chcp 65001 >nul
title E-Shop - Laragon + ngrok
set NGROK_DIR=C:\Users\saymo\Downloads\ngrok-v3-stable-windows-amd64
set VHOST=ecommerce.test
rem Domaine ngrok fixe (optionnel) : ex  set NGROK_DOMAIN=mon-nom.ngrok-free.app
set NGROK_DOMAIN=

cd /d "%~dp0"

if not exist vendor (
    echo [1/4] Installation des dependances PHP...
    call composer install
)

echo [2/4] Migrations et lien storage...
php artisan config:clear
php artisan migrate --force
php artisan storage:link 2>nul

echo.
echo === VOS LIENS SECRETS DE CONNEXION ===
php artisan portails:liens
echo.
echo [3/4] Verifiez que Laragon est demarre (Apache).
echo [4/4] Lancement de ngrok - copiez le lien https affiche (Forwarding).
echo.
cd /d "%NGROK_DIR%"
if defined NGROK_DOMAIN (
    ngrok http 80 --host-header=%VHOST% --url=%NGROK_DOMAIN%
) else (
    ngrok http 80 --host-header=%VHOST%
)
pause
