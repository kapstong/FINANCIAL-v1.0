@echo off
echo ========================================
echo    ATIERA Financial System
echo ========================================
echo.

REM Check if Node.js is installed
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Node.js is not installed or not in PATH
    echo Please install Node.js from https://nodejs.org/
    echo Required version: 18.0.0 or higher
    pause
    exit /b 1
)

REM Check if .env file exists
if not exist ".env" (
    echo Creating .env file from template...
    copy "env.example" ".env"
    echo.
    echo Please edit .env file with your database settings
    echo Then run this script again
    pause
    exit /b 1
)

REM Check if node_modules exists
if not exist "node_modules" (
    echo Installing dependencies...
    npm install
    if %errorlevel% neq 0 (
        echo ERROR: Failed to install dependencies
        pause
        exit /b 1
    )
)

REM Check if database is set up
echo Checking database connection...
node src/setup-db.js >nul 2>&1
if %errorlevel% neq 0 (
    echo.
    echo Database setup failed. Please check your WAMP Server configuration.
    echo Make sure MySQL service is running and database 'atiera' exists.
    echo.
    echo You can also run database setup manually:
    echo   npm run setup
    pause
    exit /b 1
)

echo.
echo Starting ATIERA Financial System...
echo Backend will be available at: http://localhost:5050
echo Admin Dashboard: http://localhost:5050/admin/index.php
echo Health Check: http://localhost:5050/api/health
echo.
echo For WAMP Server access:
echo - Backend API: http://your-wamp-domain:5050
echo - Admin Dashboard: http://your-wamp-domain:5050/admin/index.php
echo - Health Check: http://your-wamp-domain:5050/api/health
echo.
echo Press Ctrl+C to stop the server
echo.

REM Start the development server
npm run dev
