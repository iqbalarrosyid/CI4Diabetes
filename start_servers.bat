@echo off
start cmd /k "cd /d flask_api && python app.py"
start cmd /k "php -S 0.0.0.0:8080 -t public"

