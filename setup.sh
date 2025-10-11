#!/bin/bash

###############################################################################
# Expense Tracker - Automated Setup Script
# This script helps you quickly set up the Expense Tracker application
###############################################################################

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Functions
print_header() {
    echo -e "${PURPLE}"
    echo "╔═══════════════════════════════════════════════════════╗"
    echo "║       💰 EXPENSE TRACKER - SETUP WIZARD 💰           ║"
    echo "║                  Version 1.0.0                       ║"
    echo "╚═══════════════════════════════════════════════════════╝"
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

# Check if PHP is installed
check_php() {
    print_step "Checking PHP installation..."
    
    if ! command -v php &> /dev/null; then
        print_error "PHP is not installed!"
        print_info "Please install PHP 7.4 or higher:"
        echo ""
        echo "  macOS:   brew install php"
        echo "  Ubuntu:  sudo apt install php"
        echo "  Windows: Download from https://windows.php.net/download/"
        echo ""
        exit 1
    fi
    
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    print_success "PHP $PHP_VERSION is installed"
    
    # Check PHP version
    PHP_MAJOR=$(php -r "echo PHP_MAJOR_VERSION;")
    PHP_MINOR=$(php -r "echo PHP_MINOR_VERSION;")
    
    if [ "$PHP_MAJOR" -lt 7 ] || ([ "$PHP_MAJOR" -eq 7 ] && [ "$PHP_MINOR" -lt 4 ]); then
        print_error "PHP version must be 7.4 or higher (found: $PHP_VERSION)"
        exit 1
    fi
}

# Setup configuration file
setup_config() {
    print_step "Setting up database configuration..."
    
    if [ ! -f "config.example.php" ]; then
        print_error "config.example.php not found!"
        exit 1
    fi
    
    if [ -f "config.php" ]; then
        print_info "config.php already exists"
    else
        print_warning "config.php not found - creating from template"
        cp config.example.php config.php
        print_success "Created config.php from template"
        echo ""
        print_warning "IMPORTANT: Edit config.php with your database credentials!"
        print_info "You can edit it later using: nano config.php"
    fi
}

# Create data directory
setup_data_directory() {
    print_step "Setting up data directory..."
    
    if [ ! -d "data" ]; then
        mkdir -p data
        print_success "Created data/ directory"
    else
        print_info "data/ directory already exists"
    fi
    
    # Set permissions
    chmod 755 data
    print_success "Set permissions for data/ directory"
    
    # Create .gitkeep if it doesn't exist
    if [ ! -f "data/.gitkeep" ]; then
        touch data/.gitkeep
        print_success "Created data/.gitkeep"
    fi
}

# Check if files exist
check_files() {
    print_step "Checking required files..."
    
    REQUIRED_FILES=("index.php" ".htaccess")
    MISSING_FILES=()
    
    for file in "${REQUIRED_FILES[@]}"; do
        if [ ! -f "$file" ]; then
            MISSING_FILES+=("$file")
        fi
    done
    
    if [ ${#MISSING_FILES[@]} -ne 0 ]; then
        print_error "Missing required files:"
        for file in "${MISSING_FILES[@]}"; do
            echo "  - $file"
        done
        exit 1
    fi
    
    print_success "All required files present"
}

# Test PHP server
start_server() {
    print_step "Starting PHP development server..."
    
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

# Display info
display_info() {
    echo ""
    echo -e "${PURPLE}═══════════════════════════════════════════════════${NC}"
    print_success "Setup completed successfully!"
    echo -e "${PURPLE}═══════════════════════════════════════════════════${NC}"
    echo ""
    
    echo -e "${CYAN}Next Steps:${NC}"
    echo ""
    echo "  1. Configure your database (if not done yet):"
    echo -e "     ${GREEN}nano config.php${NC}"
    echo ""
    echo "  2. Start the development server:"
    echo -e "     ${GREEN}php -S localhost:8000${NC}"
    echo ""
    echo "  3. Open your browser:"
    echo -e "     ${GREEN}http://localhost:8000${NC}"
    echo ""
    echo "  4. Start tracking expenses!"
    echo ""
    
    echo -e "${CYAN}Deployment:${NC}"
    echo ""
    echo "  • For free hosting: Read DEPLOYMENT.md"
    echo "  • For quick start: Read QUICK_START.md"
    echo "  • For full docs: Read README.md"
    echo ""
    
    echo -e "${PURPLE}═══════════════════════════════════════════════════${NC}"
}

# Run Git setup if needed
setup_git() {
    if [ -d ".git" ]; then
        print_info "Git repository already initialized"
        return
    fi
    
    echo ""
    print_step "Would you like to initialize a Git repository? (y/n)"
    read -r response
    
    if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
        git init
        print_success "Initialized Git repository"
        
        echo ""
        print_step "Add files to Git? (y/n)"
        read -r response
        
        if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
            git add .
            git commit -m "Initial commit - Expense Tracker v1.0"
            print_success "Created initial commit"
        fi
    fi
}

# Main setup process
main() {
    print_header
    
    echo ""
    check_php
    echo ""
    check_files
    echo ""
    setup_config
    echo ""
    setup_data_directory
    echo ""
    setup_git
    echo ""
    display_info
    
    # Ask if user wants to start server
    echo ""
    print_step "Start development server now? (y/n)"
    read -r response
    
    if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
        echo ""
        start_server
    else
        echo ""
        print_info "You can start the server later with: php -S localhost:8000"
        echo ""
    fi
}

# Run main function
main


