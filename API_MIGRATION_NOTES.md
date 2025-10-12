# API Migration Notes

## Issue: ApiResponse Object Error

### Problem
After implementing the REST API architecture, you may encounter this error:
```
Uncaught Error: Cannot use object of type ExpenseTracker\Router\ApiResponse as array
```

### Cause
The AuthController methods now return `ApiResponse` objects (for API endpoints), but the old web pages (`login.php`, `signup.php`) were trying to use them as arrays.

### Solution
Web pages (HTML forms) should use the `Auth` service directly, while API endpoints use the controllers.

## Architecture Separation

### For Web Pages (Traditional Form Submissions)
Use the `Auth` service directly:
```php
// login.php, signup.php
use ExpenseTracker\Services\Auth;

$result = Auth::login($username, $password);
// Returns array: ['success' => true/false, 'error' => '...']
```

### For API Endpoints (REST API)
Use the Controllers which return `ApiResponse`:
```php
// api.php routes
$router->post('/api/auth/login', [AuthController::class, 'login']);
// Returns ApiResponse object with JSON
```

## Files Structure

### Web Entry Points (Use Services Directly)
- `login.php` → Uses `Auth::login()`
- `signup.php` → Uses `Auth::register()`
- `index.php` → Uses `ExpenseController` legacy methods
- `settings.php` → Uses `SettingsController::getSettingsData()`

### API Entry Point (Uses Controllers)
- `api.php` → All routes use Controllers that return `ApiResponse`

## Fixed Files

The following files have been updated to work correctly:

1. **login.php** - Now uses `Auth::login()` directly
2. **signup.php** - Now uses `Auth::register()` directly
3. **settings.php** - Uses legacy `getSettingsData()` method

## Moving Forward

### When Creating New Features

**Option 1: Traditional Web Pages**
```php
// page.php
use ExpenseTracker\Services\Auth;
use ExpenseTracker\Models\Expense;

$expense = new Expense();
$data = $expense->getByUser(Auth::id());
// Use $data in your view
```

**Option 2: API-First Approach (Recommended)**
```php
// 1. Create API endpoint in api.php
$router->get('/api/myfeature', [MyController::class, 'index']);

// 2. Create controller method
public function index(): ApiResponse {
    $data = $this->model->getData();
    return ApiResponse::success(['items' => $data])
        ->setQueries(Model::getQueries());
}

// 3. Use in frontend
// page.php - Load data via JavaScript
<script src="/js/api-client.js"></script>
<script>
    api.get('/api/myfeature').then(response => {
        console.log(response.data.items);
    });
</script>
```

## Best Practice Recommendation

For new development, prefer the **API-First approach**:

1. ✅ All data operations go through API endpoints
2. ✅ Full debugging visibility in Network tab
3. ✅ Consistent response format
4. ✅ Query tracking and performance monitoring
5. ✅ Easy to test and maintain

For existing pages that work with forms, keep using the services directly to avoid breaking changes.

## Quick Test

After the fix, test that login works:

1. Go to `/login.php`
2. Enter credentials
3. Submit form
4. Should successfully log in without errors

If you want to use the API for login instead:
1. Use `/api.php/api/auth/login` endpoint
2. Send JSON data
3. Check response in Network tab

