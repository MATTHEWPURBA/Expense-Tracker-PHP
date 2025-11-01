<?php
/**
 * Database Configuration
 * 
 * This file reads credentials from environment variables.
 * Safe for version control!
 * 
 * @version 2.1.0
 */

return [
    'db_host' => getenv('DB_HOST') ?: 'ep-soft-band-adq7jlul-pooler.c-2.us-east-1.aws.neon.tech',
    'db_port' => getenv('DB_PORT') ?: '5432',
    'db_name' => getenv('DB_NAME') ?: 'neondb',
    'db_user' => getenv('DB_USER') ?: 'neondb_owner',
    'db_pass' => getenv('DB_PASS') ?: 'npg_qFrYv2eyG1UE',
    'db_ssl' => getenv('DB_SSL') ?: 'require',
    
    // Google Gemini API Configuration
    // Get your FREE API key from: https://makersuite.google.com/app/apikey
    // Leave empty or set to null to disable AI features
    'gemini_api_key' => getenv('GEMINI_API_KEY') ?: null
];
