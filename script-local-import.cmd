@echo off
setlocal
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0script-local-import.ps1" %*
exit /b %ERRORLEVEL%
