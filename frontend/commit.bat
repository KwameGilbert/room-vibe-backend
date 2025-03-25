@echo off
:loop
for /f "tokens=2 delims==" %%i in ('wmic os get localdatetime /value ^| find "="') do set dt=%%i
set commit_message=%dt:~0,4%-%dt:~4,2%-%dt:~6,2% %dt:~8,2%:%dt:~10,2%:%dt:~12,2%
git add -A
git commit --allow-empty -m "%commit_message%"
git push
echo "Done Commiting"
timeout /t 60 > nul
goto loop
