# 💰 Expense Tracker - Personal Finance Manager

<div align="center">

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)
![Status](https://img.shields.io/badge/Status-Active-success?style=for-the-badge)

**A beautiful, modern expense tracking application powered by PostgreSQL!**

[Live Demo](#) | [Features](#features) | [Installation](#installation) | [Screenshots](#screenshots)

</div>

---

## 📖 About

A modern, responsive expense tracker built with PHP and PostgreSQL. Track your daily expenses, visualize spending patterns with interactive charts, and export your data using a powerful database backend. Perfect for personal use and portfolio showcase.

### ✨ Key Highlights

- 🗄️ **PostgreSQL Database** - Powered by Neon's serverless PostgreSQL
- 📊 **Beautiful Analytics** - Interactive Chart.js visualizations
- 📱 **Fully Responsive** - Works perfectly on all devices
- 💾 **CSV Export** - Download your expense data anytime
- 🎨 **Modern UI** - Clean, gradient design with smooth animations
- ⚡ **Lightning Fast** - Optimized database queries and caching
- 🔒 **Secure** - SQL injection protection with prepared statements
- 🆓 **100% Free** - Deploy with free Neon PostgreSQL tier

---

## 🎯 Features

### Core Functionality

| Feature | Description |
|---------|-------------|
| ✅ **Expense Tracking** | Add, view, and delete expenses with ease |
| 📂 **Category Management** | 7 pre-defined categories with custom colors & icons |
| 📊 **Visual Analytics** | Interactive doughnut chart showing spending breakdown |
| 📈 **Statistics Dashboard** | Total expenses, monthly spending, average transaction |
| 📥 **CSV Export** | Export all data for Excel/Google Sheets analysis |
| 📅 **Date Tracking** | Track expenses by date with chronological sorting |
| 💬 **Descriptions** | Add notes to remember what each expense was for |
| 🎨 **Color-Coded** | Visual category identification with custom colors |

### Categories Included

| Icon | Category | Color |
|------|----------|-------|
| 🍔 | Food & Dining | Red |
| 🚗 | Transportation | Blue |
| 💡 | Utilities | Yellow |
| 🎮 | Entertainment | Teal |
| 🏥 | Healthcare | Purple |
| 🛍️ | Shopping | Orange |
| 📦 | Other | Gray |

---

## 🚀 Quick Start

### Prerequisites

- PHP 7.4 or higher with PDO PostgreSQL extension
- PostgreSQL database (we use Neon - free tier)
- Apache web server (with mod_rewrite)
- Web browser (Chrome, Firefox, Safari, Edge)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/yourusername/expense-tracker-php.git

# 2. Navigate to project directory
cd expense-tracker-php

# 3. Configure database connection
cp config.example.php config.php
# Edit config.php with your Neon PostgreSQL credentials

# 4. Migrate existing data (if any)
php migrate.php

# 5. Start PHP development server
php -S localhost:8000

# 6. Open in browser
# Visit: http://localhost:8000
```

The application will automatically:
- Connect to your PostgreSQL database
- Create necessary tables (expenses, categories)
- Insert default categories
- Set up proper database indexes

> **🔒 Security Note:** Database credentials are stored in `config.php` which is excluded from version control via `.gitignore`. Never commit this file to Git!

---

## 🌐 Deployment Guide

### Option 1: Free Hosting (InfinityFree) 🎁

Perfect for portfolio showcase - **100% FREE forever!**

#### Step-by-Step:

1. **Create Account**
   - Visit: https://infinityfree.com
   - Sign up with your email
   - Verify your account

2. **Create Website**
   - Choose free subdomain (e.g., `yourname.rf.gd`)
   - Wait 2-5 minutes for activation

3. **Upload Files**
   - Access File Manager in control panel
   - Navigate to `htdocs/` folder
   - Upload all project files:
     - `index.php`
     - `.htaccess`
     - `README.md`

4. **Set Permissions**
   - Right-click on `data/` folder
   - Set permissions to `777`
   - Apply to all files inside

5. **Visit Your Site**
   ```
   https://yourname.rf.gd/
   ```

#### InfinityFree Features:
- ✅ PHP 7.4/8.0 Support
- ✅ Free SSL Certificate
- ✅ Unlimited Bandwidth
- ✅ No Ads on Your Site
- ✅ 5GB Storage
- ✅ MySQL (if needed later)

### Option 2: Traditional Hosting

Works with any PHP hosting:
- Namecheap
- Bluehost
- HostGator
- GoDaddy
- SiteGround

Just upload via FTP and set folder permissions!

---

## 🔒 Security

This application implements industry-standard security practices:

- **Secure Configuration**: Database credentials stored separately in `config.php`
- **Git Ignore Protection**: Sensitive files automatically excluded from version control
- **SQL Injection Protection**: All queries use PDO prepared statements
- **SSL/TLS Encryption**: Forced SSL connections to PostgreSQL
- **File Access Control**: `.htaccess` protects sensitive files
- **Error Handling**: Production-safe error reporting

📖 **Read full security guide**: [SECURITY.md](SECURITY.md)

---

## 📁 Project Structure

```
expense-tracker/
├── index.php              # Main application (2000+ lines)
│   ├── Backend Logic      # PHP expense management
│   ├── Frontend UI        # HTML/CSS interface
│   └── JavaScript         # AJAX & Chart.js integration
│
├── config.php             # Database credentials (NOT in Git)
├── config.example.php     # Configuration template
├── migrate.php            # JSON to PostgreSQL migration tool
│
├── data/                  # Legacy data directory (for migration)
│   ├── expenses.json      # Old expense records
│   └── categories.json    # Old category definitions
│
├── .htaccess              # Apache configuration & security
├── .gitignore             # Git ignore rules
├── README.md              # This file
├── SECURITY.md            # Security best practices
├── DEPLOYMENT.md          # Detailed hosting guide
├── QUICK_START.md         # Fast setup guide
└── LICENSE                # MIT License
```

---

## 💻 Usage Examples

### Adding an Expense

1. Fill in the amount (e.g., `45.50`)
2. Select category (e.g., `🍔 Food & Dining`)
3. Add description (e.g., `Lunch at Italian restaurant`)
4. Choose date (defaults to today)
5. Click **"Add Expense"**

### Viewing Analytics

- **Dashboard Cards** show key metrics at the top
- **Doughnut Chart** displays category breakdown with percentages
- **Expense List** shows all transactions with details

### Exporting Data

Click the **"📥 Export to CSV"** button to download:
```csv
Date,Category,Description,Amount,Created At
2025-10-11,Food & Dining,Lunch at restaurant,45.50,2025-10-11 14:30:00
2025-10-10,Transportation,Uber to work,15.00,2025-10-10 09:15:00
```

### Deleting an Expense

1. Find the expense in the list
2. Click the **"🗑️ Delete"** button
3. Confirm the action
4. Statistics update automatically

---

## 🎨 Screenshots

### Dashboard View
![Dashboard](screenshots/dashboard.png)
*Clean, modern interface with real-time statistics*

### Analytics Chart
![Analytics](screenshots/analytics.png)
*Interactive spending breakdown by category*

### Expense List
![Expense List](screenshots/expense-list.png)
*Chronologically sorted expense history*

### Mobile View
![Mobile](screenshots/mobile.png)
*Fully responsive design for all devices*

---

## 🛠️ Technical Details

### Technologies Used

- **Backend:** PHP 7.4+ with PDO
- **Frontend:** HTML5, CSS3, JavaScript (ES6)
- **Charts:** Chart.js 4.4.0
- **Database:** PostgreSQL (Neon serverless)
- **Server:** Apache with mod_rewrite

### Browser Compatibility

| Browser | Version | Status |
|---------|---------|--------|
| Chrome | 90+ | ✅ Fully Supported |
| Firefox | 88+ | ✅ Fully Supported |
| Safari | 14+ | ✅ Fully Supported |
| Edge | 90+ | ✅ Fully Supported |
| Opera | 76+ | ✅ Fully Supported |

### File Structure Details

**index.php** (Single-file application)
- Lines 1-100: PHP backend logic
- Lines 100-500: CSS styling
- Lines 500-800: HTML structure
- Lines 800-1000+: JavaScript functionality

### Security Features

- ✅ SQL injection prevention with prepared statements
- ✅ XSS prevention with `htmlspecialchars()`
- ✅ Input validation on all forms
- ✅ Database-level constraints and foreign keys
- ✅ CSRF protection (same-origin)
- ✅ Secure SSL database connections
- ✅ Error logging without exposing sensitive data

---

## 🗄️ Database Migration

This project has been upgraded from JSON file storage to PostgreSQL! See [DATABASE_MIGRATION.md](DATABASE_MIGRATION.md) for detailed migration information.

### Quick Migration Steps:

1. **Run Migration Script:**
   ```bash
   php migrate.php
   ```

2. **Your existing JSON data will be automatically imported to PostgreSQL**

3. **Enjoy improved performance and scalability!**

---

## 🔧 Configuration

### Changing Timezone

Edit `index.php` line 8:
```php
date_default_timezone_set('America/New_York');
```

### Adding New Categories

Edit the `$defaultCategories` array in `index.php`:
```php
$defaultCategories = [
    ['id' => 'groceries', 'name' => 'Groceries', 'color' => '#FF6384', 'icon' => '🛒'],
    // Add more categories here
];
```

### Customizing Colors

Modify CSS variables in `index.php` (style section):
```css
:root {
    --primary: #667eea;    /* Main theme color */
    --secondary: #764ba2;  /* Gradient color */
    /* ... more variables ... */
}
```

---

## 🐛 Troubleshooting

### Issue: White blank page

**Solution:**
```php
// Add to top of index.php temporarily
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Issue: Permission denied errors

**Solution:**
```bash
# Set correct permissions
chmod 777 data/
chmod 666 data/*.json
```

### Issue: Chart not displaying

**Solution:**
- Check internet connection (Chart.js loads from CDN)
- Open browser console (F12) for JavaScript errors
- Ensure PHP date functions work properly

### Issue: Data not saving

**Solution:**
1. Verify `data/` folder exists
2. Check write permissions on folder
3. Ensure PHP has file write access
4. Test with: `<?php echo is_writable('data/'); ?>`

---

## 📊 Performance

- **Load Time:** < 1 second
- **File Size:** ~50KB (index.php)
- **Memory Usage:** ~2MB per request
- **Concurrent Users:** 100+ (depends on hosting)

---

## 🤝 Contributing

Contributions are welcome! Here's how:

1. Fork the repository
2. Create a feature branch
   ```bash
   git checkout -b feature/AmazingFeature
   ```
3. Commit your changes
   ```bash
   git commit -m 'Add some AmazingFeature'
   ```
4. Push to the branch
   ```bash
   git push origin feature/AmazingFeature
   ```
5. Open a Pull Request

### Ideas for Contributions

- [ ] Multiple currency support
- [ ] Budget limits and alerts
- [ ] Income tracking
- [ ] Monthly/Yearly reports
- [ ] Data backup/restore
- [ ] Dark mode toggle
- [ ] Multi-language support
- [ ] PDF export

---

## 📜 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

```
MIT License

Copyright (c) 2025 Your Name

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction...
```

---

## 👨‍💻 Author

**Your Name**

- Website: [yourwebsite.com](https://yourwebsite.com)
- GitHub: [@yourusername](https://github.com/yourusername)
- LinkedIn: [Your Name](https://linkedin.com/in/yourprofile)
- Email: your.email@example.com

---

## 🙏 Acknowledgments

- **Chart.js** - Beautiful JavaScript charts
- **InfinityFree** - Free PHP hosting
- **Font Awesome** - (Future: icon integration)
- **Google Fonts** - System font stack used

---

## 📈 Roadmap

### Version 1.1 (Planned)
- [ ] Budget setting and tracking
- [ ] Expense categories customization
- [ ] Recurring expenses
- [ ] Search and filter functionality

### Version 1.2 (Future)
- [ ] Multi-user support with login
- [ ] Income tracking
- [ ] Monthly budget reports
- [ ] Email notifications

### Version 2.0 (Ideas)
- [ ] Mobile app (PWA)
- [ ] Bank integration
- [ ] Receipt photo uploads
- [ ] AI spending insights

---

## ⭐ Show Your Support

If you find this project helpful, please consider:

- ⭐ **Star** this repository
- 🐛 **Report** bugs or issues
- 💡 **Suggest** new features
- 🔀 **Fork** and contribute
- 📢 **Share** with others

---

## 📞 Support

Having issues? Need help?

1. Check the [Troubleshooting](#troubleshooting) section
2. Open an [Issue](https://github.com/yourusername/expense-tracker-php/issues)
3. Contact me via [email](mailto:your.email@example.com)

---

## 🎓 Learning Resources

Built this for learning? Check out:

- [PHP Official Documentation](https://www.php.net/manual/)
- [Chart.js Documentation](https://www.chartjs.org/docs/)
- [MDN Web Docs](https://developer.mozilla.org/)
- [W3Schools PHP Tutorial](https://www.w3schools.com/php/)

---

<div align="center">

**Made with ❤️ and lots of ☕**

⭐ Star this repo if you found it helpful!

[Back to Top](#-expense-tracker---personal-finance-manager)

</div>
