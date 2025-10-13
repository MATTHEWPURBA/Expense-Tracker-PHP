# 🚀 API Quick Reference Guide

## Quick Start (30 seconds)

1. Open your browser DevTools: **F12**
2. Go to **Network** tab
3. Perform any action in the app (add/delete expense)
4. See the API call appear with full details
5. Click on it to see request/response data

**That's it!** You can now track every CRUD operation.

## Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| `F12` | Open browser DevTools |
| `Ctrl + Shift + D` | Show API debug panel |
| `Ctrl + Shift + I` | Open browser console |

## Browser Network Tab

### What You'll See

```
Name                Method  Status  Type    Time
───────────────────────────────────────────────
api/expenses        GET     200     xhr     45ms
api/expenses        POST    201     xhr     67ms
api/expenses/exp_1  DELETE  200     xhr     23ms
```

### Click on Any Request to See:

**Headers Tab:**
- Request method
- Request URL
- Status code
- Response headers

**Payload Tab:**
- Request data
- Form data or JSON body

**Response Tab:**
```json
{
  "success": true,
  "data": {
    "expense": {
      "id": "exp_123",
      "amount": 100,
      "category": "food",
      "description": "Lunch",
      "date": "2025-10-12"
    }
  },
  "_debug": {
    "route": {...},
    "controller": "ExpenseController@store",
    "queries": [
      {
        "query": "INSERT INTO expenses...",
        "execution_time": "2.3ms"
      }
    ],
    "performance": {
      "execution_time": "15.6ms"
    }
  }
}
```

## Browser Console

### What You'll See

```javascript
🌐 API Request: POST /api/expenses
  📤 Request: {
    method: "POST",
    endpoint: "/api/expenses",
    data: {amount: 100, category: "food"}
  }
  📥 Response: {
    status: 201,
    statusText: "Created",
    responseTime: "67.23ms",
    data: {...}
  }
  🐛 Debug Information
    Route: POST /api/expenses
    Controller: ExpenseController@store
    💾 Database Queries (1)
      Query 1 - 2.34ms
        Model: ExpenseTracker\Models\Expense
        Table: expenses
        SQL: INSERT INTO expenses (id, user_id, amount, category, description, date) VALUES (?, ?, ?, ?, ?, ?)
        Params: ["exp_123", 1, 100, "food", "Lunch", "2025-10-12"]
```

## Common API Calls

### Get All Expenses
```javascript
const response = await api.get('/api/expenses');
// Check Network tab: GET /api/expenses
// See all expenses in response
```

### Create Expense
```javascript
await api.post('/api/expenses', {
    amount: 100,
    category: 'food',
    description: 'Lunch',
    date: '2025-10-12'
});
// Check Network tab: POST /api/expenses
// See created expense in response
```

### Update Expense
```javascript
await api.put('/api/expenses/exp_123', {
    amount: 150
});
// Check Network tab: PUT /api/expenses/exp_123
// See updated expense in response
```

### Delete Expense
```javascript
await api.delete('/api/expenses/exp_123');
// Check Network tab: DELETE /api/expenses/exp_123
// See success response
```

## Debugging Flow

### Finding Bugs in Routes

1. **Open Network Tab** (F12)
2. **Perform the action** that has a bug
3. **Find the API call** in the list
4. **Click on it** to see details
5. **Check Response tab** for debug info
6. **Look at queries** to see what database operations happened

### Example: "Why isn't my expense showing up?"

1. Open Network tab
2. Add the expense
3. See `POST /api/expenses` with status 201 (created)
4. Check response - see the created expense
5. See `GET /api/expenses` refresh call
6. Check if your expense is in the list
7. Look at the queries to see the SELECT statement

### Example: "Why is this slow?"

1. Open Network tab
2. Perform the slow action
3. Look at the Time column
4. Click on the slow request
5. Check `_debug.performance.execution_time`
6. Look at `_debug.queries` to find slow queries
7. See which query took the most time

## File Locations

| Feature | File Path |
|---------|-----------|
| API Entry Point | `/api.php` |
| API Documentation | `/api-docs.php` |
| Request Logs | `/api-logs.php` |
| API Client JS | `/js/api-client.js` |
| Dashboard JS | `/js/dashboard-api.js` |
| Request Logs | `/logs/api-requests.log` |

## Pages You Can Visit

| URL | Description |
|-----|-------------|
| `/api-docs.php` | Interactive API documentation with tester |
| `/api-logs.php` | View all API request logs |
| `/api.php/api/routes` | JSON list of all routes |
| `/api.php/api/health` | API health check |

## Debug Modes

### Enable Debug Info in Responses

**Option 1:** Add to URL
```
/api.php/api/expenses?debug=true
```

**Option 2:** Add header
```javascript
fetch('/api.php/api/expenses', {
    headers: { 'X-Debug': 'true' }
})
```

**Option 3:** Enable globally
```php
// In api.php
define('DEBUG_MODE', true);
```

## Common Issues & Solutions

### Issue: "Can't see API calls in Network tab"

**Solution:**
1. Make sure DevTools is open BEFORE making the request
2. Make sure "XHR" or "Fetch/XHR" filter is enabled
3. Try "Preserve log" option

### Issue: "No debug info in response"

**Solution:**
1. Add `?debug=true` to URL
2. Or enable `DEBUG_MODE` in `api.php`

### Issue: "Empty response"

**Solution:**
1. Check browser console for errors
2. Check Network tab status code
3. Look at Response tab for error message

### Issue: "Request not going through"

**Solution:**
1. Check if you're using the right method (GET/POST/etc)
2. Check if endpoint path is correct
3. Look at Console tab for CORS errors

## Response Format

### Success Response
```json
{
  "success": true,
  "data": { ... },
  "message": "Optional success message"
}
```

### Error Response
```json
{
  "success": false,
  "error": "Error message",
  "errors": {
    "field": "Validation error for this field"
  }
}
```

### With Debug Info
```json
{
  "success": true,
  "data": { ... },
  "_debug": {
    "request": { ... },
    "route": { ... },
    "controller": { ... },
    "queries": { ... },
    "performance": { ... },
    "memory": { ... }
  }
}
```

## Tips & Tricks

### 1. Use the Debug Panel
Press `Ctrl+Shift+D` for a floating panel that shows recent requests

### 2. Export Logs
```javascript
api.exportLog(); // Downloads JSON file of all requests
```

### 3. Clear Logs
```javascript
api.clearLog(); // Clears request history
```

### 4. Check API Health
```javascript
fetch('/api.php/api/health')
  .then(r => r.json())
  .then(d => console.log('API Status:', d));
```

### 5. View All Routes
Visit `/api.php/api/routes` or check `/api-docs.php`

### 6. Filter Network Requests
In DevTools Network tab, type "api/" in the filter to see only API calls

### 7. Copy Request as cURL
Right-click on request → Copy → Copy as cURL

### 8. Replay Request
Right-click on request → Replay XHR

## Integration with Frontend

### Basic Setup
```html
<!-- Include API client -->
<script src="/js/api-client.js"></script>
<script src="/js/dashboard-api.js"></script>

<script>
// API client is now available as window.api
// Dashboard is available as window.dashboard

// Initialize dashboard
dashboard.init();
</script>
```

### Make API Calls
```javascript
// The api object is globally available
const response = await api.get('/api/expenses');
const expenses = response.data.expenses;
```

## Production Considerations

### Disable Debug Mode
```php
// In api.php
define('DEBUG_MODE', false);
```

### Disable Request Logging
```php
// In bootstrap.php or config.php
define('LOG_API_REQUESTS', false);
```

### Or Keep Minimal Logging
```php
define('LOG_API_REQUESTS', true);
define('LOG_LEVEL', 'ERROR'); // Only log errors
```

---

## Need More Help?

- 📖 Full documentation: `REST_API_ARCHITECTURE.md`
- 🧪 Test APIs: `/api-docs.php`
- 📊 View logs: `/api-logs.php`
- 💻 Check console: F12 → Console tab
- 🌐 Check network: F12 → Network tab

**Happy Debugging! 🚀**

