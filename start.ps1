# ATIERA Financial System - PowerShell Startup Script

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "    ATIERA Financial System" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if Node.js is installed
try {
    $nodeVersion = node --version 2>$null
    if ($LASTEXITCODE -ne 0) {
        throw "Node.js not found"
    }
    Write-Host "✅ Node.js version: $nodeVersion" -ForegroundColor Green
} catch {
    Write-Host "ERROR: Node.js is not installed or not in PATH" -ForegroundColor Red
    Write-Host "Please install Node.js from https://nodejs.org/" -ForegroundColor Yellow
    Write-Host "Required version: 18.0.0 or higher" -ForegroundColor Yellow
    Read-Host "Press Enter to exit"
    exit 1
}

# Check if .env file exists
if (-not (Test-Path ".env")) {
    Write-Host "Creating .env file from template..." -ForegroundColor Yellow
    Copy-Item "env.example" ".env"
    Write-Host ""
    Write-Host "Please edit .env file with your database settings" -ForegroundColor Yellow
    Write-Host "Then run this script again" -ForegroundColor Yellow
    Read-Host "Press Enter to exit"
    exit 1
}

# Check if node_modules exists
if (-not (Test-Path "node_modules")) {
    Write-Host "Installing dependencies..." -ForegroundColor Yellow
    npm install
    if ($LASTEXITCODE -ne 0) {
        Write-Host "ERROR: Failed to install dependencies" -ForegroundColor Red
        Read-Host "Press Enter to exit"
        exit 1
    }
}

# Check if database is set up
Write-Host "Checking database connection..." -ForegroundColor Yellow
try {
    node src/setup-db.js 2>$null
    if ($LASTEXITCODE -ne 0) {
        throw "Database setup failed"
    }
} catch {
    Write-Host ""
    Write-Host "Database setup failed. Please check your WAMP Server configuration." -ForegroundColor Red
    Write-Host "Make sure MySQL service is running and database 'atiera' exists." -ForegroundColor Yellow
    Write-Host ""
    Write-Host "You can also run database setup manually:" -ForegroundColor Yellow
    Write-Host "  npm run setup" -ForegroundColor Cyan
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host ""
Write-Host "Starting ATIERA Financial System..." -ForegroundColor Green
Write-Host "Backend will be available at: http://localhost:5050" -ForegroundColor Cyan
Write-Host "Admin Dashboard: http://localhost:5050/admin/dashboard.php" -ForegroundColor Cyan
Write-Host "Health Check: http://localhost:5050/api/health" -ForegroundColor Cyan
Write-Host ""
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow
Write-Host ""

# Start the development server
npm run dev
