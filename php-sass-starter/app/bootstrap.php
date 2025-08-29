<?php
// Load .env and initialize common app settings
require_once __DIR__ . '/lib/Env.php';
Env::load(__DIR__ . '/../.env');

// Default timezone (override via APP_TZ in .env)
$tz = getenv('APP_TZ') ?: 'UTC';
@date_default_timezone_set($tz);

// Display errors in dev
$env = strtolower((string)(getenv('APP_ENV') ?: 'development'));
if (in_array($env, ['dev','development','local'], true)) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
    // Auto seed sample data in dev
    require_once __DIR__ . '/lib/Seeder.php';
    Seeder::run();
}
