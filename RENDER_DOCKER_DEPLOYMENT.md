# Deploy PHP Application to Render using Docker

## ⚠️ Important: PHP Requires Docker on Render

According to [Render's documentation](https://render.com/docs/language-support), Render **does NOT natively support PHP**. However, Render fully supports Docker, which allows you to deploy PHP applications.

**Supported Languages (Native):**
- ✅ Node.js / Bun
- ✅ Python
- ✅ Ruby
- ✅ Go
- ✅ Rust
- ✅ Elixir

**PHP Applications:**
- ❌ **Not natively supported**
- ✅ **Must use Docker** (which this guide covers)

---

## Quick Start (5 Minutes)

### Step 1: Add Required Files to Your Project

Add these 3 files to your project root:

1. **`Dockerfile`** - Defines your PHP environment
2. **`.dockerignore`** - Optimizes build speed
3. **`config.php`** - Already updated to use environment variables ✅

### Step 2: Commit and Push to GitHub

```bash
git add Dockerfile .dockerignore config.php
git commit -m "Add Docker support for Render deployment"
git push origin main
```

### Step 3: Deploy to Render

1. Go to https://render.com and sign up with GitHub
2. Click **New +** → **Web Service**
3. Connect your repository
4. **CRITICAL**: Set **Runtime** to **Docker** ⚠️
5. Add environment variables (see below)
6. Click **"Create Web Service"**

### Step 4: Set Environment Variables

In Render Dashboard → Your Service → **Environment** tab, add:

```
PORT=80
DB_HOST=your-neon-host.neon.tech
DB_PORT=5432
DB_NAME=your-database-name
DB_USER=your-username
DB_PASS=your-password
DB_SSL=require
GEMINI_API_KEY=your-api-key (optional)
```

---

## Complete Step-by-Step Guide

### Part 1: Prepare Your Project

#### 1.1 Verify Dockerfile Exists

Your project root should have a `Dockerfile` with this content:

```dockerfile
FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (PostgreSQL support)
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Install dependencies
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/data \
    && chmod -R 777 /var/www/html/logs

# Configure Apache for Render's PORT variable
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

EXPOSE 80

CMD ["apache2-foreground"]
```

#### 1.2 Verify .dockerignore Exists

Create `.dockerignore` to exclude unnecessary files from Docker build:

```
.git
*.md
.env
vendor/
node_modules/
logs/
data/*.json
.DS_Store
```

#### 1.3 Verify config.php Uses Environment Variables

Your `config.php` should read from environment variables:

```php
return [
    'db_host' => getenv('DB_HOST') ?: 'localhost',
    'db_port' => getenv('DB_PORT') ?: '5432',
    'db_name' => getenv('DB_NAME') ?: 'neondb',
    'db_user' => getenv('DB_USER') ?: 'postgres',
    'db_pass' => getenv('DB_PASS') ?: '',
    'db_ssl' => getenv('DB_SSL') ?: 'require',
    'gemini_api_key' => getenv('GEMINI_API_KEY') ?: null,
];
```

---

### Part 2: Deploy to Render

#### 2.1 Create Render Account

1. Visit https://render.com
2. Click **"Get Started"** or **"Sign Up"**
3. **Sign up with GitHub** (recommended for easy repository connection)
4. Authorize Render to access your repositories
5. Verify email if required

#### 2.2 Create Web Service

1. From Render Dashboard, click **"New +"** (top right)
2. Select **"Web Service"**
3. Click **"Connect account"** if prompted
4. Select your GitHub repository
5. Click **"Connect"**

#### 2.3 Configure Service Settings

**Basic Information:**

| Setting | Value |
|---------|-------|
| **Name** | `expense-tracker` (choose a unique name) |
| **Region** | Choose closest to you (e.g., Oregon, Frankfurt) |
| **Branch** | `main` (or your default branch) |
| **Root Directory** | Leave blank (root of repository) |

**⚠️ CRITICAL - Environment Configuration:**

| Setting | Value |
|---------|-------|
| **Runtime** | **Docker** ⚠️ MUST SELECT THIS |
| **Dockerfile Path** | `Dockerfile` (if in root) |
| **Docker Build Context Directory** | `.` (root directory) |
| **Docker Command** | Leave blank (uses CMD from Dockerfile) |

**Instance Type:**

| Setting | Value |
|---------|-------|
| **Plan** | **Free** |

**Free Tier Details:**
- 512 MB RAM
- 0.1 CPU
- **Sleeps after 15 minutes of inactivity**
- 750 hours/month free
- First request after sleep takes 30-60 seconds (cold start)

#### 2.4 Set Environment Variables

**⚠️ CRITICAL**: Add these environment variables in Render Dashboard:

Click **"Add Environment Variable"** for each:

| Key | Value | Description |
|-----|-------|-------------|
| `PORT` | `80` | Apache port (Render will override) |
| `DB_HOST` | `ep-soft-band-adq7jlul-pooler.c-2.us-east-1.aws.neon.tech` | Your Neon PostgreSQL host |
| `DB_PORT` | `5432` | PostgreSQL port |
| `DB_NAME` | `neondb` | Your database name |
| `DB_USER` | `neondb_owner` | Database username |
| `DB_PASS` | `npg_qFrYv2eyG1UE` | Database password |
| `DB_SSL` | `require` | SSL mode for PostgreSQL |
| `GEMINI_API_KEY` | `AIzaSyDoUiiHMK2HC7czAIIMTRgtyWZFJuBJupY` | (Optional) Gemini API key |

**⚠️ SECURITY WARNING**: The credentials above are exposed in this conversation. After deployment:

1. **Change Neon database password** in Neon console
2. **Generate new Gemini API key** if needed
3. **Update environment variables** in Render Dashboard

#### 2.5 Advanced Settings (Optional)

- **Auto-Deploy**: ✅ Enabled (deploys automatically on git push)
- **Health Check Path**: `/api/health` (or leave blank)
- **Disk**: Not needed for PHP apps

#### 2.6 Create Web Service

1. Review all settings carefully
2. **Verify Runtime is set to Docker** ⚠️
3. Click **"Create Web Service"**
4. Render starts building your Docker image (takes 3-7 minutes)

---

### Part 3: Monitor Deployment

#### 3.1 Watch Build Progress

1. You'll automatically see the **Logs** tab
2. Watch the build progress:
   ```
   ==> Cloning from GitHub...
   ==> Building Docker image...
   Step 1/12 : FROM php:8.2-apache
   Step 2/12 : RUN apt-get update...
   Step 3/12 : RUN docker-php-ext-install...
   ...
   ==> Build successful ✓
   ==> Starting service...
   ==> Your service is live 🎉
   ```

**Build Time:**
- First build: **3-7 minutes** (downloads everything)
- Subsequent builds: **1-3 minutes** (caching speeds it up)

#### 3.2 Access Your Application

Once deployment completes:

1. Status changes to **"Live"** (green dot)
2. Your app URL: `https://your-app-name.onrender.com`
3. Click the URL to access your application

**⚠️ Important:** 
- **First request** takes 30-60 seconds (cold start on free tier)
- After inactivity, app sleeps and takes time to wake up
- This is normal for free tier

---

### Part 4: Verify Deployment

#### 4.1 Test Application

1. Visit your app URL: `https://your-app-name.onrender.com`
2. Test login functionality
3. Verify database connection works
4. Check that expenses load correctly

#### 4.2 Test API Endpoints

1. Visit: `https://your-app-name.onrender.com/api/health.php`
   - Should return: `{"success":true,"data":{"status":"healthy",...}}`

2. Visit: `https://your-app-name.onrender.com/api/routes`
   - Should return list of available API routes

#### 4.3 Check Logs for Errors

In Render Dashboard → Your Service → **Logs** tab:

1. Look for any PHP errors
2. Check database connection messages
3. Verify Apache is running correctly

**Common log messages:**
- ✅ `Server started` - Apache is running
- ✅ `Database connection successful` - Database connected
- ❌ `Database connection failed` - Check environment variables
- ❌ `PHP Fatal error` - Check PHP errors

---

### Part 5: Update Security (IMPORTANT!)

#### 5.1 Change Neon Database Password

**⚠️ Your credentials were exposed in this conversation!**

1. Go to https://console.neon.tech
2. Sign in to your account
3. Select your database project
4. Navigate to **Settings** → **Password**
5. Click **"Reset Password"**
6. **Copy the new password**

#### 5.2 Update Render Environment Variables

1. In Render Dashboard, go to your service
2. Click **"Environment"** tab
3. Find `DB_PASS` variable
4. Click **edit icon** (pencil)
5. Paste the **new password**
6. Click **"Save Changes"**
7. Service automatically redeploys (2-3 minutes)

#### 5.3 Generate New Gemini API Key (Optional)

If you're using Gemini API:

1. Go to https://makersuite.google.com/app/apikey
2. Create a new API key
3. Update `GEMINI_API_KEY` in Render (same process)
4. **Delete the old API key** in Google Cloud Console

---

## Troubleshooting

### Issue 1: Build Fails - "Docker build failed"

**Causes:**
- Syntax error in Dockerfile
- Missing dependencies
- Composer install fails

**Solutions:**
1. Check build logs for specific error
2. Test Dockerfile locally:
   ```bash
   docker build -t test-app .
   ```
3. Fix errors and push changes

### Issue 2: App Returns 500 Error

**Causes:**
- Database connection failed
- PHP errors
- Missing environment variables
- Permission issues

**Solutions:**
1. Check Render logs for PHP errors
2. Verify all environment variables are set
3. Test database connection from logs
4. Check file permissions in Dockerfile

### Issue 3: Database Connection Failed

**Causes:**
- Wrong environment variables
- Neon database not accessible
- SSL mode mismatch

**Solutions:**
1. Double-check all `DB_*` environment variables
2. Verify PostgreSQL extensions are installed (they are in Dockerfile)
3. Test Neon connection from another tool
4. Check if database is paused (some free services pause inactive databases)

### Issue 4: "Port already in use" Error

**Solution:**
This shouldn't happen on Render, but if testing locally:
```bash
# Use different port locally
docker run -p 8080:80 my-app
```

### Issue 5: Slow First Load (Cold Start)

**Cause:** Free tier sleeps after 15 minutes inactivity

**Solutions:**
- Normal behavior for free tier
- First request wakes service (30-60 seconds)
- Upgrade to paid tier ($7/month) for always-on
- Use uptime monitor to ping app every 10 minutes

### Issue 6: Apache Not Starting

**Causes:**
- Port configuration error
- Permission issues
- Syntax error in .htaccess

**Solutions:**
1. Check logs for Apache errors
2. Verify Dockerfile has correct port configuration
3. Check .htaccess syntax

---

## Customization

### Change PHP Version

Edit Dockerfile:
```dockerfile
FROM php:8.3-apache  # Instead of 8.2
```

### Add More PHP Extensions

Edit Dockerfile:
```dockerfile
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    zip \
    gd \          # Add GD for image processing
    mysqli        # Add MySQL support
```

### If index.php is in 'public/' folder

Edit Dockerfile:
```dockerfile
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
```

### Increase PHP Memory Limit

Add to Dockerfile before CMD:
```dockerfile
RUN echo "memory_limit = 256M" > /usr/local/etc/php/conf.d/memory.ini
```

---

## Free Tier Limitations

**What's Free:**
- ✅ Web service (with limitations)
- ✅ PostgreSQL database (90 days free, then sleeps after inactivity)
- ✅ Auto-deploy from GitHub
- ✅ Free SSL certificate
- ✅ Custom domains

**Limitations:**
- ❌ Sleeps after 15 minutes inactivity
- ❌ Cold start takes 30-60 seconds
- ❌ 512 MB RAM
- ❌ 0.1 CPU
- ❌ 750 hours/month (enough for most use cases)

**Paid Tier ($7/month):**
- ✅ Always-on (no sleeping)
- ✅ Faster cold starts
- ✅ More resources
- ✅ Better performance

---

## Summary

✅ **What You Achieved:**
- Deployed PHP application to Render using Docker
- PostgreSQL support with pdo_pgsql extension
- Apache web server configured
- Automatic deployments from GitHub
- Free SSL certificate
- Environment variable management

**Your App URL:**
`https://your-app-name.onrender.com`

**Next Steps:**
1. ✅ Change database password
2. ✅ Test all features
3. ✅ Monitor logs
4. ✅ Update domain (optional)

---

## References

- **Render Documentation**: https://render.com/docs/docker
- **Language Support**: https://render.com/docs/language-support
- **Docker Support**: https://render.com/docs/docker
- **PHP Docker Images**: https://hub.docker.com/_/php
- **Neon Database**: https://neon.tech

---

**Last Updated:** 2025  
**Version:** 2.0 - Docker Deployment Guide  
**Status:** ✅ Production Ready

