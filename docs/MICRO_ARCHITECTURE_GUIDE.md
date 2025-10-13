# 🏗️ Micro Architecture Implementation Guide

## What Changed?

Your Expense Tracker has been completely restructured into a professional micro-architecture that is **much easier to read, maintain, and extend**.

## Before vs After

### Before (Monolithic)
```
❌ All code in one file (1400+ lines)
❌ Repeated database connections
❌ Mixed HTML, PHP, and SQL
❌ Hard to find specific functionality
❌ Difficult to test
❌ Security vulnerabilities scattered
```

### After (Micro-Architecture)
```
✅ Organized into logical components
✅ Single database connection (Singleton)
✅ Separated presentation from logic
✅ Clear file structure
✅ Easy to unit test
✅ Centralized security
```

## 📂 New Structure Overview

```
Your Project/
│
├── bootstrap.php          ← Starts everything
├── index.php              ← Dashboard entry (clean!)
├── login.php              ← Login entry (clean!)
├── signup.php             ← Signup entry (clean!)
│
├── src/                   ← All your PHP classes
│   ├── Controllers/       ← Handle requests
│   ├── Models/            ← Database operations
│   ├── Middleware/        ← Security checks
│   └── Services/          ← Business logic
│
└── views/                 ← All your HTML/CSS
    ├── dashboard/
    ├── auth/
    └── layouts/
```

## 🎯 Key Benefits

### 1. **Easier to Read**
Instead of scrolling through 1400 lines, you now have:
- Small, focused files (50-200 lines each)
- Clear naming: `ExpenseController` does expense things
- One responsibility per file

### 2. **Easier to Modify**
Want to change the login form? → `views/auth/login.php`  
Want to change login logic? → `src/Controllers/AuthController.php`  
Want to change database queries? → `src/Models/User.php`

### 3. **Easier to Add Features**
Adding a new feature follows a pattern:
```
1. Create Model (if needed)
2. Create Controller
3. Create View
4. Add entry point
```

### 4. **Professional Code**
- Follows PSR-4 autoloading standard
- Uses design patterns (Singleton, MVC, Service Layer)
- Includes security best practices
- Ready for team collaboration

## 🚀 How It Works

### Example: User Logs In

**Old Way (Monolithic):**
```php
// Everything in login.php (500+ lines)
session_start();
$pdo = new PDO(...); // Database connection
if ($_POST) {
    // Validation code
    // Query code
    // Session code
    // Redirect code
}
// HTML starts here...
```

**New Way (Micro-Architecture):**
```php
// login.php (clean entry point)
require_once __DIR__ . '/bootstrap.php';

use ExpenseTracker\Controllers\AuthController;
use ExpenseTracker\Middleware\AuthMiddleware;

$authMiddleware = new AuthMiddleware();
$authMiddleware->handleGuest();

$authController = new AuthController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $authController->login();
    // Handle result
}

require_once __DIR__ . '/views/auth/login.php';
```

**What Happens Behind the Scenes:**
1. `bootstrap.php` sets everything up
2. `AuthMiddleware` checks if already logged in
3. `AuthController` handles the login
4. `Auth` service validates credentials
5. `User` model queries database
6. View renders the form

**Result:** 
- Entry point is **43 lines** vs 500+ lines
- Each component is focused and testable
- Easy to understand flow

## 📚 Component Guide

### Controllers (`src/Controllers/`)

**What they do:** Handle web requests and return responses

**Example:**
```php
class ExpenseController {
    public function addExpense() {
        // 1. Get user input
        // 2. Validate
        // 3. Use model to save
        // 4. Return response
    }
}
```

**When to use:** Need to handle a web request

---

### Models (`src/Models/`)

**What they do:** Talk to the database

**Example:**
```php
class Expense extends Model {
    public function getByUser($userId) {
        // SQL query to get user's expenses
    }
}
```

**When to use:** Need to read/write database

---

### Services (`src/Services/`)

**What they do:** Reusable business logic

**Example:**
```php
class Auth {
    public static function login($username, $password) {
        // Authentication logic
    }
}
```

**When to use:** Logic used by multiple controllers

---

### Middleware (`src/Middleware/`)

**What they do:** Check requests before they reach controllers

**Example:**
```php
class AuthMiddleware {
    public function handle() {
        if (!Auth::check()) {
            redirect('/login.php');
        }
    }
}
```

**When to use:** Need to check every request (auth, permissions, etc.)

---

### Views (`views/`)

**What they do:** Display HTML

**Example:**
```php
<!-- views/dashboard/index.php -->
<h1>Welcome, <?php echo $user['name']; ?></h1>
```

**When to use:** Displaying information to users

## 🎓 Quick Examples

### Adding a New Page

**Goal:** Add a "Settings" page

**Steps:**

1. **Create Controller** (`src/Controllers/SettingsController.php`):
```php
<?php
namespace ExpenseTracker\Controllers;

class SettingsController {
    public function show() {
        $user = Auth::user();
        return ['user' => $user];
    }
    
    public function update() {
        // Handle settings update
    }
}
```

2. **Create View** (`views/settings/index.php`):
```php
<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
</head>
<body>
    <h1>Settings for <?php echo $user['name']; ?></h1>
    <!-- Settings form -->
</body>
</html>
```

3. **Create Entry Point** (`settings.php`):
```php
<?php
require_once __DIR__ . '/bootstrap.php';

use ExpenseTracker\Controllers\SettingsController;
use ExpenseTracker\Middleware\AuthMiddleware;

$authMiddleware = new AuthMiddleware();
$authMiddleware->handle();

$controller = new SettingsController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->update();
}

$data = $controller->show();
extract($data);

require_once __DIR__ . '/views/settings/index.php';
```

Done! You have a new feature.

---

### Adding a New Database Query

**Goal:** Get expenses from last month

**Steps:**

1. **Add method to Model** (`src/Models/Expense.php`):
```php
public function getLastMonth($userId) {
    $sql = "
        SELECT * FROM expenses 
        WHERE user_id = ? 
        AND date >= DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month')
        AND date < DATE_TRUNC('month', CURRENT_DATE)
        ORDER BY date DESC
    ";
    
    return $this->query($sql, [$userId]);
}
```

2. **Use in Controller:**
```php
$expenses = $this->expenseModel->getLastMonth($userId);
```

Done! New query added safely with prepared statements.

---

## 🔍 Finding Things

### "Where is the login logic?"
→ `src/Controllers/AuthController.php` and `src/Services/Auth.php`

### "Where is the database connection?"
→ `src/Services/Database.php`

### "Where is the expense list HTML?"
→ `views/dashboard/index.php`

### "Where are expenses saved?"
→ `src/Models/Expense.php` → `create()` method

### "Where is authentication checked?"
→ `src/Middleware/AuthMiddleware.php`

### "Where are categories stored?"
→ `src/Models/Category.php`

---

## 🛠️ Common Tasks

### Change the Dashboard Layout
Edit: `views/dashboard/index.php`

### Change Dashboard CSS
Edit: `views/layouts/dashboard-styles.php`

### Add Validation to Login
Edit: `src/Services/Auth.php` → `validateRegistration()`

### Change Database Tables
Edit: `src/Services/Database.php` → `initializeTables()`

### Add New Export Format
Edit: `src/Controllers/ExportController.php` → Add new method

---

## 🎨 Design Patterns Used

### 1. **MVC (Model-View-Controller)**
Separates data (Model), presentation (View), and logic (Controller)

### 2. **Singleton**
One database connection for entire application

### 3. **Service Layer**
Business logic separated from controllers

### 4. **Middleware**
Request filtering before reaching controllers

### 5. **Active Record**
Models represent database tables

---

## 📊 File Size Comparison

| Component | Old | New | Improvement |
|-----------|-----|-----|-------------|
| Login | 423 lines | 43 lines | **90% reduction** |
| Dashboard | 1406 lines | 63 lines | **96% reduction** |
| Auth Logic | Mixed everywhere | 200 lines (Auth.php) | **Centralized** |
| Database | Repeated 5+ times | 1 Singleton | **DRY** |

---

## 🚀 Next Steps

1. **Explore the Code**
   - Start with `bootstrap.php`
   - Look at `index.php`
   - Follow the flow to controllers
   - See how models work

2. **Read Full Documentation**
   - `ARCHITECTURE.md` - Complete architecture guide
   - Comments in code files

3. **Try Adding a Feature**
   - Start small (new page)
   - Follow the patterns
   - Reference existing code

4. **Run the Application**
   ```bash
   # Install dependencies (optional)
   composer install
   
   # Start PHP server
   php -S localhost:8000
   ```

---

## 🎯 Philosophy

> "A good architecture makes the system easy to understand, easy to develop, easy to maintain, and easy to deploy." - Robert C. Martin

This micro-architecture follows the **SOLID** principles:
- **S**ingle Responsibility - Each class does one thing
- **O**pen/Closed - Easy to extend, no need to modify
- **L**iskov Substitution - Models are interchangeable
- **I**nterface Segregation - Small, focused interfaces
- **D**ependency Inversion - Depend on abstractions

---

## 📞 Questions?

1. Check `ARCHITECTURE.md` for detailed docs
2. Look at code comments
3. Follow existing patterns
4. Each file is small and focused - read the whole file!

---

**Remember:** The goal is **readability and maintainability**. Every file should be easy to understand, and every change should be easy to make.

Happy coding! 🚀

