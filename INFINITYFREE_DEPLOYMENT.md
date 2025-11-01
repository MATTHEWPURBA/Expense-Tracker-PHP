# 🚀 InfinityFree Deployment Guide - Expense Tracker PHP

Complete step-by-step guide to deploy your Expense Tracker application to InfinityFree hosting.

---

## 📋 Prerequisites

- ✅ Expense Tracker project files
- ✅ Email address for account registration
- ✅ Basic knowledge of file management

---

## 🌐 STEP 1: Create InfinityFree Account

### 1.1 Sign Up

1. **Go to:** https://infinityfree.com
2. **Click:** "Sign Up Now" button
3. **Fill in your details:**
   - Email address
   - Password (strong password recommended)
4. **Click:** "Sign Up"
5. **Check your email** and verify your account

---

## 🌐 STEP 2: Create Website

### 2.1 Create Account (Website)

1. **Login** to your InfinityFree account
2. **Click:** "Create Account" button
   - *(Note: "Account" means website/hosting account)*

### 2.2 Choose Domain

**Option A: Free Subdomain** (Recommended for beginners)

1. Choose a subdomain extension:
   - `.rf.gd` (recommended)
   - `.ct8.pl`
   - `.unaux.com`
   - Or others available

2. Enter your desired subdomain name:
   - Example: `myexpenses`
   - Result: `myexpenses.rf.gd`

**Option B: Use Your Own Domain** (If you have one)

1. Enter your domain name
2. Follow DNS configuration instructions provided
3. Wait for DNS propagation (1-48 hours)

### 2.3 Finalize Setup

1. **Enter a label** (just a name for you to identify this site)
   - Example: "Expense Tracker"
   
2. **Click:** "Create Account"

3. **Wait 2-5 minutes** for activation
   - You'll receive an email when ready

---

## 🌐 STEP 3: Access Control Panel

### 3.1 Navigate to Control Panel

1. **Login** to InfinityFree
2. **Go to:** "Client Area" → "Accounts"
3. **Find** your newly created account
4. **Click:** "Control Panel" (this opens VistaPanel)

### 3.2 VistaPanel Overview

You'll see these tools:
- ✅ **Online File Manager** - We'll use this to upload files
- **FTP Details** - For FTP access (optional)
- **MySQL Databases** - For database setup
- **SSL Certificates** - For HTTPS
- **PHP Configuration** - PHP settings
- And more...

---

## 🌐 STEP 4: Prepare Files for Upload

### 4.1 Files to Upload

Before uploading, prepare these files from your project:

**Essential Files:**
```
✅ index.php
✅ login.php
✅ signup.php
✅ logout.php
✅ bootstrap.php
✅ .htaccess
✅ config.example.php (we'll create config.php on server)
✅ composer.json
✅ api.php
✅ settings.php
```

**Directories to Upload:**
```
✅ src/           (all files inside)
✅ views/         (all files inside)
✅ js/            (all files inside)
✅ data/          (create empty on server, set permissions)
✅ logs/          (create empty on server)
✅ vendor/        (Composer dependencies - IMPORTANT!)
```

**Files NOT to Upload:**
```
❌ .git/          (Git repository)
❌ serve          (Local development script)
❌ manage.sh      (Local management script)
❌ setup.sh       (Local setup script)
❌ check          (Local utility)
❌ stop           (Local utility)
❌ show-logs      (Local utility)
❌ *.md           (Documentation files - optional, can upload)
❌ node_modules/  (If exists - not needed)
❌ config.php     (Create fresh on server - NEVER upload your local one!)
```
### 4.2 Install Composer Dependencies Locally

**IMPORTANT:** You need to upload the `vendor/` directory to the server. This directory contains all PHP libraries your application needs. It's too large to install on the free hosting, so we install it locally and upload it.

---

#### Step 1: Check if Composer is Installed

1. **Open Terminal/Command Prompt:**
   - **Mac/Linux:** Open Terminal
   - **Windows:** Open Command Prompt or PowerShell

2. **Navigate to your project directory:**
   ```bash
   cd /Users/910219/Downloads/Expense-Tracker-PHP
   ```
   *(Replace with your actual project path)*

3. **Check if Composer is installed:**
   ```bash
   composer --version
   ```

   **Expected output:**
   ```
   Composer version 2.x.x
   ```
   
   ✅ **If you see a version number:** Composer is installed! Skip to Step 3.

   ❌ **If you see:** `command not found` or `composer: command not found`
   - Continue to Step 2 to install Composer

---

#### Step 2: Install Composer (If Not Installed)

**For Mac Users:**

1. **Install using Homebrew** (Recommended):
   ```bash
   brew install composer
   ```

2. **Or install globally:**
   ```bash
   php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
   php composer-setup.php
   php -r "unlink('composer-setup.php');"
   sudo mv composer.phar /usr/local/bin/composer
   ```

3. **Verify installation:**
   ```bash
   composer --version
   ```

**For Windows Users:**

1. **Download Composer Installer:**
   - Visit: https://getcomposer.org/download/
   - Download `Composer-Setup.exe`

2. **Run the installer:**
   - Follow the installation wizard
   - Make sure it detects your PHP installation

3. **Verify installation:**
   - Open **Command Prompt**
   - Run:
     ```bash
     composer --version
     ```

**For Linux Users:**

1. **Install via package manager:**
   ```bash
   sudo apt-get update
   sudo apt-get install composer
   ```
   *(For Ubuntu/Debian)*

   OR

   ```bash
   sudo yum install composer
   ```
   *(For CentOS/RHEL)*

2. **Verify installation:**
   ```bash
   composer --version
   ```

---

#### Step 3: Navigate to Project Directory

1. **Open Terminal/Command Prompt**

2. **Navigate to your Expense Tracker project:**
   ```bash
   cd /Users/910219/Downloads/Expense-Tracker-PHP
   ```

3. **Verify you're in the right directory:**
   ```bash
   ls -la
   ```
   
   *(On Windows, use: `dir`)*

   **You should see:**
   - ✅ `composer.json` file
   - ✅ `index.php`
   - ✅ `src/` directory
   - ✅ Other project files

---

#### Step 4: Verify composer.json Exists

1. **Check if composer.json exists:**
   ```bash
   ls -la composer.json
   ```
   
   *(On Windows: `dir composer.json`)*

2. **View composer.json contents** (optional):
   ```bash
   cat composer.json
   ```
   
   *(On Windows: `type composer.json`)*

   **Expected content:** Should show dependencies like:
   ```json
   {
       "require": {
           "some/package": "^version"
       }
   }
   ```

---

#### Step 5: Install Dependencies

1. **Run Composer install:**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

   **What this command does:**
   - `composer install` - Installs all packages listed in `composer.json`
   - `--no-dev` - Skips development dependencies (saves space)
   - `--optimize-autoloader` - Optimizes autoloader for production (faster)

2. **Wait for installation** (may take 1-5 minutes):
   - Composer will download packages from internet
   - You'll see progress output
   - Don't close terminal until finished

3. **Expected output:**
   ```
   Loading composer repositories with package information
   Updating dependencies
   Lock file operations: X installs, 0 updates, 0 removals
   Writing lock file
   Installing dependencies from lock file
   Package operations: X installs, 0 updates, 0 removals
   Generating optimized autoload files
   ```

---

#### Step 6: Verify Installation

1. **Check if vendor directory was created:**
   ```bash
   ls -la vendor/
   ```
   
   *(On Windows: `dir vendor`)*

2. **Verify required files exist:**
   ```bash
   ls -la vendor/autoload.php
   ```
   
   **Expected output:** Should show `vendor/autoload.php` exists

3. **Check vendor directory structure:**
   ```bash
   ls -la vendor/
   ```
   
   **You should see:**
   - ✅ `autoload.php` (required file)
   - ✅ `composer/` directory
   - ✅ Various package directories (installed packages)

4. **Verify directory size** (optional):
   ```bash
   du -sh vendor/
   ```
   
   *(On Windows: `powershell -Command "(Get-ChildItem -Path vendor -Recurse | Measure-Object -Property Length -Sum).Sum / 1MB"`)*
   
   **Expected:** Should be several MB (5-50MB depending on dependencies)

---

#### Step 7: Verify composer.lock File

1. **Check if composer.lock exists:**
   ```bash
   ls -la composer.lock
   ```
   
   ✅ **If exists:** Good! Upload this file too (see Step 5.2 for upload instructions)

   ⚠️ **If missing:** Not critical, but recommended to generate:
   ```bash
   composer update --lock
   ```

---

#### Step 8: Final Verification Checklist

Before uploading, verify:

✅ **Composer installed** (`composer --version` works)
✅ **In project directory** (`composer.json` exists)
✅ **Dependencies installed** (`vendor/` directory exists)
✅ **autoload.php exists** (`vendor/autoload.php` exists)
✅ **vendor/ directory has content** (not empty)
✅ **composer.lock exists** (optional but recommended)

---

#### Troubleshooting

**Issue 1: "composer: command not found"**

**Solution:**
- Install Composer (see Step 2)
- Restart terminal after installation
- On Windows: Restart computer if PATH wasn't updated

---

**Issue 2: "composer.json not found"**

**Solution:**
- Make sure you're in the project root directory
- Verify `composer.json` file exists:
  ```bash
  ls -la composer.json
  ```
- Navigate to correct directory if needed

---

**Issue 3: "Permission denied" or "Could not write"**

**Solution:**
- Check directory permissions:
  ```bash
  ls -la .
  ```
- Fix permissions if needed:
  ```bash
  chmod 755 .
  ```
- On Windows: Run Command Prompt as Administrator

---

**Issue 4: "Connection timeout" or network errors**

**Solution:**
- Check internet connection
- Try again (may be temporary)
- If using proxy, configure Composer:
  ```bash
  composer config -g http-proxy-url http://proxy:port
  ```

---

**Issue 5: "vendor/ directory is empty"**

**Solution:**
- Delete vendor directory:
  ```bash
  rm -rf vendor/
  ```
  *(On Windows: `rmdir /s vendor`)*
- Run install again:
  ```bash
  composer install --no-dev --optimize-autoloader
  ```

---

**Issue 6: "PHP version mismatch"**

**Solution:**
- Check PHP version:
  ```bash
  php -v
  ```
- Ensure PHP 7.4 or higher is installed
- Update PHP if needed

---

#### What to Upload Later

After successful installation, you'll upload:

- ✅ **entire `vendor/` directory** (including all subdirectories)
- ✅ `composer.json` file
- ✅ `composer.lock` file (if exists)

**Important:** Upload the entire `vendor/` folder, not individual files!

---

#### Summary

After completing these steps, you should have:

1. ✅ Composer installed and working
2. ✅ `vendor/` directory created with all dependencies
3. ✅ `vendor/autoload.php` file exists
4. ✅ Ready to upload `vendor/` directory to server

**Next Step:** Continue to Step 5 (Upload Files) and upload the `vendor/` directory to your InfinityFree hosting.

---

## 🌐 STEP 5: Upload Files

### Method 1: Using File Manager (Easiest) ✅

#### 5.1 Access File Manager

1. In **VistaPanel**, click **"Online File Manager"**
2. Navigate to **`htdocs/`** folder
   - This is your web root directory
   - All files here are accessible via your domain

#### 5.2 Upload Essential Files

1. **Click "Upload"** button (top toolbar)

2. **Select and upload these files:**
   - `index.php`
   - `login.php`
   - `signup.php`
   - `logout.php`
   - `bootstrap.php`
   - `api.php`
   - `settings.php`
   - `composer.json`
   - `composer.lock` (if exists)

3. **Upload .htaccess:**
   - ⚠️ **Important:** Hidden files (starting with `.`) might not upload easily
   - If you can't select `.htaccess`, use FTP method (below) or:
     - Click "Create File" in File Manager
     - Name it: `.htaccess`
     - Paste content from your local `.htaccess` file

4. **Upload `config.example.php`:**
   - This will be used as template for creating `config.php`

#### 5.3 Upload Directories

**Upload `src/` directory:**
1. Click "Upload" → "Upload Folder" (if available)
2. Or upload all files from `src/` one by one maintaining structure:
   ```
   htdocs/
   └── src/
       ├── Controllers/
       ├── Middleware/
       ├── Models/
       ├── Router/
       └── Services/
   ```

**Upload `views/` directory:**
- Upload entire `views/` directory with all subdirectories

**Upload `js/` directory:**
- Upload all JavaScript files

**Upload `vendor/` directory:**
- ⚠️ **This is large** - may take several minutes
- Upload entire `vendor/` directory
- Make sure `vendor/autoload.php` exists

#### 5.4 Create Data Directory

1. In File Manager, click **"New Folder"**
2. Name it: **`data`**
3. Click **"Create Folder"**

#### 5.5 Create Logs Directory

1. Click **"New Folder"** again
2. Name it: **`logs`**
3. Click **"Create Folder"**

---

### Method 2: Using FTP (Professional) ✅

#### 5.1 Get FTP Credentials

1. In **VistaPanel**, find **"FTP Details"** section
2. Note down:
   - **FTP Hostname:** `ftpupload.net` (or your assigned hostname)
   - **FTP Username:** `if0_XXXXXXX` (your username)
   - **FTP Password:** (your password)
   - **Port:** `21`

#### 5.2 Download FileZilla

1. **Visit:** https://filezilla-project.org/
2. **Download** FileZilla Client for your OS
3. **Install** FileZilla

#### 5.3 Connect via FTP

1. **Open** FileZilla
2. **Enter credentials** at top:
   - **Host:** `ftpupload.net`
   - **Username:** `if0_XXXXXXX`
   - **Password:** `your_password`
   - **Port:** `21`
3. **Click:** "Quickconnect"

4. **Wait for connection** - You'll see:
   - **Left side:** Your local computer files
   - **Right side:** Server files

#### 5.4 Upload Files

1. **Navigate on server side** to `htdocs/` folder

2. **Navigate on local side** to your project folder

3. **Upload files:**
   - Select all files and folders from left side
   - **Drag and drop** to right side (`htdocs/` folder)
   - Wait for upload to complete (may take 10-15 minutes)

4. **Verify upload:**
   - Check that all directories are uploaded
   - Verify `vendor/` directory is uploaded (it's large)

---

## 🌐 STEP 6: Configure Database

### 6.1 Important Note

⚠️ **Your application uses PostgreSQL**, but **InfinityFree provides MySQL**.

**You have two options:**

**Option A: Use InfinityFree MySQL** (Requires code changes)
- Modify database service to use MySQL
- Update queries to MySQL syntax
- More complex

**Option B: Use External PostgreSQL** (Recommended)
- Use free PostgreSQL hosting:
  - **Neon.tech** (free tier) ✅ Recommended
  - **Supabase** (free tier)
  - **ElephantSQL** (free tier)
- Keep your existing code unchanged

### 6.2 Setup PostgreSQL (Recommended)

#### Step 1: Create Free PostgreSQL Database

1. **Go to:** https://neon.tech
2. **Sign up** for free account
3. **Create new project:**
   - Click "Create Project"
   - Choose project name
   - Select region closest to InfinityFree servers
4. **Get connection string:**
   - You'll get connection details:
     - Host
     - Port (usually 5432)
     - Database name
     - Username
     - Password

#### Step 2: Create config.php on Server

1. In **File Manager**, navigate to `htdocs/`

2. **Click "Create File"**

3. **Name it:** `config.php`

4. **Paste this content** (replace with your PostgreSQL details):

```php
<?php
/**
 * Database Configuration
 * 
 * Update with your PostgreSQL credentials
 */

return [
    // PostgreSQL Database Host
    'db_host' => 'your-neon-host.neon.tech',  // Replace with your host
    
    // PostgreSQL Database Port (default: 5432)
    'db_port' => '5432',
    
    // Database Name
    'db_name' => 'neondb',  // Replace with your database name
    
    // Database Username
    'db_user' => 'username',  // Replace with your username
    
    // Database Password
    'db_pass' => 'password',  // Replace with your password
    
    // SSL Mode (use 'require' for Neon)
    'db_ssl' => 'require',
    
    // Optional: Gemini API Key (if using AI features)
    'gemini_api_key' => null,  // Set this if you have an API key
];
```

5. **Save the file**

⚠️ **Security Note:** Never share your `config.php` file publicly!

---

## 🌐 STEP 7: Set Permissions

### 7.1 Set Data Directory Permissions

1. In **File Manager**, find **`data`** folder
2. **Right-click** on `data` folder
3. Select **"Change Permissions"**
4. **Set all checkboxes** (Read, Write, Execute):
   - ✅ Read (Owner)
   - ✅ Write (Owner)
   - ✅ Execute (Owner)
   - ✅ Read (Group)
   - ✅ Write (Group)
   - ✅ Execute (Group)
   - ✅ Read (Public)
   - ✅ Write (Public)
   - ✅ Execute (Public)
5. **Numeric value should be:** `777`
6. **Click:** "Change Permissions"

### 7.2 Set Logs Directory Permissions

1. **Right-click** on **`logs`** folder
2. **Set permissions to:** `777` (same as above)

⚠️ **Critical:** Without proper permissions, the app cannot save expenses or log errors!

---

## 🌐 STEP 8: Test Your Website

### 8.1 Visit Your Site

1. **Open your browser**
2. **Visit your URL:**
   ```
   https://yoursubdomain.rf.gd/
   ```
   or
   ```
   https://yoursubdomain.infinityfreeapp.com/
   ```

### 8.2 Initial Test Checklist

✅ **Page loads** without errors
✅ **Login/Signup page** appears
✅ **No white screen** or error messages
✅ **Styles load correctly** (not plain HTML)

### 8.3 Create Test Account

1. **Click "Sign Up"** (if first time)
2. **Fill in:**
   - Email: `test@example.com`
   - Password: `Test123!`
   - Confirm Password: `Test123!`
3. **Click "Sign Up"**
4. **Verify:** You're redirected to dashboard

### 8.4 Test Dashboard

✅ **Statistics cards** show $0.00 (initial state)
✅ **Add expense form** is visible
✅ **All categories** appear in dropdown
✅ **Date picker** works
✅ **Chart displays** (may be empty initially)

### 8.5 Test Adding Expense

1. **Fill in form:**
   - Amount: `50.00`
   - Category: `Food & Dining`
   - Description: `Test expense`
   - Date: Today's date

2. **Click "Add Expense"**

3. **Verify:**
   - ✅ Success message appears
   - ✅ Page updates
   - ✅ Expense appears in list
   - ✅ Statistics update
   - ✅ Chart shows data

### 8.6 Test Delete Expense

1. **Click delete button** on an expense
2. **Confirm deletion**
3. **Verify:** Expense is removed and stats update

---

## 🌐 STEP 9: Enable SSL (HTTPS)

### 9.1 Install Free SSL Certificate

1. In **VistaPanel**, go to **"SSL Certificates"**
2. **Click:** "Install Free SSL"
3. **Wait 5-10 minutes** for activation
4. Your site will be accessible via `https://`

### 9.2 Enable HTTPS Redirect

1. In **File Manager**, open **`.htaccess`**
2. **Find these lines** (around line 10):
   ```apache
   # RewriteCond %{HTTPS} off
   # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```
3. **Remove the `#`** to uncomment:
   ```apache
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```
4. **Save** the file

5. **Test:** Visit `http://yoursubdomain.rf.gd`
   - Should redirect to `https://`

---

## 🐛 Troubleshooting

### Issue 1: Blank White Page

**Symptoms:** Site shows nothing, just white screen

**Solutions:**

1. **Enable error reporting temporarily:**
   - Edit `bootstrap.php`
   - Change line 19:
     ```php
     ini_set('display_errors', '1');  // Change from '0' to '1'
     ```
   - Save and refresh
   - Check what error appears

2. **Check PHP version:**
   - Create `test.php` file:
     ```php
     <?php phpinfo(); ?>
     ```
   - Upload and visit: `yoursite.com/test.php`
   - Verify PHP 7.4 or higher
   - Delete `test.php` after checking

3. **Verify file upload:**
   - Ensure `index.php` uploaded completely
   - Check file size matches original

4. **Check database connection:**
   - Verify `config.php` has correct credentials
   - Test connection using database tool

---

### Issue 2: "Configuration file not found"

**Symptoms:** Error message about missing config.php

**Solution:**
1. Make sure `config.php` exists in `htdocs/`
2. Create it from `config.example.php` template
3. Fill in your PostgreSQL credentials

---

### Issue 3: Database Connection Failed

**Symptoms:** Cannot connect to database

**Solutions:**

1. **Verify credentials in config.php:**
   - Check host, port, database name, username, password
   - Test connection using database client

2. **Check PostgreSQL hosting:**
   - Verify database is running
   - Check if database allows external connections
   - For Neon: Check connection pooling settings

3. **SSL Mode:**
   - Try changing `'db_ssl' => 'require'` to `'db_ssl' => 'prefer'`
   - Or `'db_ssl' => 'disable'` (less secure)

---

### Issue 4: Permission Denied

**Symptoms:**
- "Failed to save expense"
- "Permission denied" errors
- Cannot create files

**Solutions:**

1. **Verify folder permissions:**
   - `data/` should be `777`
   - `logs/` should be `777`

2. **Check file permissions:**
   - Files in `data/` should be writable
   - Try `666` for JSON files if `777` doesn't work

3. **Manual file creation:**
   - Create empty `data/expenses.json` via File Manager
   - Set permissions to `666`

---

### Issue 5: 500 Internal Server Error

**Symptoms:** Server error page

**Solutions:**

1. **Check error logs:**
   - In VistaPanel → "Error Log"
   - Look for PHP errors

2. **Check .htaccess:**
   - Verify `.htaccess` uploaded correctly
   - Try temporarily renaming it to `.htaccess.bak`
   - If site works, issue is in `.htaccess`

3. **Check PHP settings:**
   - In VistaPanel → "PHP Configuration"
   - Ensure PHP version is 7.4 or higher

---

### Issue 6: Styles Not Loading / Plain HTML

**Symptoms:** Site loads but looks broken

**Solutions:**

1. **Check file paths:**
   - Verify all CSS/JS files uploaded
   - Check browser console (F12) for 404 errors

2. **Check .htaccess:**
   - Ensure `.htaccess` is uploaded
   - Verify rewrite rules are correct

---

## ✅ Final Checklist

Before sharing your project:

```
✅ Account created on InfinityFree
✅ Website account created
✅ All files uploaded to htdocs/
✅ vendor/ directory uploaded
✅ config.php created with database credentials
✅ data/ folder created with 777 permissions
✅ logs/ folder created with 777 permissions
✅ SSL certificate installed
✅ HTTPS redirect enabled
✅ Test account created successfully
✅ Can add expenses
✅ Can delete expenses
✅ Statistics update correctly
✅ Chart displays data
✅ Site accessible via https://
✅ No error messages visible
✅ Mobile responsive tested
```

---

## 🎉 Congratulations!

Your Expense Tracker is now live on InfinityFree!

**Your site is:**
- ✅ Live on the internet
- ✅ Accessible 24/7
- ✅ Free to run
- ✅ Portfolio-ready
- ✅ Shareable

**Time to celebrate! 🚀**

---

## 📞 Need Help?

- **InfinityFree Forum:** https://forum.infinityfree.com/
- **Project Issues:** Check GitHub issues
- **Documentation:** See `docs/` folder in project

---

## 🔄 Updating Your Site

### To update files:

1. **Edit files locally**
2. **Upload changed files** via File Manager or FTP
3. **Replace old files** on server
4. **Test immediately** after update

### To update database:

1. **Never update database directly** unless necessary
2. **Backup first** before any changes
3. **Test on local** before deploying

---

**Last Updated:** 2025
**Version:** 1.0


