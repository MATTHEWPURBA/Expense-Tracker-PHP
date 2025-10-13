# 🚀 Quick Start - Authentication Setup

## What Was Added?

Your Expense Tracker now has a **complete multi-user authentication system**! Multiple users can now create accounts and track their expenses separately.

---

## ✨ New Features

### 1. User Registration (Sign Up)
- Create a new account with username, email, and password
- Secure password hashing
- Duplicate account prevention

### 2. User Login
- Secure authentication
- Session management
- Persistent login across pages

### 3. User Logout
- Clean session cleanup
- Secure logout

### 4. User Isolation
- Each user sees only their own expenses
- Complete data privacy
- Secure database queries

---

## 📁 Files Added

- **`auth.php`** - Authentication functions
- **`login.php`** - Login page
- **`signup.php`** - Registration page
- **`logout.php`** - Logout handler
- **`config.example.php`** - Database config template

## 📝 Files Updated

- **`index.php`** - Added authentication checks and user filtering
- **`README.md`** - Updated documentation

---

## 🎯 How to Use

### Step 1: Configure Database

If you haven't already, copy the config template:

```bash
cp config.example.php config.php
```

Edit `config.php` with your PostgreSQL credentials:

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

### Step 2: Start the Server

```bash
php -S localhost:8000
```

### Step 3: Create Your Account

1. Open http://localhost:8000 in your browser
2. You'll be redirected to the login page
3. Click **"Sign Up"**
4. Fill in:
   - **Username:** 3-20 characters (letters, numbers, underscores)
   - **Email:** Valid email address
   - **Password:** Minimum 6 characters
   - **Confirm Password:** Must match
5. Click **"Create Account"**

### Step 4: Login

1. Enter your username and password
2. Click **"Sign In"**
3. You'll be redirected to the expense tracker!

### Step 5: Start Tracking

- Add expenses as usual
- View your statistics
- Export your data
- Click **"Logout"** when done

---

## 🔒 Security Features

✅ **Password Hashing** - Passwords are never stored in plain text  
✅ **Session Security** - Secure session management  
✅ **User Isolation** - You can only see your own data  
✅ **SQL Injection Protection** - All queries use prepared statements  
✅ **XSS Prevention** - All inputs are sanitized  

---

## 👥 Multiple Users

Want to test with multiple users?

1. Logout (click the logout button)
2. Create another account with different username/email
3. Login with the new account
4. Add expenses - they'll be separate from the first user!
5. Switch between accounts to verify data isolation

---

## 📖 Database Schema

The application automatically creates these tables:

### `users` table
- Stores user accounts
- Hashed passwords
- Email addresses
- Login timestamps

### `expenses` table (updated)
- Now includes `user_id` column
- Links each expense to a specific user
- Enforces data isolation

### `categories` table
- Shared across all users
- Pre-defined categories

---

## 🔧 Troubleshooting

### "Configuration file not found"
**Solution:** Copy `config.example.php` to `config.php`

### "Database connection failed"
**Solution:** 
- Verify PostgreSQL is running
- Check credentials in `config.php`
- Ensure database exists

### "Username already exists"
**Solution:** Try a different username

### "Invalid username or password"
**Solution:** 
- Check username spelling
- Verify password
- Try resetting your password (if you've set that up)

### Can't see logout button
**Solution:** 
- Make sure you're logged in
- Clear browser cache
- Refresh the page

### Session issues
**Solution:**
- Clear browser cookies
- Restart PHP server
- Check PHP session directory permissions

---

## 📚 Documentation

For more details, see:

- **AUTH_GUIDE.md** - Complete authentication guide
- **AUTHENTICATION_CHANGELOG.md** - Detailed changelog
- **README.md** - Updated project documentation

---

## 🎓 Testing It Out

### Quick Test Scenario

1. **Create User 1:**
   - Username: `john_doe`
   - Email: `john@example.com`
   - Password: `password123`

2. **Add Expenses as User 1:**
   - Add 3-4 expenses
   - Note the statistics

3. **Logout**

4. **Create User 2:**
   - Username: `jane_doe`
   - Email: `jane@example.com`
   - Password: `password123`

5. **Add Expenses as User 2:**
   - Add different expenses
   - Note different statistics

6. **Verify Isolation:**
   - Logout and login as User 1
   - Should only see User 1's expenses
   - Statistics should be for User 1 only

---

## 🚀 Next Steps

Now that you have authentication set up, consider:

1. **Deploy to Production**
   - Use HTTPS
   - Use strong passwords
   - Regular backups

2. **Customize**
   - Add more categories
   - Customize colors
   - Add more features

3. **Enhance Security**
   - Add password reset
   - Add email verification
   - Add 2FA (future feature)

---

## ✅ Verification Checklist

Make sure everything works:

- [ ] Can create a new account
- [ ] Can login with credentials
- [ ] Can add expenses
- [ ] Can see statistics (user-specific)
- [ ] Can export data (user-specific)
- [ ] Can delete expenses
- [ ] Can logout
- [ ] Redirected to login when not authenticated
- [ ] Multiple users have separate data

---

## 💡 Tips

1. **Strong Passwords:** Use passwords with at least 8+ characters
2. **Unique Usernames:** Choose memorable but unique usernames
3. **Valid Email:** Use real email (for future features)
4. **Regular Backups:** Backup your database regularly
5. **HTTPS:** Use HTTPS in production

---

## 🎉 You're All Set!

Your Expense Tracker now supports multiple users with secure authentication!

**Enjoy tracking your expenses! 💰**

---

## 📞 Need Help?

- Check **AUTH_GUIDE.md** for detailed documentation
- Review error logs for debugging
- Check database connectivity
- Verify PHP version (7.4+)
- Ensure PostgreSQL is running

---

**Happy Expense Tracking! 🎯**

