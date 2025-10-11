# 🔐 Authentication System Guide

## Overview

The Expense Tracker now includes a complete user authentication system that allows multiple users to maintain their own separate expense records securely.

## Features

✅ **User Registration** - New users can create accounts  
✅ **Secure Login** - Password hashing with PHP's built-in functions  
✅ **Session Management** - Secure session handling  
✅ **User Isolation** - Each user can only see their own expenses  
✅ **Protected Routes** - Automatic redirect to login if not authenticated  
✅ **Logout Functionality** - Clean session termination  

## Files Added

### Authentication System Files

- **`auth.php`** - Core authentication helper functions
  - User registration
  - Login/logout handling
  - Session management
  - Input validation and sanitization

- **`login.php`** - User login page
  - Clean, modern login interface
  - Error handling and validation
  - Remember username on failed login

- **`signup.php`** - User registration page
  - Account creation form
  - Email and username validation
  - Password strength requirements
  - Duplicate account prevention

- **`logout.php`** - Logout handler
  - Session cleanup
  - Cookie removal
  - Redirect to login with success message

- **`config.example.php`** - Database configuration template
  - Easy setup for database credentials

## Database Schema

### New Tables

#### Users Table
```sql
CREATE TABLE users (
    id VARCHAR(50) PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP
)
```

#### Updated Expenses Table
```sql
CREATE TABLE expenses (
    id VARCHAR(50) PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,  -- NEW: Links expense to user
    amount DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category) REFERENCES categories(id) ON DELETE CASCADE
)
```

## Setup Instructions

### 1. Database Configuration

First, ensure you have PostgreSQL installed and create a database:

```bash
# Create database (if not exists)
createdb expense_tracker
```

### 2. Configure Database Connection

Copy the example configuration file:

```bash
cp config.example.php config.php
```

Edit `config.php` with your database credentials:

```php
return [
    'db_host' => 'localhost',
    'db_port' => '5432',
    'db_name' => 'expense_tracker',
    'db_user' => 'your_username',
    'db_pass' => 'your_password',
    'db_ssl' => 'prefer',
];
```

### 3. Start the Application

The application will automatically create all necessary tables on first run:

```bash
php -S localhost:8000
```

### 4. Create Your First User

1. Navigate to `http://localhost:8000`
2. You'll be redirected to the login page
3. Click "Sign Up" to create a new account
4. Fill in the registration form:
   - Username (3-20 characters, alphanumeric + underscore)
   - Email address
   - Password (minimum 6 characters)
   - Confirm password
5. After successful registration, you'll be redirected to login
6. Login with your credentials

## Usage

### Registration

**URL:** `signup.php`

**Requirements:**
- Username: 3-20 characters (letters, numbers, underscores only)
- Email: Valid email format
- Password: Minimum 6 characters
- Passwords must match

### Login

**URL:** `login.php`

**Requirements:**
- Valid username
- Correct password

### Logout

**URL:** `logout.php` or click the logout button in the header

### Adding Expenses

Once logged in, you can add expenses normally. Each expense is automatically associated with your user account and only visible to you.

## Security Features

### Password Security
- Passwords are hashed using `password_hash()` with `PASSWORD_DEFAULT`
- Passwords are never stored in plain text
- Password verification uses `password_verify()` for constant-time comparison

### Session Security
- Session IDs are regenerated on login
- Sessions are properly destroyed on logout
- Session cookies are removed on logout

### Input Validation
- All user inputs are sanitized
- SQL injection prevention with prepared statements
- XSS prevention with `htmlspecialchars()`

### User Isolation
- Database queries filter by `user_id`
- Users can only access their own data
- Delete operations verify ownership

## API Changes

### Modified Functions

All database functions now filter by the current user:

- `loadExpenses()` - Returns only current user's expenses
- `saveExpense($expense)` - Saves expense with current user_id
- `deleteExpense($id)` - Only deletes if expense belongs to user
- `getExpenseStats()` - Calculates stats for current user only
- `getCategoryBreakdown()` - Shows breakdown for current user

### New Functions (in auth.php)

- `isLoggedIn()` - Check if user is logged in
- `requireLogin()` - Redirect to login if not authenticated
- `getCurrentUserId()` - Get current user's ID
- `getCurrentUser()` - Get current user's data
- `registerUser($username, $email, $password)` - Register new user
- `loginUser($username, $password)` - Authenticate user
- `logoutUser()` - End user session
- `validateEmail($email)` - Validate email format
- `validatePassword($password)` - Check password strength
- `validateUsername($username)` - Validate username format
- `sanitizeInput($input)` - Sanitize user input

## User Interface Updates

### Header
The main application header now shows:
- Welcome message with username
- Logout button

### Styling
- Modern, clean authentication pages
- Gradient backgrounds matching main app theme
- Smooth animations and transitions
- Responsive design for mobile devices
- Form validation feedback

## Testing

### Test User Creation
1. Go to `signup.php`
2. Create a test account
3. Login and add some expenses
4. Logout and create another account
5. Verify that each user only sees their own expenses

### Test Security
1. Try accessing `index.php` without logging in → Should redirect to login
2. Try accessing another user's expense (if you know the ID) → Should not be accessible
3. Test SQL injection attempts → Should be prevented by prepared statements
4. Test XSS attempts → Should be sanitized

## Troubleshooting

### "Configuration file not found"
- Make sure `config.php` exists
- Copy from `config.example.php` if needed

### "Database connection failed"
- Verify PostgreSQL is running
- Check database credentials in `config.php`
- Ensure database exists

### "Invalid username or password"
- Check username spelling (case-sensitive)
- Verify password is correct
- Ensure account exists

### Session Issues
- Clear browser cookies
- Check session directory permissions
- Verify `session_start()` is called

### Can't see expenses
- Make sure you're logged in
- Verify expenses were created under your user account
- Check database for `user_id` column

## Migration from Single-User

If you have an existing single-user installation with data:

1. Backup your database first!
2. Create a user account
3. Update existing expenses with your user_id:

```sql
-- Get your user_id after registration
SELECT id FROM users WHERE username = 'your_username';

-- Update existing expenses (replace 'your_user_id' with actual ID)
UPDATE expenses SET user_id = 'your_user_id' WHERE user_id IS NULL;
```

## Best Practices

1. **Use Strong Passwords** - Minimum 8+ characters with mixed case, numbers, symbols
2. **Keep Config Secure** - Never commit `config.php` to version control
3. **Regular Backups** - Backup your database regularly
4. **Update PHP** - Keep PHP version up to date for security patches
5. **HTTPS in Production** - Always use HTTPS in production environments

## Future Enhancements

Potential features for future versions:
- Password reset functionality
- Email verification
- Two-factor authentication (2FA)
- Remember me functionality
- Account settings page
- Profile customization
- Password change feature
- Account deletion

## Support

For issues or questions:
1. Check this guide first
2. Review error logs
3. Check database connection
4. Verify all files are present
5. Ensure PostgreSQL is running

## License

Same as the main application (MIT License)

