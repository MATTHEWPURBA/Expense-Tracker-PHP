# 💰 Expense Tracker - Super Simple Commands

Just like `php artisan serve` but for your Expense Tracker!

## 🚀 Quick Start

```bash
# Just run this to start everything:
./serve

# That's it! Your app will be running at http://localhost:8000
```

## 📋 Super Simple Commands

| Command | What it does |
|---------|-------------|
| `./serve` | Start the server (like php artisan serve) |
| `./stop` | Stop the server |
| `./check` | Check if everything is working |

## 🔧 Usage Examples

### Starting the Application
```bash
./serve
```
This will:
- Start the PHP development server on `http://localhost:8000`
- Automatically open your browser
- Show server status

### Checking if Everything Works
```bash
./check
```
This will check:
- ✅ PHP version and installation
- ✅ Database connection
- ✅ Server status

## 🛠️ Troubleshooting

### Server Won't Start
```bash
# Check if everything is working
./check

# Stop and start again
./stop
./serve
```

### Permission Issues
```bash
# Make scripts executable
chmod +x serve stop check
```

## 📁 Project Structure

```
expense-tracker/
├── manage.sh              # This management script
├── index.php              # Main application
├── config.php             # Database configuration
├── bootstrap.php          # Application bootstrap
├── composer.json          # PHP dependencies
├── data/                  # JSON data files (legacy)
├── logs/                  # Application logs
├── backups/               # Data backups
└── src/                   # Source code
```

## 🔒 Security Notes

- The script respects your existing configuration
- Database credentials are read from `config.php`
- Backups are stored locally in the `backups/` directory
- No sensitive data is logged or exposed

## 🆘 Getting Help

```bash
# Check if everything is working
./check
```

## 🎯 Features

- **Super simple** - Just run `./serve` to start (like php artisan serve)
- **Automatic browser opening** when starting server
- **Port conflict detection** and automatic fallback
- **Quick health check** to see if everything works
- **Cross-platform compatibility** (macOS, Linux, Windows with WSL)

---

**Made with ❤️ for the Expense Tracker project**
