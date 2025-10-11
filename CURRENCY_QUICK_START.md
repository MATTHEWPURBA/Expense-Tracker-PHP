# Currency Feature - Quick Start Guide

## 🚀 Get Started in 3 Steps

### Step 1: Run Migration
```bash
cd /Users/910219/Downloads/Expense-Tracker-PHP
php migrate_currency.php
```

### Step 2: Test the Features
1. **Visit Settings**: Navigate to `http://your-domain/settings.php`
2. **Select Currency**: Choose from 30+ currencies
3. **Save**: Your dashboard will update instantly

### Step 3: Try Different Currencies
Create a new account and see how signup detects your currency automatically!

## 💡 Quick Examples

### For Users
```
✅ Sign up → Automatically suggests USD/EUR/etc based on your location
✅ Go to Settings → Change currency anytime
✅ Dashboard → See all amounts in your currency
✅ Export → All formats include your currency
```

### For Developers
```php
// Simple usage
use ExpenseTracker\Services\Currency;

// Format an amount
echo Currency::format(1234.56, 'USD');  // $1,234.56
echo Currency::format(1234.56, 'EUR');  // 1.234,56 €
echo Currency::format(1234.56, 'INR');  // ₹1,234.56
echo Currency::format(1234, 'JPY');     // ¥1,234

// Get symbol
echo Currency::getSymbol('GBP');        // £

// In your views
$currency = Auth::user()['currency'] ?? 'USD';
echo Currency::format($expense['amount'], $currency);
```

## 🌍 Supported Currencies at a Glance

| Region | Currencies |
|--------|-----------|
| **Americas** | USD, CAD, BRL, MXN |
| **Europe** | EUR, GBP, CHF, RUB, SEK, NOK, PLN, TRY |
| **Asia-Pacific** | JPY, CNY, INR, KRW, SGD, HKD, AUD, NZD, THB, MYR, IDR, PHP, VND |
| **Middle East** | AED, SAR |
| **Africa** | ZAR, EGP, NGN |

## 🎯 Key URLs

- **Settings Page**: `/settings.php`
- **Signup with Currency**: `/signup.php`
- **Dashboard**: `/index.php` (shows your currency)

## 📱 User Flow

```
New User:
Signup → Select Currency → Dashboard with Currency

Existing User:
Dashboard → Click Settings → Change Currency → Save → Updated Dashboard
```

## 🔑 Key Features

✅ **30+ Currencies** - Major global currencies  
✅ **Auto-Detection** - Smart locale-based suggestions  
✅ **Real-Time Search** - Find your currency quickly  
✅ **Proper Formatting** - Locale-specific number formats  
✅ **All Exports** - CSV, JSON, XML, Excel, PDF  
✅ **User Preference** - Each user has their own currency  

## 🛠️ Quick Troubleshooting

**Q: Currency not showing?**  
A: Run `php migrate_currency.php` to add the column

**Q: Default currency?**  
A: System defaults to USD if not set

**Q: Can I convert between currencies?**  
A: No, currency is display-only (future enhancement)

**Q: My currency not listed?**  
A: Edit `src/Services/Currency.php` to add more

## 📖 Full Documentation

For complete details, see:
- `CURRENCY_GUIDE.md` - Full documentation
- `CURRENCY_FEATURE_SUMMARY.md` - Implementation details

---

**Ready to go! Start tracking expenses in your local currency! 🎉**

