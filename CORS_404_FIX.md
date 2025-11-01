# CORS and 404 Error Fix for InfinityFree Deployment

## Problem Summary

When deploying to InfinityFree, the API endpoint `/api/auth/login` was returning a 404 error, which then redirected to InfinityFree's error page, causing a CORS policy error.

## Root Cause

1. **Missing URL Rewriting Rules**: The `.htaccess` file didn't have rules to route `/api/*` requests to `api.php`
2. **Path Handling**: The Router needed improvements to handle rewritten requests correctly
3. **CORS Configuration**: CORS headers needed better handling for same-origin requests

## Solutions Implemented

### 1. Added URL Rewriting Rules to `.htaccess`

Added rewrite rules to route all `/api/*` requests to `api.php`:

```apache
# API Routing: Route all /api/* requests to api.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^api/(.*)$ api.php [QSA,L]
```

**Location**: `.htaccess` lines 9-12

### 2. Improved Router Path Handling

Updated `Router.php` to correctly handle rewritten requests:

- Better detection of when requests are routed via `.htaccess`
- Preserves full path including `/api/` prefix for rewritten requests
- Normalizes paths to ensure consistent matching

**Location**: `src/Router/Router.php` lines 104-127

### 3. Enhanced CORS Headers

Updated `api.php` to handle CORS more intelligently:

- Detects same-origin requests
- Sets appropriate CORS headers based on origin
- Properly handles preflight OPTIONS requests

**Location**: `api.php` lines 25-41

### 4. Created Fallback Directory Structure

Created an `api/` directory with routing files as a fallback solution:

- `api/index.php` - Routes to parent `api.php`
- `api/.htaccess` - Handles routing within the directory

**Note**: This is only needed if the main `.htaccess` rewrite rules don't work on your hosting.

## Deployment Instructions

### Step 1: Upload Updated Files

Upload these updated files to your InfinityFree hosting:

1. ✅ `.htaccess` - Contains new API routing rules
2. ✅ `api.php` - Contains improved CORS handling
3. ✅ `src/Router/Router.php` - Contains improved path handling
4. ✅ `api/` directory (if created) - Fallback routing structure

### Step 2: Verify `.htaccess` is Uploaded

**Important**: Make sure `.htaccess` file is uploaded correctly:

- Hidden files (starting with `.`) might not upload easily via File Manager
- **If using File Manager**: 
  - Click "Create File"
  - Name it exactly: `.htaccess` (with the dot)
  - Paste the content from your local `.htaccess` file
- **If using FTP**: 
  - Make sure to show hidden files in your FTP client
  - Upload `.htaccess` normally

### Step 3: Test the API Endpoint

1. **Visit your site**: `https://yoursite.rf.gd/login.php`
2. **Open browser console** (F12 → Console tab)
3. **Check for debug info**: You should see:
   ```
   🔧 Login page loaded
   📋 Available endpoints:
     - Traditional: POST /login.php
     - API: POST /api/auth/login
   ```
4. **Enable debug mode** (checkbox on login page)
5. **Try to login** - Should see API calls in Network tab

### Step 4: Verify API is Working

Test the API endpoint directly:

1. **Visit**: `https://yoursite.rf.gd/api/health`
   - Should return: `{"success":true,"data":{"status":"healthy",...}}`

2. **Visit**: `https://yoursite.rf.gd/api/routes`
   - Should return list of all available API routes

If both work, your API routing is configured correctly!

## Troubleshooting

### Issue 1: Still Getting 404 Errors

**Symptoms**: `/api/auth/login` still returns 404

**Solutions**:

1. **Verify `.htaccess` is uploaded**:
   - Check file exists in `htdocs/` directory
   - Check file content matches local version

2. **Check InfinityFree supports mod_rewrite**:
   - InfinityFree should support it, but verify in PHP configuration
   - Some free hosts disable mod_rewrite

3. **Try the fallback directory structure**:
   - Access via: `https://yoursite.rf.gd/api/index.php`
   - Or: `https://yoursite.rf.gd/api/auth/login` (should work if `api/` directory is set up)

4. **Test direct access to api.php**:
   - Visit: `https://yoursite.rf.gd/api.php`
   - Should show API response or error (not 404)

### Issue 2: CORS Errors Still Occur

**Symptoms**: CORS policy errors in browser console

**Solutions**:

1. **Check CORS headers are being sent**:
   - Open Network tab in browser (F12)
   - Look for `/api/auth/login` request
   - Check Response Headers for `Access-Control-Allow-Origin`

2. **Verify same origin**:
   - Make sure you're accessing from the same domain
   - No mixed HTTP/HTTPS issues

3. **Check preflight requests**:
   - Look for OPTIONS request before POST
   - Should return 200 status

### Issue 3: API Endpoint Works but Login Fails

**Symptoms**: API responds but authentication fails

**Solutions**:

1. **Check database connection**:
   - Verify `config.php` has correct credentials
   - Test database connection

2. **Check authentication logic**:
   - Verify user exists in database
   - Check password hashing is correct

3. **Check logs**:
   - Look in `logs/error.log` for error messages
   - Check browser console for detailed error responses

### Issue 4: Path Matching Issues

**Symptoms**: Router can't match routes

**Solutions**:

1. **Check REQUEST_URI value**:
   - Add temporary debug output in `api.php`:
     ```php
     error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
     error_log("SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME']);
     ```

2. **Verify path format**:
   - Should be: `/api/auth/login`
   - Not: `/api.php/api/auth/login`

## Alternative Solution: Direct API Access

If URL rewriting doesn't work on your hosting, you can modify the frontend to access `api.php` directly with query parameters. However, this is **NOT recommended** and the rewriting solution is better.

## Testing Checklist

After deploying the fixes, verify:

- [ ] `.htaccess` file is uploaded correctly
- [ ] `/api/health` endpoint returns success response
- [ ] `/api/routes` endpoint returns list of routes
- [ ] Login page loads without console errors
- [ ] API login works when debug mode is enabled
- [ ] Traditional form login still works as fallback
- [ ] No CORS errors in browser console
- [ ] Network tab shows successful API calls

## Additional Notes

- **SSL/HTTPS**: Make sure SSL certificate is installed and working
- **PHP Version**: Ensure PHP 7.4+ is active
- **File Permissions**: Ensure `data/` and `logs/` directories have write permissions (777)

## Support

If you continue to experience issues:

1. Check InfinityFree error logs in VistaPanel
2. Enable debug mode in `api.php` (already enabled)
3. Check browser Network tab for detailed request/response
4. Verify all files are uploaded correctly
5. Test with a simple API endpoint first (`/api/health`)

---

**Last Updated**: 2025
**Version**: 1.0

