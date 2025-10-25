#!/bin/bash

###############################################################################
# Expense Tracker - Project Management Script
# Simple script to manage the Expense Tracker PHP application
###############################################################################

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Project directory
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR"

# Functions
print_header() {
    echo -e "${PURPLE}"
    echo "💰 Expense Tracker - Simple Manager"
    echo -e "${NC}"
}

print_step() {
    echo -e "${CYAN}➜ $1${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

# Check if server is running
check_server() {
    if curl -s -o /dev/null -w "%{http_code}" http://localhost:8000 > /dev/null 2>&1; then
        return 0
    else
        return 1
    fi
}

# Start the development server
start_server() {
    print_step "Starting PHP development server..."
    
    if check_server; then
        print_warning "Server is already running on http://localhost:8000"
        return 0
    fi
    
    # Check if port 8000 is available
    if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null 2>&1; then
        print_warning "Port 8000 is already in use"
        print_info "Trying port 8001..."
        PORT=8001
    else
        PORT=8000
    fi
    
    print_success "Starting server on http://localhost:$PORT"
    echo ""
    print_info "Server is running! Press Ctrl+C to stop."
    echo ""
    
    # Open browser (optional)
    if command -v xdg-open &> /dev/null; then
        xdg-open "http://localhost:$PORT" &> /dev/null
    elif command -v open &> /dev/null; then
        open "http://localhost:$PORT" &> /dev/null
    fi
    
    php -S localhost:$PORT
}

# Stop the development server
stop_server() {
    print_step "Stopping PHP development server..."
    
    # Find and kill PHP server processes
    PIDS=$(pgrep -f "php -S localhost:8000")
    if [ -n "$PIDS" ]; then
        echo "$PIDS" | xargs kill
        print_success "Server stopped"
    else
        print_info "No server process found"
    fi
}

# Check project status
check_status() {
    print_step "Checking project status..."
    echo ""
    
    # Check PHP version
    PHP_VERSION=$(php -r "echo PHP_VERSION;" 2>/dev/null)
    if [ $? -eq 0 ]; then
        print_success "PHP $PHP_VERSION is installed"
    else
        print_error "PHP is not installed or not in PATH"
    fi
    
    # Check Composer
    if command -v composer &> /dev/null; then
        COMPOSER_VERSION=$(composer --version 2>/dev/null | head -n1)
        print_success "Composer is installed: $COMPOSER_VERSION"
    else
        print_error "Composer is not installed"
    fi
    
    # Check configuration
    if [ -f "config.php" ]; then
        print_success "Configuration file exists"
    else
        print_error "Configuration file (config.php) not found"
    fi
    
    # Check database connection
    if php -r "require_once 'bootstrap.php';" 2>/dev/null; then
        print_success "Database connection is working"
    else
        print_error "Database connection failed"
    fi
    
    # Check server status
    if check_server; then
        print_success "Development server is running on http://localhost:8000"
    else
        print_info "Development server is not running"
    fi
    
    echo ""
}

# Install dependencies
install_deps() {
    print_step "Installing PHP dependencies..."
    
    if [ ! -f "composer.json" ]; then
        print_error "composer.json not found"
        return 1
    fi
    
    if command -v composer &> /dev/null; then
        composer install
        print_success "Dependencies installed"
    else
        print_error "Composer is not installed"
        return 1
    fi
}

# Run database migration
migrate_db() {
    print_step "Running database migration..."
    
    if [ -f "migrate.php" ]; then
        php migrate.php
        print_success "Database migration completed"
    else
        print_error "Migration script (migrate.php) not found"
    fi
}

# Show logs
show_logs() {
    print_step "Showing application logs..."
    echo ""
    
    if [ -d "logs" ]; then
        if [ -f "logs/error.log" ]; then
            print_info "Error Log:"
            tail -20 logs/error.log
            echo ""
        fi
        
        if [ -f "logs/login_attempts.log" ]; then
            print_info "Login Attempts Log:"
            tail -10 logs/login_attempts.log
            echo ""
        fi
    else
        print_info "No logs directory found"
    fi
}

# Backup data
backup_data() {
    print_step "Creating data backup..."
    
    BACKUP_DIR="backups/$(date +%Y%m%d_%H%M%S)"
    mkdir -p "$BACKUP_DIR"
    
    # Backup JSON files if they exist
    if [ -d "data" ]; then
        cp -r data "$BACKUP_DIR/"
        print_success "JSON data backed up to $BACKUP_DIR"
    fi
    
    # Export database data
    if php -r "require_once 'bootstrap.php';" 2>/dev/null; then
        php -r "
        require_once 'bootstrap.php';
        use ExpenseTracker\Services\Database;
        \$db = Database::getInstance();
        \$pdo = \$db->getConnection();
        
        // Export users
        \$users = \$pdo->query('SELECT * FROM users')->fetchAll(PDO::FETCH_ASSOC);
        file_put_contents('$BACKUP_DIR/users.json', json_encode(\$users, JSON_PRETTY_PRINT));
        
        // Export expenses
        \$expenses = \$pdo->query('SELECT * FROM expenses')->fetchAll(PDO::FETCH_ASSOC);
        file_put_contents('$BACKUP_DIR/expenses.json', json_encode(\$expenses, JSON_PRETTY_PRINT));
        
        // Export categories
        \$categories = \$pdo->query('SELECT * FROM categories')->fetchAll(PDO::FETCH_ASSOC);
        file_put_contents('$BACKUP_DIR/categories.json', json_encode(\$categories, JSON_PRETTY_PRINT));
        
        echo 'Database data exported successfully\n';
        " 2>/dev/null
        
        print_success "Database data backed up to $BACKUP_DIR"
    fi
    
    print_success "Backup completed: $BACKUP_DIR"
}

# Show help
show_help() {
    echo ""
    echo -e "${CYAN}Super Simple Commands:${NC}"
    echo ""
    echo "  ./serve           - Start the server (like php artisan serve)"
    echo "  ./stop            - Stop the server"
    echo "  ./check           - Check if everything is working"
    echo ""
    echo -e "${CYAN}That's it! Just run ./serve to start.${NC}"
    echo ""
}

# Main function
main() {
    print_header
    
    case "${1:-start}" in
        "stop")
            stop_server
            ;;
        "check")
            check_status
            ;;
        "help")
            show_help
            ;;
        "start"|*)
            start_server
            ;;
    esac
}

# Run main function with all arguments
main "$@"
