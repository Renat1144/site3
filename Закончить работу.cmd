@echo off
setlocal
title Finish WordPress work
echo ========================================
echo  Finish WordPress work
echo ========================================
echo.
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0script-static-export.ps1"
set "static_exit_code=%ERRORLEVEL%"
if not "%static_exit_code%"=="0" goto static_export_failed
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0script-project-handoff.ps1"
set "handoff_exit_code=%ERRORLEVEL%"
if not "%handoff_exit_code%"=="0" goto handoff_failed
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0script-local-export.ps1"
set "exit_code=%ERRORLEVEL%"
echo.
if "%exit_code%"=="0" goto export_success
echo Export failed. Review the message above.
goto export_done
:export_success
echo Archive created successfully in Google Drive - Codex Drive.
echo Wait until Google Drive reports Sync complete before switching devices.
goto export_done
:static_export_failed
set "exit_code=%static_exit_code%"
echo Static page export failed. The private transfer archive was not created.
goto export_done
:handoff_failed
set "exit_code=%handoff_exit_code%"
echo Project handoff update failed. The private transfer archive was not created.
:export_done
echo.
echo This window will close automatically in 10 seconds.
ping 127.0.0.1 -n 11 >nul
exit /b %exit_code%
