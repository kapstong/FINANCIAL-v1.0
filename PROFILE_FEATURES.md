# Profile and Settings Features

## Overview
This document describes the new profile and settings functionality added to the ATIERA Financial Management System.

## New Pages

### 1. Settings Page (`admin/settings.php`)
- **Profile Settings**: Upload and manage profile images
- **Security Settings**: Change password functionality
- **System Preferences**: Dark mode, email notifications, auto-save
- **Notification Settings**: Configure various alert types

### 2. Profile Page (`admin/profile.php`)
- **Profile Overview**: Display user information and statistics
- **Activity History**: Track user actions and login history
- **Edit Profile**: Update profile image and information

## Features

### Profile Image Management
- Users can upload profile images (JPG, PNG, GIF)
- Default image: `admindefault.png`
- Images are stored in the `uploads/` directory
- Automatic file naming: `profile_{user_id}_{timestamp}.{extension}`

### Database Updates
- Added `profile_image` field to `users` table
- Default value: `admindefault.png`
- Images are referenced by filename in the database

### Security Features
- Password change with current password verification
- Minimum password length: 6 characters
- Secure password hashing using PHP's built-in functions

### User Interface
- Tabbed navigation between different settings sections
- Responsive design with Tailwind CSS
- Real-time form validation and feedback
- Success/error message display

## File Structure
```
admin/
├── settings.php          # Settings page
├── profile.php           # Profile page
└── index.php         # Updated with profile image

includes/
└── auth.php              # Enhanced with profile methods

uploads/
└── admindefault.png      # Default profile image

database_update.sql       # Database schema updates
```

## Database Schema
```sql
ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT 'admindefault.png';
```

## Usage

### Uploading Profile Image
1. Navigate to Settings → Profile or Profile → Edit Profile
2. Click "Choose File" and select an image
3. Click "Update Profile" or "Save Changes"
4. Image will be uploaded and displayed immediately

### Changing Password
1. Go to Settings → Security
2. Enter current password
3. Enter new password (minimum 6 characters)
4. Confirm new password
5. Click "Change Password"

### Accessing New Pages
- **Settings**: Click profile menu → Settings
- **Profile**: Click profile menu → Profile
- Both pages are accessible from any admin module

## Technical Details

### File Upload Security
- File type validation (JPG, PNG, GIF only)
- Unique filename generation to prevent conflicts
- Upload directory: `../uploads/` (relative to admin folder)

### Authentication
- All pages require user login
- Profile image updates are user-specific
- Password changes require current password verification

### Session Management
- User data is refreshed after profile updates
- Profile images are displayed in header across all pages
- Consistent navigation between all admin modules

## Notes
- The `admindefault.png` file is a placeholder and should be replaced with an actual image
- Recommended image size: 200x200 pixels
- All profile images are stored locally in the uploads directory
- Profile updates are reflected immediately across all pages
