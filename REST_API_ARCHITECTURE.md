# 🚀 REST API Architecture Documentation

## Overview

This project has been restructured with a comprehensive REST API architecture that makes debugging and tracking CRUD operations extremely easy. All data operations now go through traceable API endpoints that can be monitored in your browser's Network tab.

## 📁 Architecture Components

### 1. **API Router** (`src/Router/Router.php`)
- Centralized routing system with pattern matching
- Supports all HTTP methods (GET, POST, PUT, PATCH, DELETE)
- Automatic route parameter extraction
- Middleware support
- Full request/response logging

### 2. **API Response** (`src/Router/ApiResponse.php`)
- Standardized JSON response format
- Automatic debug information injection
- SQL query tracking
- Performance metrics
- Memory usage monitoring

### 3. **Request Logger** (`src/Services/RequestLogger.php`)
- Automatic logging of all API requests/responses
- File-based log storage with rotation
- Statistics and analytics
- Error tracking
- User activity monitoring

### 4. **Enhanced Models** (`src/Models/Model.php`)
- All SQL queries are automatically tracked
- Query execution time measurement
- Query parameter logging
- Easy to see what data is being fetched

### 5. **API Client** (`js/api-client.js`)
- Browser-side API client
- Automatic request logging in console
- Debug panel with keyboard shortcut (Ctrl+Shift+D)
- Request/response history
- Error handling with detailed messages

## 🎯 Key Features

### Browser Network Tab Visibility

Every CRUD operation is now visible in your browser's Developer Tools Network tab:

1. **Open Browser DevTools**: Press F12 or right-click → Inspect
2. **Go to Network Tab**: All API calls are clearly visible
3. **See All Details**:
   - Request method (GET, POST, PUT, DELETE)
   - Endpoint URL
   - Request payload
   - Response data
   - HTTP status codes
   - Response time

### Debug Mode

All API responses include detailed debug information when enabled:

```json
{
  "success": true,
  "data": { ... },
  "_debug": {
    "request": {
      "method": "GET",
      "path": "/api/expenses",
      "timestamp": "2025-10-12 10:30:45",
      "ip": "127.0.0.1"
    },
    "route": {
      "method": "GET",
      "path": "/api/expenses",
      "params": []
    },
    "controller": {
      "class": "ExpenseTracker\\Controllers\\ExpenseController",
      "method": "index"
    },
    "queries": {
      "count": 2,
      "list": [
        {
          "model": "ExpenseTracker\\Models\\Expense",
          "table": "expenses",
          "query": "SELECT e.*, c.name as category_name FROM expenses e LEFT JOIN categories c ON e.category = c.id WHERE e.user_id = ? ORDER BY e.date DESC",
          "params": [1],
          "execution_time": "2.34ms",
          "timestamp": "2025-10-12 10:30:45"
        }
      ]
    },
    "performance": {
      "execution_time": "15.67ms"
    },
    "memory": {
      "usage": "2.45 MB",
      "peak": "2.67 MB"
    }
  }
}
```

### Enable Debug Mode

**Method 1**: Add query parameter
```javascript
fetch('/api.php/api/expenses?debug=true')
```

**Method 2**: Add header
```javascript
fetch('/api.php/api/expenses', {
    headers: {
        'X-Debug': 'true'
    }
})
```

**Method 3**: Enable globally in config
```php
// In api.php
define('DEBUG_MODE', true);
```

## 📍 API Endpoints

### Authentication Routes

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/login` | User login |
| POST | `/api/auth/register` | User registration |
| POST | `/api/auth/logout` | User logout |
| GET | `/api/auth/me` | Get current user |

### Expense Routes

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/expenses` | Get all expenses for current user |
| GET | `/api/expenses/{id}` | Get single expense |
| POST | `/api/expenses` | Create new expense |
| PUT | `/api/expenses/{id}` | Update expense |
| DELETE | `/api/expenses/{id}` | Delete expense |
| GET | `/api/expenses/stats/summary` | Get expense statistics |
| GET | `/api/expenses/stats/categories` | Get category breakdown |

### Dashboard Route

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/dashboard` | Get all dashboard data in one request |

### Category Routes

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/categories` | Get all categories |

### Settings Routes

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/settings` | Get user settings |
| PATCH | `/api/settings/currency` | Update currency |
| PATCH | `/api/settings/profile` | Update user profile |

### System Routes

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/health` | API health check |
| GET | `/api/routes` | Get all available routes |

## 💻 Usage Examples

### Frontend JavaScript (Using API Client)

#### Create Expense
```javascript
// The API client is automatically loaded and available as window.api
const newExpense = await api.post('/api/expenses', {
    amount: 100,
    category: 'food',
    description: 'Lunch at restaurant',
    date: '2025-10-12'
});

console.log('Expense created:', newExpense.data.expense);
```

#### Get All Expenses
```javascript
const response = await api.get('/api/expenses');
const expenses = response.data.expenses;

console.log(`Loaded ${expenses.length} expenses`);
```

#### Update Expense
```javascript
const updated = await api.put('/api/expenses/exp_123', {
    amount: 150,
    description: 'Updated description'
});
```

#### Delete Expense
```javascript
await api.delete('/api/expenses/exp_123');
```

### Using Fetch API Directly

```javascript
// Create expense
const response = await fetch('/api.php/api/expenses', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-Debug': 'true'
    },
    body: JSON.stringify({
        amount: 100,
        category: 'food',
        description: 'Test expense',
        date: '2025-10-12'
    })
});

const data = await response.json();

if (data.success) {
    console.log('Expense created:', data.data.expense);
    console.log('Debug info:', data._debug);
} else {
    console.error('Error:', data.error);
}
```

### Backend PHP (Creating Custom Routes)

```php
// In api.php

$router->get('/api/custom/stats', function() {
    $model = new ExpenseModel();
    $stats = $model->getCustomStats();
    
    return ApiResponse::success([
        'stats' => $stats
    ])->setQueries(Model::getQueries());
});

$router->post('/api/custom/action', [CustomController::class, 'handleAction']);
```

### Creating Custom Controller

```php
<?php
namespace ExpenseTracker\Controllers;

use ExpenseTracker\Router\ApiResponse;
use ExpenseTracker\Models\Model;

class CustomController
{
    public function handleAction(array $params): ApiResponse
    {
        // Get input
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validation
        if (empty($input['required_field'])) {
            return ApiResponse::error('Field is required', 400);
        }
        
        // Process data
        // ...
        
        // Return response with queries
        return ApiResponse::success([
            'result' => $data
        ])->setQueries(Model::getQueries());
    }
}
```

## 🔍 Debugging Tools

### 1. Browser Console

The API client automatically logs all requests in the browser console:

```javascript
// Open console (F12) and you'll see:
🌐 API Request: GET /api/expenses
  📤 Request: {...}
  📥 Response: {...}
  🐛 Debug Information
    Route: {...}
    Controller: {...}
    💾 Database Queries (2)
      Query 1 - 2.34ms
        Model: ExpenseTracker\Models\Expense
        SQL: SELECT * FROM expenses WHERE user_id = ?
        Params: [1]
```

### 2. Debug Panel (Ctrl+Shift+D)

Press `Ctrl+Shift+D` to show a floating debug panel that displays:
- Recent API requests
- Response times
- Status codes
- Quick export to JSON

### 3. API Documentation Page

Visit `/api-docs.php` to access:
- Interactive API tester
- Complete route listing
- Request/response examples
- Live API status

### 4. API Logs Viewer

Visit `/api-logs.php` to view:
- All API requests
- Response logs
- Error tracking
- Statistics and analytics
- Filterable by type, method, endpoint

## 📊 Monitoring & Analytics

### View Request Logs

```php
use ExpenseTracker\Services\RequestLogger;

$logger = RequestLogger::getInstance();

// Get recent logs
$logs = $logger->getRecentLogs(100);

// Get statistics
$stats = $logger->getStatistics();

// Export logs
$allLogs = $logger->getRecentLogs(1000);
file_put_contents('logs-export.json', json_encode($allLogs));
```

### Track Specific Operations

Every database operation is automatically tracked:

```php
// In your controller
$expenses = $this->expenseModel->getByUser($userId);

// Get all queries that were executed
$queries = Model::getQueries();

// Each query contains:
// - model: Which model class
// - table: Which database table
// - query: The SQL query
// - params: Query parameters
// - execution_time: How long it took
// - timestamp: When it was executed
```

## 🛠️ Configuration

### Enable/Disable Features

```php
// In bootstrap.php or config.php

// Enable API request logging
define('LOG_API_REQUESTS', true);

// Enable debug mode (includes _debug in responses)
define('DEBUG_MODE', true);

// Set log level
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR
```

### CORS Configuration

```php
// In api.php

// Configure CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Debug');
```

## 🎓 Best Practices

### 1. Always Use API Client

```javascript
// ✅ Good
await api.post('/api/expenses', data);

// ❌ Avoid
fetch('/api.php/api/expenses', {method: 'POST', body: JSON.stringify(data)});
```

### 2. Handle Errors Properly

```javascript
try {
    const response = await api.post('/api/expenses', data);
    // Handle success
} catch (error) {
    if (error instanceof ApiError) {
        // Show validation errors
        if (error.data?.errors) {
            console.error('Validation errors:', error.data.errors);
        }
    }
}
```

### 3. Return ApiResponse from Controllers

```php
// ✅ Good
public function index(): ApiResponse
{
    $data = $this->model->getAll();
    return ApiResponse::success(['items' => $data])
        ->setQueries(Model::getQueries());
}

// ❌ Avoid
public function index()
{
    echo json_encode(['items' => $data]);
}
```

### 4. Track All Queries

```php
// Always include query tracking in responses
return ApiResponse::success($data)->setQueries(Model::getQueries());
```

## 🔐 Security Considerations

1. **Authentication**: Protected routes require session authentication
2. **Input Validation**: All inputs are validated before processing
3. **SQL Injection**: Prepared statements are used everywhere
4. **XSS Protection**: Outputs are HTML-escaped
5. **CSRF**: Consider adding CSRF tokens for production

## 📝 File Structure

```
/
├── api.php                          # Main API entry point
├── api-docs.php                     # Interactive API documentation
├── api-logs.php                     # Request logs viewer
├── js/
│   ├── api-client.js               # Browser API client
│   └── dashboard-api.js            # Dashboard integration
├── src/
│   ├── Router/
│   │   ├── Router.php              # API router
│   │   └── ApiResponse.php         # Response class
│   ├── Services/
│   │   └── RequestLogger.php       # Request logging
│   ├── Models/
│   │   └── Model.php               # Base model with query tracking
│   └── Controllers/
│       ├── ExpenseController.php   # Updated with API responses
│       ├── AuthController.php      # Auth endpoints
│       └── SettingsController.php  # Settings endpoints
└── logs/
    └── api-requests.log            # Request logs
```

## 🚀 Getting Started

1. **Make an API Request**: Open your app and perform any action
2. **Open DevTools**: Press F12
3. **Check Network Tab**: See the API call with full details
4. **View Debug Info**: Click on the request to see debug data
5. **Check Console**: See detailed logs with SQL queries
6. **Press Ctrl+Shift+D**: Open the debug panel for quick access

## 🎉 Benefits

✅ **Easy Debugging**: See exactly what's happening in your app  
✅ **Performance Monitoring**: Track slow queries and optimize  
✅ **Error Tracking**: Know exactly where and why errors occur  
✅ **Developer Friendly**: Clear structure and documentation  
✅ **Production Ready**: Can disable debug mode in production  
✅ **Standards Compliant**: RESTful API design  
✅ **Extensible**: Easy to add new routes and features  

## 📞 Support

- Check `/api-docs.php` for interactive documentation
- View `/api-logs.php` for request history
- Press `Ctrl+Shift+D` for quick debug panel
- Open browser console (F12) for detailed logs

---

**Happy Debugging! 🐛🔍**

