# Currency Support Guide

## Overview

The Expense Tracker now supports **30+ international currencies** with automatic formatting and localization. Users can select their preferred currency during signup or change it anytime in settings.

## Supported Currencies

The application supports the following currencies:

| Code | Currency | Symbol | Countries |
|------|----------|--------|-----------|
| USD | US Dollar | $ | United States, Ecuador, El Salvador |
| EUR | Euro | € | Germany, France, Spain, Italy, Netherlands |
| GBP | British Pound | £ | United Kingdom |
| JPY | Japanese Yen | ¥ | Japan |
| CNY | Chinese Yuan | ¥ | China |
| INR | Indian Rupee | ₹ | India |
| CAD | Canadian Dollar | CA$ | Canada |
| AUD | Australian Dollar | A$ | Australia |
| CHF | Swiss Franc | CHF | Switzerland |
| BRL | Brazilian Real | R$ | Brazil |
| MXN | Mexican Peso | MX$ | Mexico |
| KRW | South Korean Won | ₩ | South Korea |
| RUB | Russian Ruble | ₽ | Russia |
| SGD | Singapore Dollar | S$ | Singapore |
| HKD | Hong Kong Dollar | HK$ | Hong Kong |
| SEK | Swedish Krona | kr | Sweden |
| NOK | Norwegian Krone | kr | Norway |
| NZD | New Zealand Dollar | NZ$ | New Zealand |
| ZAR | South African Rand | R | South Africa |
| TRY | Turkish Lira | ₺ | Turkey |
| AED | UAE Dirham | د.إ | United Arab Emirates |
| SAR | Saudi Riyal | ﷼ | Saudi Arabia |
| THB | Thai Baht | ฿ | Thailand |
| MYR | Malaysian Ringgit | RM | Malaysia |
| IDR | Indonesian Rupiah | Rp | Indonesia |
| PHP | Philippine Peso | ₱ | Philippines |
| VND | Vietnamese Dong | ₫ | Vietnam |
| PLN | Polish Zloty | zł | Poland |
| EGP | Egyptian Pound | E£ | Egypt |
| NGN | Nigerian Naira | ₦ | Nigeria |

## Features

### 1. Automatic Currency Detection
When users sign up, the system automatically detects their likely currency based on browser locale settings. Users can still change this during signup or later in settings.

### 2. Smart Currency Formatting
Each currency is formatted according to its local conventions:
- **Decimal places**: Some currencies like JPY, KRW, and VND don't use decimals
- **Separators**: Different thousand and decimal separators (e.g., 1.000,00 vs 1,000.00)
- **Symbol position**: Symbol before or after the amount (e.g., $100 vs 100 kr)

### 3. Dashboard Display
All amounts throughout the application display in the user's selected currency:
- Total expenses
- Monthly expenses
- Average expense
- Individual transaction amounts
- Chart tooltips

### 4. Export Functionality
All export formats include currency information:
- **CSV**: Includes currency code column
- **JSON**: Includes both raw amounts and formatted amounts with currency
- **XML**: Includes currency attribute and formatted amounts
- **Excel**: Shows currency in column header
- **PDF**: Displays all amounts in user's currency

### 5. Settings Page
Users can easily change their currency preference:
- Visual currency selector with symbols and names
- Real-time search/filter functionality
- Changes apply immediately across the application

## Usage

### For Users

#### During Signup
1. Fill in your registration details
2. Select your preferred currency from the dropdown
3. The system pre-selects the currency most likely for your region

#### Changing Currency
1. Navigate to Settings (⚙️ icon in header)
2. Browse or search for your desired currency
3. Click on your preferred currency
4. Click "Save Currency" button
5. You'll be redirected to the dashboard with updated currency

### For Developers

#### Using the Currency Service

```php
use ExpenseTracker\Services\Currency;

// Get all supported currencies
$currencies = Currency::getAll();

// Format an amount
$formatted = Currency::format(1234.56, 'USD'); // Returns: $1,234.56
$formatted = Currency::format(1234.56, 'EUR'); // Returns: 1.234,56 €

// Get currency symbol
$symbol = Currency::getSymbol('INR'); // Returns: ₹

// Check if currency is valid
$isValid = Currency::isValid('USD'); // Returns: true

// Detect user's currency from browser
$detected = Currency::detectUserCurrency(); // Returns: USD, EUR, etc.

// Get currency details
$details = Currency::get('GBP');
// Returns array with: name, symbol, code, decimal_places, 
// thousands_separator, decimal_separator, symbol_position, countries
```

#### Currency in Views

```php
// In controllers, pass currency to view
$user = Auth::user();
$userCurrency = $user['currency'] ?? 'USD';

// In views, use Currency service
<?php echo \ExpenseTracker\Services\Currency::format($amount, $userCurrency); ?>
```

## Database Schema

The `users` table includes a `currency` column:

```sql
ALTER TABLE users ADD COLUMN currency VARCHAR(3) DEFAULT 'USD' NOT NULL;
```

## Migration

To add currency support to an existing installation:

```bash
php migrate_currency.php
```

This will:
1. Add the `currency` column to the `users` table
2. Set default currency (USD) for existing users
3. Display confirmation and available currencies

## Technical Details

### Currency Class Structure

Each currency in the system has:
- `name`: Full currency name
- `symbol`: Currency symbol
- `code`: ISO 4217 currency code
- `decimal_places`: Number of decimal places (0-2)
- `thousands_separator`: Character for thousands (comma, dot, space, apostrophe)
- `decimal_separator`: Character for decimals (dot or comma)
- `symbol_position`: 'before' or 'after' the amount
- `countries`: Array of countries using this currency

### Locale Detection

The system uses the `HTTP_ACCEPT_LANGUAGE` header to detect the user's language preference and suggests an appropriate currency. Mapping includes:

- `en` → USD
- `de`, `fr`, `es`, `it` → EUR
- `ja` → JPY
- `zh` → CNY
- `hi` → INR
- `pt` → BRL
- And more...

## Best Practices

1. **Always use Currency::format()** instead of hardcoding currency symbols
2. **Store amounts as numbers** in the database (not formatted strings)
3. **Pass currency code** to all formatting functions
4. **Use user's currency** from session or user object
5. **Validate currency codes** before saving to database

## FAQ

**Q: Can I add more currencies?**  
A: Yes! Edit `src/Services/Currency.php` and add entries to the `$currencies` array following the same structure.

**Q: What happens if I change currency?**  
A: The currency is only a display preference. Your stored expense amounts remain the same; they're just displayed with a different symbol and formatting.

**Q: Does the system do currency conversion?**  
A: No, currency is display-only. All amounts are stored as numbers. If you want conversion, you'd need to integrate a currency conversion API.

**Q: Can different users have different currencies?**  
A: Yes! Each user can select their own currency preference independently.

**Q: What if a user's browser locale doesn't match any currency?**  
A: The system defaults to USD.

## Future Enhancements

Potential improvements for currency support:

1. **Currency Conversion**: Integrate with exchange rate APIs
2. **Multi-Currency Support**: Allow expenses in different currencies with automatic conversion
3. **Currency History**: Track exchange rates over time
4. **Regional Formats**: More locale-specific number formatting
5. **Cryptocurrency Support**: Add BTC, ETH, etc.

## Support

For issues or questions about currency support, please check:
- The Currency Service implementation: `src/Services/Currency.php`
- Settings controller: `src/Controllers/SettingsController.php`
- Settings view: `views/settings/index.php`

---

**Last Updated**: October 2025  
**Version**: 2.0  
**License**: MIT

