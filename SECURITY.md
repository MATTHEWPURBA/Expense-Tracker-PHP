# 🔒 Security Best Practices

This document outlines the security measures implemented in the Expense Tracker application and best practices for deployment.

---

## 🛡️ Database Security

### Secure Configuration

Database credentials are now stored in a separate `config.php` file instead of being hardcoded in the main application.

#### Why This Matters

- **Version Control Safety**: Credentials are never committed to Git
- **Environment Separation**: Different configs for dev/staging/production
- **Easy Updates**: Change credentials without modifying application code
- **Reduced Exposure**: Credentials in one place, easier to secure

#### Setup Instructions

1. **Create Configuration File**
   ```bash
   cp config.example.php config.php
   ```

2. **Edit with Your Credentials**
   ```bash
   nano config.php  # or use your preferred editor
   ```

3. **Set Appropriate Permissions** (Linux/macOS)
   ```bash
   chmod 600 config.php  # Read/write for owner only
   ```

4. **Verify Git Ignore**
   ```bash
   git status  # config.php should NOT appear
   ```

### Connection Security

The application uses PostgreSQL with SSL encryption:

```php
'db_ssl' => 'require'  // Forces encrypted connections
```

### SQL Injection Protection

All database queries use **PDO prepared statements**:

```php
// ✅ SECURE - Uses prepared statements
$stmt = $pdo->prepare("SELECT * FROM expenses WHERE id = ?");
$stmt->execute([$id]);

// ❌ NEVER DO THIS - Vulnerable to SQL injection
$result = $pdo->query("SELECT * FROM expenses WHERE id = $id");
```

---

## 🔐 File Security

### .htaccess Protection

The `.htaccess` file includes multiple security measures:

```apache
# Prevent directory listing
Options -Indexes

# Protect sensitive files
<FilesMatch "^(config\.php|\.env|\.git)">
    Order allow,deny
    Deny from all
</FilesMatch>

# Prevent access to JSON data files
<FilesMatch "\.(json|lock)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### File Permissions

Recommended file permissions:

```bash
# Application files
chmod 644 index.php
chmod 644 .htaccess
chmod 644 migrate.php

# Configuration (more restrictive)
chmod 600 config.php

# Data directory
chmod 755 data/
chmod 644 data/*.json
```

---

## 🚫 What NOT to Do

### ❌ Never Commit Sensitive Data

```bash
# BAD - Don't do this!
git add config.php
git commit -m "Added config"
git push

# GOOD - This is safe
git add config.example.php
git commit -m "Added config template"
git push
```

### ❌ Never Share Credentials

- Don't share `config.php` via email, chat, or screenshots
- Don't paste credentials in issue trackers or forums
- Don't store credentials in notes apps that sync to cloud

### ❌ Never Use Default Credentials

Always change example credentials:

```php
// ❌ BAD - Using example values
'db_host' => 'your-database-host.com',
'db_user' => 'your-database-user',

// ✅ GOOD - Real credentials
'db_host' => 'prod-db-cluster.aws.neon.tech',
'db_user' => 'app_user_2024',
```

---

## ✅ Production Deployment Checklist

### Before Deploying

- [ ] Copy `config.example.php` to `config.php`
- [ ] Update `config.php` with production credentials
- [ ] Set restrictive file permissions (600 for config.php)
- [ ] Verify `.gitignore` includes `config.php`
- [ ] Test database connection locally first
- [ ] Ensure `.htaccess` is uploaded and working
- [ ] Verify error reporting is disabled in production

### Security Headers

Consider adding these headers to `.htaccess`:

```apache
# Security headers
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-Content-Type-Options "nosniff"
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
```

### Error Handling

The application already includes production-safe error handling:

```php
// Production mode - hides errors from users
error_reporting(0);
ini_set('display_errors', 0);
```

For debugging, temporarily enable errors (only in development):

```php
// Development mode - shows errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

---

## 🔄 Credential Rotation

### How to Update Database Credentials

1. **Update Database Password**
   - Change password in your database provider (Neon, etc.)

2. **Update config.php**
   ```php
   'db_pass' => 'new_secure_password_here'
   ```

3. **Test Connection**
   ```bash
   php -r "require 'index.php'; echo 'Connected successfully';"
   ```

4. **Restart Application**
   - Restart PHP-FPM or web server
   - Clear any application caches

---

## 🚨 Security Incident Response

### If Credentials Are Exposed

1. **Immediately Rotate Credentials**
   - Change database password in provider
   - Update `config.php` with new credentials

2. **Check Database Logs**
   - Look for unauthorized access
   - Check for suspicious queries

3. **Review Recent Changes**
   - Check Git history for exposed credentials
   - If found, use tools like [BFG Repo-Cleaner](https://rtyley.github.io/bfg-repo-cleaner/) to remove them

4. **Notify Team**
   - Alert anyone with access to the application
   - Document the incident

### Verify Security

```bash
# Check if config.php is in Git
git log --all --full-history -- config.php

# Check if config.php is ignored
git check-ignore -v config.php

# Search for potential credential exposure
git log -S "password" --all
```

---

## 📚 Additional Resources

### Database Security (Neon PostgreSQL)

- [Neon Security Best Practices](https://neon.tech/docs/security/security-overview)
- [PostgreSQL SSL Connections](https://www.postgresql.org/docs/current/ssl-tcp.html)

### PHP Security

- [OWASP PHP Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)
- [PHP.net Security Guide](https://www.php.net/manual/en/security.php)

### Web Application Security

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [MDN Web Security](https://developer.mozilla.org/en-US/docs/Web/Security)

---

## 📞 Reporting Security Issues

If you discover a security vulnerability:

1. **Do NOT** open a public issue
2. Email: [your-email@example.com]
3. Provide details:
   - Description of vulnerability
   - Steps to reproduce
   - Potential impact
   - Suggested fix (if any)

---

## 📝 Version History

### Version 2.0.0 (Current)
- ✅ Separated database configuration from application code
- ✅ Added `config.php` / `config.example.php` system
- ✅ Updated `.gitignore` to exclude credentials
- ✅ Added comprehensive security documentation
- ✅ Implemented SSL-required database connections

### Version 1.0.0
- Basic expense tracking functionality
- PostgreSQL database backend
- Prepared statements for SQL injection protection

---

**Remember:** Security is an ongoing process, not a one-time setup! 🔒

