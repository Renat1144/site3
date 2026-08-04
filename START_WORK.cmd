@echo off
setlocal
title Start WordPress work
echo ========================================
echo  Start WordPress work
echo ========================================
echo.
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0script-local-import.ps1" -Force
set "exit_code=%ERRORLEVEL%"
echo.
if "%exit_code%"=="0" goto import_success
if "%exit_code%"=="3" goto import_cancelled
echo Import failed. Review the message above.
goto import_done
:import_success
echo The latest project state was restored successfully.
goto import_done
:import_cancelled
echo Import cancelled. Local data was not changed.
:import_done
echo.
echo This window will close automatically in 10 seconds.
ping 127.0.0.1 -n 11 >nul
exit /b %exit_code%
