# Expense Tracker - Micro Architecture Documentation

## 📐 Overview

This document describes the micro-architecture implementation of the Expense Tracker application. The architecture follows modern PHP development practices with a clear separation of concerns, making the codebase maintainable, testable, and easy to understand.

## 🏗️ Architecture Pattern

The application uses a **Micro-MVC** (Model-View-Controller) architecture pattern with additional Service and Middleware layers:

```
┌─────────────────────────────────────────────────────────┐
│                    Entry Points                          │
│              (index.php, login.php, etc.)                │
└───────────────────┬─────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────────────┐
│                   Bootstrap                              │
│         (Autoloading, Config, Initialization)            │
└───────────────────┬─────────────────────────────────────┘
                    │
        ┌───────────┼───────────┐
        │           │           │
        ▼           ▼           ▼
┌──────────┐  ┌──────────┐  ┌──────────┐
│Middleware│  │Controller│  │  Service │
└────┬─────┘  └────┬─────┘  └────┬─────┘
     │             │              │
     │             ▼              │
     │        ┌────────┐          │
     │        │ Model  │◄─────────┘
     │        └────┬───┘
     │             │
     │             ▼
     │        ┌────────┐
     │        │Database│
     │        └────────┘
     │
     ▼
┌──────────┐
│   View   │
└──────────┘
```

## 📁 Directory Structure

```
Expense-Tracker-PHP/
│
├── bootstrap.php              # Application initialization
├── composer.json              # Dependency management & PSR-4 autoloading
├── config.php                 # Database configuration
│
├── index.php                  # Main dashboard entry point
├── login.php                  # Login entry point
├── signup.php                 # Registration entry point
├── logout.php                 # Logout entry point
│
├── src/                       # Source code (PSR-4 autoloaded)
│   │
│   ├── Controllers/           # Handle HTTP requests & responses
│   │   ├── AuthController.php
│   │   ├── ExpenseController.php
│   │   └── ExportController.php
│   │
│   ├── Models/                # Database interactions
│   │   ├── Model.php         # Base model with CRUD operations
│   │   ├── User.php
│   │   ├── Expense.php
│   │   └── Category.php
│   │
│   ├── Middleware/            # Request filtering & authorization
│   │   └── AuthMiddleware.php
│   │
│   └── Services/              # Business logic & utilities
│       ├── Auth.php           # Authentication service
│       └── Database.php       # Database connection singleton
│
├── views/                     # Presentation layer
│   ├── dashboard/
│   │   └── index.php         # Dashboard view
│   ├── auth/
│   │   ├── login.php         # Login view
│   │   └── signup.php        # Registration view
│   ├── exports/
│   │   └── pdf.php           # PDF export template
│   └── layouts/
│       ├── dashboard-styles.php  # Dashboard CSS
│       └── auth-styles.php       # Auth pages CSS
│
├── data/                      # JSON data files (legacy/backup)
├── logs/                      # Application logs
│
└── old-architecture-backup-20251011_202303/  # Pre-refactoring backup
```

## 🔍 Component Details

### 1. **bootstrap.php** - Application Bootstrap

**Purpose:** Initializes the application environment

**Responsibilities:**
- Start PHP session
- Configure error handling
- Set up PSR-4 autoloading
- Load configuration
- Initialize database tables
- Define global helper functions

**Key Functions:**
```php
config($key, $default)    # Get configuration values
redirect($url)            # HTTP redirect helper
view($path, $data)        # Render view files
jsonResponse($data)       # Return JSON responses
```

---

### 2. **Controllers** - Request Handlers

Controllers receive HTTP requests, process them using models/services, and return responses.

#### **AuthController**
- `login()` - Handle user login
- `register()` - Handle user registration
- `logout()` - Handle user logout

#### **ExpenseController**
- `getDashboardData()` - Fetch all dashboard data
- `addExpense()` - Create new expense
- `deleteExpense()` - Delete expense
- `getExpenses()` - Get user's expenses (AJAX)

#### **ExportController**
- `export($format)` - Export data in various formats
- `exportCSV()` - CSV export
- `exportJSON()` - JSON export
- `exportXML()` - XML export
- `exportExcel()` - Excel export
- `exportPDF()` - Printable HTML/PDF export

**Design Pattern:** Front Controller Pattern

---

### 3. **Models** - Data Layer

Models handle all database operations using prepared statements for security.

#### **Model (Base Class)**
Provides common CRUD operations:
```php
find($id)               # Find by primary key
all()                   # Get all records
where($column, $value)  # Find with WHERE clause
create($data)           # Insert new record
update($id, $data)      # Update record
delete($id)             # Delete record
```

#### **User Model**
- `findByEmail($email)`
- `findByUsername($username)`
- `findByUsernameOrEmail($usernameOrEmail)`
- `updateLastLogin($userId)`

#### **Expense Model**
- `getByUser($userId)` - Get user's expenses with category info
- `getStats($userId)` - Calculate expense statistics
- `getCategoryBreakdown($userId)` - Get spending by category
- `deleteByUser($expenseId, $userId)` - Secure delete

#### **Category Model**
- `getAllOrdered()` - Get categories sorted by name
- `findById($categoryId)` - Find specific category

**Design Pattern:** Active Record Pattern

---

### 4. **Services** - Business Logic

Services contain reusable business logic that doesn't belong in controllers or models.

#### **Database Service**
- Singleton pattern for database connection
- Manages PostgreSQL connection
- Initializes database tables
- Inserts default categories

**Methods:**
```php
getInstance()           # Get singleton instance
getConnection()         # Get PDO connection
initializeTables()      # Create tables if not exist
```

#### **Auth Service**
- User authentication and authorization
- Password hashing and verification
- Session management

**Methods:**
```php
check()                          # Check if user is logged in
id()                             # Get current user ID
user()                           # Get current user data
login($username, $password)      # Authenticate user
register($name, $email, $pass)   # Register new user
logout()                         # Destroy session
sanitize($input)                 # Sanitize user input
```

**Design Pattern:** Service Layer Pattern, Singleton Pattern

---

### 5. **Middleware** - Request Filtering

Middleware runs before controllers to perform checks and filters.

#### **AuthMiddleware**
- `handle()` - Ensure user is authenticated (redirect to login if not)
- `handleGuest()` - Ensure user is NOT authenticated (redirect to dashboard if logged in)

**Design Pattern:** Middleware Pattern

---

### 6. **Views** - Presentation Layer

Views contain only presentation logic (HTML/CSS/JavaScript). All data is passed from controllers.

**View Structure:**
```
views/
  ├── dashboard/index.php      # Main dashboard
  ├── auth/login.php           # Login form
  ├── auth/signup.php          # Registration form
  ├── exports/pdf.php          # PDF export template
  └── layouts/                 # Shared styles
```

**View Variables:**
- Dashboard: `$expenses`, `$categories`, `$stats`, `$categoryBreakdown`, `$user`
- Auth: `$error`, `$success`

---

## 🔄 Request Flow

### Example: Adding a New Expense

```
1. User submits form (POST /index.php)
   ↓
2. bootstrap.php initializes application
   ↓
3. AuthMiddleware::handle() checks authentication
   ↓
4. ExpenseController::addExpense() processes request
   ↓
5. ExpenseModel::create() inserts into database
   ↓
6. Controller returns JSON response
   ↓
7. JavaScript reloads page
   ↓
8. Dashboard view renders with new data
```

### Example: User Login

```
1. User visits /login.php
   ↓
2. bootstrap.php initializes application
   ↓
3. AuthMiddleware::handleGuest() checks if already logged in
   ↓
4. User submits credentials (POST)
   ↓
5. AuthController::login() receives request
   ↓
6. Auth::login() validates credentials
   ↓
7. UserModel::findByUsernameOrEmail() queries database
   ↓
8. Password verified, session created
   ↓
9. Redirect to dashboard (index.php)
```

---

## 🔐 Security Features

### 1. **SQL Injection Prevention**
- All queries use PDO prepared statements
- Parameters are properly bound

### 2. **Password Security**
- Passwords hashed with `PASSWORD_DEFAULT` (bcrypt)
- Verified with `password_verify()`

### 3. **XSS Prevention**
- All user input sanitized with `htmlspecialchars()`
- Output escaped in views

### 4. **CSRF Protection**
- Session-based authentication
- AJAX requests use same-origin policy

### 5. **Authentication**
- Session management
- Middleware guards protected routes
- Secure session cookies

### 6. **Input Validation**
- Server-side validation
- Email format validation
- Password strength requirements
- Username character restrictions

---

## 📊 Database Schema

```sql
-- Users table
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    color VARCHAR(7) NOT NULL,
    icon VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Expenses table
CREATE TABLE expenses (
    id VARCHAR(50) PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    amount DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL REFERENCES categories(id) ON DELETE CASCADE,
    description TEXT,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🎯 Design Patterns Used

1. **MVC (Model-View-Controller)** - Overall architecture
2. **Singleton** - Database connection
3. **Active Record** - Model layer
4. **Service Layer** - Business logic separation
5. **Middleware** - Request filtering
6. **Front Controller** - Entry points route to controllers
7. **Dependency Injection** - Models injected into controllers

---

## 🚀 Benefits of This Architecture

### 1. **Separation of Concerns**
- Each component has a single, well-defined responsibility
- Easy to locate and modify specific functionality

### 2. **Maintainability**
- Clean code structure
- Easy to understand for new developers
- Changes are isolated to specific components

### 3. **Testability**
- Components can be unit tested independently
- Mock objects can be used for dependencies
- Clear interfaces between layers

### 4. **Reusability**
- Services can be used by multiple controllers
- Base model provides common functionality
- Views can be rendered from anywhere

### 5. **Scalability**
- Easy to add new features
- Can add new controllers/models without affecting existing code
- Clear extension points

### 6. **Security**
- Centralized authentication logic
- Middleware enforces security consistently
- Input sanitization in one place

---

## 📝 Coding Standards

### PSR-4 Autoloading
```
ExpenseTracker\Controllers\AuthController
└─ src/Controllers/AuthController.php

ExpenseTracker\Models\User
└─ src/Models/User.php
```

### Naming Conventions
- **Classes:** PascalCase (e.g., `ExpenseController`)
- **Methods:** camelCase (e.g., `getDashboardData()`)
- **Variables:** camelCase (e.g., `$userId`)
- **Constants:** UPPER_SNAKE_CASE (e.g., `APP_ROOT`)

### File Organization
- One class per file
- File name matches class name
- Namespace matches directory structure

---

## 🔧 Adding New Features

### Example: Adding a Budget Feature

1. **Create Model:**
```php
// src/Models/Budget.php
class Budget extends Model {
    protected $table = 'budgets';
    // Add budget-specific methods
}
```

2. **Create Controller:**
```php
// src/Controllers/BudgetController.php
class BudgetController {
    public function getBudgets() { }
    public function createBudget() { }
}
```

3. **Create View:**
```php
// views/budget/index.php
// Budget management interface
```

4. **Add Entry Point:**
```php
// budget.php
require_once __DIR__ . '/bootstrap.php';
$controller = new BudgetController();
// Handle requests and render view
```

---

## 📚 Helper Functions

Global helpers defined in `bootstrap.php`:

```php
config($key, $default = null)
// Get configuration value
// Usage: config('db_host')

redirect($url, $statusCode = 302)
// Perform HTTP redirect
// Usage: redirect('/login.php')

view($viewPath, $data = [])
// Render view file with data
// Usage: view('dashboard.index', ['expenses' => $expenses])

jsonResponse($data, $statusCode = 200)
// Return JSON response and exit
// Usage: jsonResponse(['success' => true])
```

---

## 🎓 Learning Resources

To understand this architecture better, learn about:

1. **MVC Pattern** - Model-View-Controller architecture
2. **PSR-4** - PHP autoloading standard
3. **PDO** - PHP Data Objects for database
4. **Prepared Statements** - SQL injection prevention
5. **Singleton Pattern** - Single instance management
6. **Active Record** - ORM pattern
7. **Service Layer** - Business logic separation

---

## 🔄 Migration from Old Architecture

The old monolithic architecture has been refactored to this micro-architecture:

**Before:**
- All logic in single files (index.php, login.php)
- Repeated database connection code
- Mixed presentation and business logic
- Hard to test and maintain

**After:**
- Separated concerns (MVC + Services)
- Centralized database management
- Reusable components
- Easy to test and extend

Old files are backed up in: `old-architecture-backup-20251011_202303/`

---

## 📞 Support

For questions about the architecture:
1. Read this documentation thoroughly
2. Examine the code comments
3. Study the design patterns used
4. Review the examples provided

---

**Version:** 1.0.0  
**Last Updated:** October 2025  
**Architecture Pattern:** Micro-MVC with Service Layer

