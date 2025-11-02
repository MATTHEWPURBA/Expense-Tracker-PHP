<?php
/**
 * Database Configuration
 * 
 * This file reads credentials from environment variables for security.
 * Falls back to default values for local development.
 * 
 * PRODUCTION: Set environment variables in Render Dashboard
 * DEVELOPMENT: Values will fall back to the defaults below
 * 
 * ⚠️ IMPORTANT: Never commit actual credentials to Git!
 * 
 * @version 3.0.0
 */

return [
    // PostgreSQL Database Configuration
    'db_host' => getenv('DB_HOST') ?: 'localhost',
    'db_port' => getenv('DB_PORT') ?: '5432',
    'db_name' => getenv('DB_NAME') ?: 'neondb',
    'db_user' => getenv('DB_USER') ?: 'postgres',
    'db_pass' => getenv('DB_PASS') ?: '',
    'db_ssl'  => getenv('DB_SSL') ?: 'require',
    
    // Google Gemini API Configuration
    // Get your API key from: https://makersuite.google.com/app/apikey
    // Leave empty or set to null to disable AI features
    'gemini_api_key' => getenv('GEMINI_API_KEY') ?: null,
];
