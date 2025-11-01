# 📦 Files to Deploy - Quick Reference

Complete list of files and directories needed for InfinityFree deployment.

---

## ✅ Files to Upload

### Core PHP Files
```
index.php
login.php
signup.php
logout.php
bootstrap.php
api.php
settings.php
.htaccess
```

### Configuration Files
```
config.example.php    (for reference only)
composer.json
composer.lock         (if exists)
```

### Required Directories
```
src/
  ├── Controllers/
  │   ├── AIController.php
  │   ├── AuthController.php
  │   ├── ExpenseController.php
  │   ├── ExportController.php
  │   └── SettingsController.php
  ├── Middleware/
  │   └── AuthMiddleware.php
  ├── Models/
  │   ├── Category.php
  │   ├── Expense.php
  │   ├── Model.php
  │   └── User.php
  ├── Router/
  │   ├── ApiResponse.php
  │   └── Router.php
  └── Services/
      ├── AIService.php
      ├── Auth.php
      ├── Currency.php
      ├── Database.php
      ├── LoginLogger.php
      └── RequestLogger.php

views/
  ├── auth/
  │   ├── login.php
  │   └── signup.php
  ├── dashboard/
  │   └── index.php
  ├── exports/
  │   └── pdf.php
  ├── layouts/
  │   ├── auth-styles.php
  │   └── dashboard-styles.php
  └── settings/
      └── index.php

js/
  ├── api-client.js
  └── dashboard-api.js

vendor/                (IMPORTANT: Composer dependencies)
  ├── autoload.php
  ├── composer/
  └── [installed packages]
```

---

## 📁 Directories to Create on Server

### Create These Folders (Empty)
```
data/          (create empty, set to 777)
logs/          (create empty, set to 777)
```

---

## ❌ Files NOT to Upload

### Local Development Scripts
```
serve
manage.sh
setup.sh
check
stop
show-logs
```

### Git Files
```
.git/
.gitignore    (optional, can upload)
.gitattributes (if exists)
```

### Documentation (Optional)
```
*.md          (documentation files - optional)
docs/         (optional)
```

### Configuration (Create Fresh on Server)
```
config.php    ❌ NEVER upload - create fresh on server
```

### IDE Files
```
.vscode/
.idea/
*.sublime-project
*.sublime-workspace
```

### System Files
```
.DS_Store
Thumbs.db
*.swp
*.bak
*.backup
```

### Local Data
```
data/*.json   (don't upload your local data)
logs/*.log    (don't upload local logs)
```

---

## 📋 Upload Order (Recommended)

### Step 1: Core Files
1. `.htaccess`
2. `bootstrap.php`
3. `index.php`
4. `login.php`
5. `signup.php`
6. `logout.php`
7. `api.php`
8. `settings.php`

### Step 2: Configuration
1. `config.example.php`
2. `composer.json`
3. `composer.lock` (if exists)

### Step 3: Directories
1. `src/` (entire directory structure)
2. `views/` (entire directory structure)
3. `js/` (all JavaScript files)
4. `vendor/` (entire directory - may take several minutes)

### Step 4: Create on Server
1. Create `data/` folder (empty)
2. Create `logs/` folder (empty)
3. Set permissions: both folders to `777`

### Step 5: Configuration
1. Create `config.php` on server
2. Copy content from `config.example.php`
3. Fill in PostgreSQL credentials

---

## 📏 File Sizes (Approximate)

### Small Files (< 10KB each)
- All PHP files (index.php, login.php, etc.)
- All JavaScript files
- Configuration files

### Medium Files (10KB - 1MB)
- Most PHP class files
- View template files

### Large Directory
- `vendor/` directory: ~5-20MB (depends on dependencies)

---

## ✅ Verification Checklist

After uploading, verify:

```
✅ index.php exists in htdocs/
✅ bootstrap.php exists
✅ .htaccess exists (may be hidden)
✅ src/ directory present with all subdirectories
✅ views/ directory present with all subdirectories
✅ js/ directory present
✅ vendor/ directory present (IMPORTANT!)
✅ vendor/autoload.php exists
✅ data/ folder created
✅ logs/ folder created
✅ config.php created on server (NOT uploaded)
```

---

## 🔍 Quick Verification Commands

### Via File Manager
- Navigate through each directory
- Count files in each directory
- Verify file sizes match

### Via FTP
```bash
# Count files in src/
ls -R src/ | wc -l

# Verify vendor/ exists
ls -la vendor/autoload.php

# Check config.php exists (but not from local)
ls -la config.php
```

---

## 🚨 Critical Files

**These files MUST be uploaded correctly:**

1. **`vendor/autoload.php`** - Without this, app won't work
2. **`.htaccess`** - Required for routing and security
3. **`bootstrap.php`** - App initialization
4. **`config.php`** - Database configuration (create on server)
5. **`src/Database.php`** - Database service

**Missing any of these will cause the app to fail!**

---

## 💡 Tips

1. **Upload `vendor/` last** - It's the largest and may take time
2. **Verify `.htaccess`** - Hidden files can be missed
3. **Double-check `config.php`** - Never upload local version
4. **Test after each major step** - Don't wait until everything is uploaded
5. **Keep a list** - Check off files as you upload them

---

## 📞 Need Help?

If you're missing files:
1. Check your local project directory
2. Run `composer install` locally if `vendor/` is missing
3. Verify all files are in correct directories
4. Check `.gitignore` - some files might be intentionally excluded

---

**Last Updated:** 2025
**Version:** 1.0


