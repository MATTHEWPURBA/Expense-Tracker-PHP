# ⚡ Quick Start Guide - Expense Tracker

Get your Expense Tracker running in **5 minutes**!

---

## 🎯 Method 1: Local Testing (Fastest)

### Prerequisites
- PHP 7.4+ installed
- Terminal/Command Prompt access

### Steps

```bash
# 1. Navigate to project folder
cd /path/to/expense-tracker

# 2. Configure database (first time only)
cp config.example.php config.php
# Edit config.php with your database credentials

# 3. Start PHP server
php -S localhost:8000

# 4. Open browser
# Visit: http://localhost:8000
```

**That's it!** ✅

The application will automatically:
- Connect to PostgreSQL database
- Create necessary tables
- Insert default categories
- Be ready to use

> **🔒 Security Tip:** The `config.php` file contains your database credentials and is automatically excluded from Git via `.gitignore`

---

## 🌐 Method 2: Free Online Hosting

### For Portfolio Showcase (FREE Forever)

#### Option A: InfinityFree (Recommended)

**Time needed: 10-15 minutes**

1. **Sign Up** (2 min)
   - Go to: https://infinityfree.com
   - Create free account
   - Verify email

2. **Create Website** (3 min)
   - Click "Create Account"
   - Choose subdomain: `yourname.rf.gd`
   - Wait for activation (2-5 minutes)

3. **Upload Files** (5 min)
   - Open File Manager in control panel
   - Go to `htdocs/` folder
   - Upload:
     - `index.php`
     - `config.php` (with your credentials)
     - `.htaccess`
     - `README.md`
   - Set permissions to `777`

4. **Done!** 🎉
   - Visit: `https://yourname.rf.gd`
   - Your app is live!

#### Option B: 000WebHost

**Time needed: 10 minutes**

1. Sign up at: https://www.000webhost.com
2. Create website with free domain
3. Upload files to `public_html/`
4. Set `data/` folder permissions to `777`
5. Visit your site!

---

## 📝 First Time Setup

### 1. Add Your First Expense

```
Amount: 50.00
Category: 🍔 Food & Dining
Description: Lunch at restaurant
Date: [Today's date]
```

Click **"Add Expense"** ✅

### 2. Verify It Works

You should see:
- ✅ Success message
- ✅ Expense in the list
- ✅ Statistics updated
- ✅ Chart showing data

### 3. Test Other Features

**Delete an expense:**
- Click "🗑️ Delete" button
- Confirm deletion
- Verify it's removed

**Export data:**
- Click "📥 Export to CSV"
- Download CSV file
- Open in Excel/Google Sheets

---

## 🔧 Common Quick Fixes

### Issue: Can't save expenses

**Fix:**
```bash
# Set correct permissions
chmod 777 data/
```

### Issue: Blank page

**Fix:**
```php
// Add to top of index.php temporarily:
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check for errors, then remove
```

### Issue: Chart not showing

**Fix:**
- Check internet connection
- Chart.js loads from CDN
- Open browser console (F12) for errors

---

## 🎨 Customize (Optional)

### Change Theme Color

Edit `index.php`, find:
```css
:root {
    --primary: #667eea;    /* Change this color */
    --secondary: #764ba2;  /* And this one */
}
```

### Add New Category

Edit `index.php`, find `$defaultCategories` array:
```php
['id' => 'groceries', 'name' => 'Groceries', 'color' => '#FF6384', 'icon' => '🛒']
```

---

## 📱 Share Your Project

### 1. Update README
Replace these:
- `yourusername` → Your GitHub username
- `Your Name` → Your actual name
- `your.email@example.com` → Your email

### 2. Push to GitHub

```bash
git init
git add .
git commit -m "Initial commit - Expense Tracker"
git remote add origin https://github.com/yourusername/expense-tracker-php.git
git push -u origin main
```

### 3. Add to Portfolio

```html
<a href="https://yourname.rf.gd">🚀 Live Demo</a>
<a href="https://github.com/yourusername/expense-tracker-php">📂 Code</a>
```

---

## ✅ 5-Minute Checklist

```
[ ] Project downloaded/cloned
[ ] PHP server running OR uploaded to hosting
[ ] Application loads in browser
[ ] Can add expenses
[ ] Can delete expenses
[ ] Can export CSV
[ ] Chart displays correctly
[ ] Mobile responsive works
```

---

## 🎯 Next Steps

1. ✅ **Get it working** - You are here!
2. 📸 **Take screenshots** - For your portfolio
3. 📝 **Update README** - Add your info
4. 🚀 **Deploy online** - For showcase
5. 💼 **Add to portfolio** - Show employers
6. 🌟 **Share on LinkedIn** - Get visibility

---

## 📚 More Detailed Guides

- [Full Deployment Guide](DEPLOYMENT.md) - Step-by-step hosting instructions
- [Main README](README.md) - Complete documentation
- [Troubleshooting](#) - Common issues and solutions

---

## 🤝 Need Help?

- **Can't get it working?** Open an issue on GitHub
- **Want to add features?** Check the roadmap in README
- **Questions?** Read the full documentation

---

<div align="center">

**Ready to code! 🚀**

[Main Documentation](README.md) | [Deployment Guide](DEPLOYMENT.md)

Made with ❤️ for learning and portfolio building

</div>


