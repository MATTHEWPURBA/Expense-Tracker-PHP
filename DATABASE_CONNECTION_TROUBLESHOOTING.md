# Database Connection Troubleshooting Guide

## Current Error: "Database connection failed"

This error indicates that the application cannot connect to your PostgreSQL database.

## Common Causes and Solutions

### 1. config.php File Missing or Not Uploaded

**Problem:** The `config.php` file doesn't exist on the server or has wrong credentials.

**Solution:**
1. **Check if config.php exists** on your server:
   - In File Manager, navigate to `htdocs/`
   - Verify `config.php` file exists

2. **Create config.php if missing:**
   - Copy `config.example.php` to `config.php`
   - Edit `config.php` with your actual database credentials

3. **Verify credentials in config.php:**
   ```php
   return [
       'db_host' => 'your-neon-host.neon.tech',  // Your PostgreSQL host
       'db_port' => '5432',                       // Usually 5432
       'db_name' => 'neondb',                     // Your database name
       'db_user' => 'username',                   // Your database username
       'db_pass' => 'password',                   // Your database password
       'db_ssl' => 'require',                     // SSL mode (usually 'require' for Neon)
   ];
   ```

### 2. PostgreSQL Extension Not Installed

**Problem:** InfinityFree might not have PostgreSQL extensions installed.

**Solution:**
1. **Check PHP extensions in VistaPanel:**
   - Go to VistaPanel → PHP Configuration
   - Check if `pdo_pgsql` or `pgsql` extension is available
   - If not available, contact InfinityFree support

2. **Alternative:** Use MySQL instead (requires code changes)

### 3. Database Host Blocked or Unreachable

**Problem:** InfinityFree servers might be blocking outbound connections to PostgreSQL databases.

**Solution:**
1. **Test database connection from your local computer:**
   ```bash
   # Test using psql
   psql -h your-host.neon.tech -U your-user -d your-database
   ```

2. **Check if database allows connections from anywhere:**
   - In Neon/Supabase dashboard
   - Check "Allow connections from anywhere" or add InfinityFree IP ranges

3. **Try different SSL mode:**
   ```php
   'db_ssl' => 'prefer',  // Instead of 'require'
   ```
   Or:
   ```php
   'db_ssl' => 'disable',  // Less secure, but might work
   ```

### 4. Wrong Database Credentials

**Problem:** Database credentials in `config.php` are incorrect.

**Solution:**
1. **Double-check credentials:**
   - Host: Should be your Neon/Supabase connection string
   - Database name: Verify it exists
   - Username: Check it's correct
   - Password: Copy-paste from database dashboard

2. **Test credentials:**
   - Use a database client (pgAdmin, DBeaver)
   - Try connecting with the same credentials

### 5. Database Service Not Running

**Problem:** Your PostgreSQL database might be paused or not running.

**Solution:**
1. **Check database status:**
   - Log into Neon/Supabase dashboard
   - Verify database is active (not paused)

2. **Wake up the database:**
   - Some free PostgreSQL services pause inactive databases
   - Make a connection request to wake it up

## Quick Diagnostic Steps

### Step 1: Check config.php

Create a test file `test-db.php` in your `htdocs/` directory:

```php
<?php
require_once __DIR__ . '/bootstrap.php';

echo "=== Database Connection Test ===\n";
echo "Host: " . config('db_host') . "\n";
echo "Port: " . config('db_port') . "\n";
echo "Database: " . config('db_name') . "\n";
echo "User: " . config('db_user') . "\n";
echo "SSL Mode: " . config('db_ssl', 'require') . "\n\n";

// Check if extension is loaded
echo "=== PHP Extensions ===\n";
if (extension_loaded('pdo_pgsql')) {
    echo "✓ pdo_pgsql extension is loaded\n";
} else {
    echo "✗ pdo_pgsql extension is NOT loaded\n";
}

if (extension_loaded('pgsql')) {
    echo "✓ pgsql extension is loaded\n";
} else {
    echo "✗ pgsql extension is NOT loaded\n";
}

echo "\n=== Connection Test ===\n";
try {
    $dsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s;sslmode=%s",
        config('db_host'),
        config('db_port'),
        config('db_name'),
        config('db_ssl', 'require')
    );
    
    $pdo = new PDO(
        $dsn,
        config('db_user'),
        config('db_pass'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✓ Database connection successful!\n";
    echo "Server version: " . $pdo->query('SELECT version()')->fetchColumn() . "\n";
} catch (Exception $e) {
    echo "✗ Connection failed: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
}
```

Visit: `https://yoursite.rf.gd/test-db.php`

**Important:** Delete this file after testing!

### Step 2: Check Server Logs

1. **Check InfinityFree error logs:**
   - VistaPanel → Error Log
   - Look for database connection errors

2. **Check application logs:**
   - File: `logs/error.log`
   - Look for detailed error messages

### Step 3: Verify Database Service

1. **Neon.tech:**
   - Log into dashboard
   - Check connection string
   - Verify database is not paused
   - Check "Allow connections from anywhere"

2. **Supabase:**
   - Log into dashboard
   - Check Connection Pooling settings
   - Verify database is active

## Alternative Solutions

### Solution A: Use MySQL Instead (If PostgreSQL Not Available)

InfinityFree provides MySQL databases. You would need to:
1. Modify database service to use MySQL
2. Update all queries to MySQL syntax
3. This is a significant code change

### Solution B: Use Different PostgreSQL Hosting

If Neon/Supabase connections are blocked, try:
1. **ElephantSQL** (free tier available)
2. **Railway** (has free tier)
3. **Render** (has free PostgreSQL option)

## Getting More Help

### Check Error Logs

1. **InfinityFree Error Log:**
   - VistaPanel → Error Log
   - Copy error messages

2. **Application Error Log:**
   - File: `htdocs/logs/error.log`
   - Check for detailed error messages

### Enable Debug Mode Temporarily

In `bootstrap.php`, change:
```php
ini_set('display_errors', '1'); // Enable for debugging
```

Then check the actual error message in the browser.

**Remember:** Disable this after debugging for security!

## Expected Success Indicators

After fixing the database connection:

✅ API endpoints return JSON responses  
✅ Login works without "Database connection failed" error  
✅ No errors in logs/error.log  
✅ Dashboard loads expenses  
✅ Can create and read data  

---

**Last Updated:** 2025  
**Version:** 1.0

