# Currency System Architecture

## 🏗️ System Overview

```
┌─────────────────────────────────────────────────────────────┐
│                     Currency System Flow                     │
└─────────────────────────────────────────────────────────────┘

┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│   Browser    │      │  Database    │      │   Session    │
│   Locale     │─────▶│   (users)    │◀────▶│   Storage    │
│  Detection   │      │   currency   │      │              │
└──────────────┘      └──────────────┘      └──────────────┘
       │                     │                      │
       │                     │                      │
       ▼                     ▼                      ▼
┌─────────────────────────────────────────────────────────────┐
│                    Currency Service                          │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  • 30+ Currency Definitions                          │  │
│  │  • Formatting Rules (decimals, separators, symbols)  │  │
│  │  • Validation & Detection                            │  │
│  │  • Helper Methods                                    │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
       │                     │                      │
       ├─────────────────────┼──────────────────────┤
       ▼                     ▼                      ▼
┌─────────────┐     ┌─────────────┐      ┌──────────────┐
│  Dashboard  │     │   Exports   │      │   Settings   │
│   Views     │     │ (CSV, JSON, │      │     Page     │
│             │     │  XML, PDF)  │      │              │
└─────────────┘     └─────────────┘      └──────────────┘
```

## 📦 Component Architecture

### 1. **Data Layer**

```
┌─────────────────────────────────┐
│      Database (PostgreSQL)      │
├─────────────────────────────────┤
│  users table:                   │
│  ├─ id (int)                    │
│  ├─ name (varchar)              │
│  ├─ email (varchar)             │
│  ├─ password (varchar)          │
│  ├─ currency (varchar(3)) ◀──── NEW
│  ├─ created_at (timestamp)      │
│  └─ updated_at (timestamp)      │
└─────────────────────────────────┘
```

### 2. **Service Layer**

```
┌─────────────────────────────────────────────┐
│      Currency Service                       │
├─────────────────────────────────────────────┤
│  Static Methods:                            │
│  ├─ getAll(): array                         │
│  ├─ get(code): ?array                       │
│  ├─ format(amount, code): string            │
│  ├─ getSymbol(code): string                 │
│  ├─ isValid(code): bool                     │
│  ├─ detectUserCurrency(): string            │
│  └─ getByCountry(country): ?string          │
└─────────────────────────────────────────────┘
         │
         └──▶ Used by all controllers & views
```

### 3. **Controller Layer**

```
┌─────────────────────────┐  ┌─────────────────────────┐
│   ExpenseController     │  │   SettingsController    │
├─────────────────────────┤  ├─────────────────────────┤
│ • getDashboardData()    │  │ • getSettingsData()     │
│ • addExpense()          │  │ • updateCurrency()      │
│ • deleteExpense()       │  └─────────────────────────┘
│                         │
│ Uses Currency::format() │  ┌─────────────────────────┐
└─────────────────────────┘  │   ExportController      │
                              ├─────────────────────────┤
┌─────────────────────────┐  │ • exportCSV()           │
│   AuthController        │  │ • exportJSON()          │
├─────────────────────────┤  │ • exportXML()           │
│ • register()            │  │ • exportExcel()         │
│   (with currency)       │  │ • exportPDF()           │
└─────────────────────────┘  │                         │
                              │ All use user's currency │
                              └─────────────────────────┘
```

### 4. **View Layer**

```
┌──────────────────────────────────────────────────────┐
│                   View Templates                     │
├──────────────────────────────────────────────────────┤
│                                                      │
│  ┌────────────────┐  ┌──────────────────────────┐  │
│  │   Dashboard    │  │      Settings Page       │  │
│  │   (index.php)  │  │   (settings/index.php)   │  │
│  ├────────────────┤  ├──────────────────────────┤  │
│  │ • Stats Cards  │  │ • Currency Selector      │  │
│  │ • Add Form     │  │ • Search/Filter          │  │
│  │ • Expense List │  │ • Account Info           │  │
│  │ • Chart        │  │ • Save Button            │  │
│  └────────────────┘  └──────────────────────────┘  │
│                                                      │
│  ┌────────────────┐  ┌──────────────────────────┐  │
│  │  Signup Page   │  │    Export Templates      │  │
│  │  (signup.php)  │  │   (exports/pdf.php)      │  │
│  ├────────────────┤  ├──────────────────────────┤  │
│  │ • User Info    │  │ • PDF Report             │  │
│  │ • Currency     │  │ • CSV Output             │  │
│  │   Dropdown     │  │ • JSON/XML Format        │  │
│  └────────────────┘  └──────────────────────────┘  │
└──────────────────────────────────────────────────────┘
```

## 🔄 User Interaction Flows

### Flow 1: New User Signup
```
User opens signup page
         │
         ▼
System detects browser locale
         │
         ▼
Suggests appropriate currency (e.g., EUR for German browser)
         │
         ▼
User selects/confirms currency
         │
         ▼
Submits form → AuthController → Auth Service
         │
         ▼
Currency validated & stored in database
         │
         ▼
User account created with currency preference
```

### Flow 2: Change Currency in Settings
```
User clicks Settings button
         │
         ▼
SettingsController loads all currencies
         │
         ▼
View displays currency grid with search
         │
         ▼
User searches/selects new currency
         │
         ▼
Clicks Save → AJAX request → SettingsController
         │
         ▼
Validates currency → Updates database & session
         │
         ▼
Redirects to dashboard with new currency
```

### Flow 3: Display Expense Amounts
```
Dashboard loads
         │
         ▼
ExpenseController fetches user & expenses
         │
         ▼
Gets user's currency from database
         │
         ▼
Passes to view: $userCurrency = 'EUR'
         │
         ▼
View calls Currency::format($amount, 'EUR')
         │
         ▼
Currency Service formats with Euro rules:
  • Symbol: €
  • Decimal separator: ,
  • Thousands separator: .
  • Symbol position: after
         │
         ▼
Displays: "1.234,56 €"
```

### Flow 4: Export with Currency
```
User clicks Export (CSV/JSON/etc)
         │
         ▼
ExportController gets user's currency
         │
         ▼
Fetches expense data
         │
         ▼
For each expense:
  Formats amount using Currency::format()
         │
         ▼
Includes currency code in export metadata
         │
         ▼
Generates file with proper currency formatting
         │
         ▼
User downloads file with their currency
```

## 🎨 Currency Data Structure

```php
'EUR' => [
    'name' => 'Euro',                    // Full name
    'symbol' => '€',                     // Display symbol
    'code' => 'EUR',                     // ISO 4217 code
    'decimal_places' => 2,               // Number of decimals
    'thousands_separator' => '.',        // Thousands sep
    'decimal_separator' => ',',          // Decimal sep
    'symbol_position' => 'after',        // Symbol placement
    'countries' => [                     // Using countries
        'Germany', 
        'France', 
        'Spain'
    ]
]
```

## 🔐 Security Architecture

```
┌─────────────────────────────────────────┐
│         Security Layers                 │
├─────────────────────────────────────────┤
│                                         │
│  1. Authentication Required             │
│     └─ AuthMiddleware checks session   │
│                                         │
│  2. Input Validation                    │
│     └─ Currency::isValid()             │
│                                         │
│  3. SQL Injection Protection            │
│     └─ Prepared statements in Model    │
│                                         │
│  4. XSS Protection                      │
│     └─ htmlspecialchars() in views     │
│                                         │
│  5. User Isolation                      │
│     └─ Users can only modify own data  │
│                                         │
└─────────────────────────────────────────┘
```

## 📊 Performance Considerations

```
┌────────────────────────────────────────┐
│     Performance Optimizations          │
├────────────────────────────────────────┤
│                                        │
│  ✅ Static currency array              │
│     (No database queries)              │
│                                        │
│  ✅ Session caching                    │
│     (User currency stored in session)  │
│                                        │
│  ✅ No external API calls              │
│     (All formatting done locally)      │
│                                        │
│  ✅ Minimal database queries           │
│     (Currency fetched with user)       │
│                                        │
└────────────────────────────────────────┘
```

## 🧩 Integration Points

```
Currency Service integrates with:

┌─────────────────┐
│  User Model     │─┐
├─────────────────┤ │
│ • getCurrency() │ │    ┌──────────────────┐
│ • updateCurr()  │ ├───▶│  Currency        │
└─────────────────┘ │    │  Service         │
                    │    │                  │
┌─────────────────┐ │    │  • format()      │
│  Auth Service   │─┤    │  • getSymbol()   │
├─────────────────┤ │    │  • validate()    │
│ • register()    │ │    │  • detect()      │
│ • login()       │ │    └──────────────────┘
└─────────────────┘ │           │
                    │           │
┌─────────────────┐ │           │
│  All Views      │─┤           │
├─────────────────┤ │           │
│ • Dashboard     │ ├───────────┘
│ • Exports       │ │
│ • PDF Reports   │ │
└─────────────────┘ │
                    │
┌─────────────────┐ │
│  Settings Page  │─┘
├─────────────────┤
│ • Currency list │
│ • Update form   │
└─────────────────┘
```

## 🎯 Design Principles

1. **Separation of Concerns**
   - Currency logic in dedicated service
   - Controllers coordinate, don't format
   - Views only display, don't calculate

2. **Single Responsibility**
   - Currency Service: Only currency operations
   - User Model: Only user data
   - Settings Controller: Only settings management

3. **DRY (Don't Repeat Yourself)**
   - All formatting through Currency::format()
   - Single source of truth for currency data
   - Reusable across entire application

4. **Open/Closed Principle**
   - Easy to add new currencies
   - Existing code doesn't need modification
   - Extension through configuration

5. **Dependency Injection**
   - Currency passed as parameter
   - No hard-coded currencies in views
   - Testable and flexible

## 📝 Future Architecture Considerations

```
Potential Enhancements:

┌─────────────────────────────────────┐
│  Currency Conversion API            │
│  ├─ External service integration    │
│  ├─ Rate caching                    │
│  └─ Historical rates                │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  Multi-Currency Expenses            │
│  ├─ Track currency per expense      │
│  ├─ Automatic conversion            │
│  └─ Mixed-currency reports          │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  Advanced Localization              │
│  ├─ Full i18n support               │
│  ├─ Date/time formatting            │
│  └─ Number formatting preferences   │
└─────────────────────────────────────┘
```

---

**Architecture**: Clean, Scalable, Maintainable  
**Status**: Production Ready  
**Last Updated**: October 2025

