# Quick Start: Deploy PHP to Render with Docker

## ⚠️ Important Note

**Render does NOT natively support PHP** ([see documentation](https://render.com/docs/language-support)). However, Render fully supports Docker, which allows you to deploy PHP applications.

**You MUST use Docker runtime** when deploying PHP applications to Render.

---

## TL;DR - 5 Minute Setup

### Step 1: Add 3 Files to Your Project (2 minutes)

Your project should have:

1. ✅ **`Dockerfile`** - Already created
2. ✅ **`.dockerignore`** - Already created  
3. ✅ **`config.php`** - Already updated to use environment variables

### Step 2: Push to GitHub (1 minute)

```bash
git add Dockerfile .dockerignore config.php
git commit -m "Add Docker support for Render deployment"
git push origin main
```

### Step 3: Deploy to Render (2 minutes)

1. **Go to:** https://render.com
2. **Sign up** with GitHub
3. **Click:** "New +" → "Web Service"
4. **Connect** your repository
5. **Configure:**
   - **Name:** `expense-tracker` (or your choice)
   - **Runtime:** **Docker** ⚠️ **MUST SELECT THIS**
   - **Plan:** Free
6. **Add Environment Variables:**
   ```
   PORT=80
   DB_HOST=ep-soft-band-adq7jlul-pooler.c-2.us-east-1.aws.neon.tech
   DB_PORT=5432
   DB_NAME=neondb
   DB_USER=neondb_owner
   DB_PASS=npg_qFrYv2eyG1UE
   DB_SSL=require
   GEMINI_API_KEY=AIzaSyDoUiiHMK2HC7czAIIMTRgtyWZFJuBJupY
   ```
7. **Click:** "Create Web Service"

### Step 4: Wait & Test (3-7 minutes)

- ⏳ Wait for build to complete (watch logs)
- ✅ Visit: `https://your-app-name.onrender.com`
- ✅ Test your application!

---

## Important Reminders

### ⚠️ Change Credentials After Deployment

Your credentials are exposed in this conversation. **After deploying:**

1. **Neon Database:** https://console.neon.tech → Reset password
2. **Gemini API:** https://makersuite.google.com/app/apikey → Generate new key
3. **Render:** Update environment variables with new credentials

### ⚠️ Free Tier Behavior

- ✅ **Free** - No credit card required
- ⏰ **Sleeps** after 15 minutes inactivity
- 🐌 **First load** takes 30-60 seconds after sleep (cold start)
- 🔄 **Auto-deploy** enabled (pushes to GitHub trigger deployments)

---

## What's Included

✅ **PHP 8.2** with Apache  
✅ **PostgreSQL extensions** (pdo_pgsql, pgsql)  
✅ **Composer** for dependencies  
✅ **Apache mod_rewrite** for URL routing  
✅ **Proper file permissions** for data/logs directories  

---

## Troubleshooting

### Build Failed?
- Check Dockerfile syntax
- Ensure files are pushed to GitHub
- View logs in Render dashboard

### App Shows 500 Error?
- Check environment variables are correct
- View logs for PHP errors
- Verify database connection

### Can't Connect to Database?
- Confirm DB_HOST, DB_USER, DB_PASS are correct
- Check Neon database is online (not paused)
- Verify PostgreSQL extensions in Dockerfile (already included)

---

## Complete Guide

For detailed step-by-step instructions, see:
📄 **RENDER_DOCKER_DEPLOYMENT.md**

---

## Summary

**Time:** ~10-15 minutes total  
**Cost:** $0 (Free tier)  
**Difficulty:** Easy (copy & paste)  
**Result:** PHP app with PostgreSQL on Render 🚀

Your app will be live at:
`https://your-app-name.onrender.com`

---

**Last Updated:** 2025  
**Version:** 1.0 - Quick Start Guide

