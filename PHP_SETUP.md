# ATIERA Financial System - PHP Setup Guide

## Overview
This system has been converted from Node.js to PHP to work with your WAMP localhost server. It connects directly to your `atiera` MySQL database.

## Prerequisites
- WAMP Server running (Apache + MySQL + PHP)
- MySQL database named `atiera` created
- PHP 7.4+ with PDO and MySQL extensions enabled

## Setup Steps

### 1. Database Setup
First, ensure your `atiera` database exists and has the required tables:

```sql
-- Run this in phpMyAdmin or MySQL command line
CREATE DATABASE IF NOT EXISTS atiera;
USE atiera;

-- Create the users and roles tables
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Insert default roles
INSERT INTO roles (id, name) VALUES (1, 'ADMIN'), (2, 'USER');
```

### 2. File Structure
Ensure your files are in the correct WAMP directory structure:
```
C:\wamp64\www\FINANCIAL\
├── config\
│   └── database.php
├── includes\
│   └── auth.php
├── admin\
│   ├── login.php
│   ├── index.php
│   └── [other modules].php
├── create-admin.php
└── PHP_SETUP.md
```

### 3. Create First Admin User
1. Open your browser and go to: `http://localhost/FINANCIAL/create-admin.php`
2. Fill in the form to create your first admin user
3. You'll see a success message when complete

### 4. Access the System
1. Go to: `http://localhost/FINANCIAL/admin/login.php`
2. Log in with your admin credentials
3. You'll be redirected to the dashboard

## How It Works

### Database Connection
- `config/database.php` handles MySQL connection to your `atiera` database
- Uses PDO for secure database operations
- Default settings: localhost, root user, no password

### Authentication
- `includes/auth.php` manages user sessions and authentication
- Passwords are hashed using PHP's built-in `password_hash()`
- Session-based authentication (no JWT tokens needed)

### File Conversion
- All HTML files have been converted to PHP
- Removed Node.js API calls and dependencies
- Added PHP authentication checks to protected pages

## Troubleshooting

### Database Connection Issues
- Ensure WAMP MySQL service is running
- Check database name is exactly `atiera`
- Verify MySQL credentials in `config/database.php`

### PHP Errors
- Check WAMP PHP error logs
- Ensure PHP PDO and MySQL extensions are enabled
- Verify file permissions

### Session Issues
- Ensure cookies are enabled in browser
- Check PHP session configuration in WAMP

## Next Steps
After basic setup, you can:
1. Convert remaining HTML modules to PHP
2. Implement database-driven data for charts and KPIs
3. Add more financial management features

## Security Notes
- Change default database credentials in production
- Use HTTPS in production environments
- Regularly update WAMP components
- Implement proper input validation for all forms
