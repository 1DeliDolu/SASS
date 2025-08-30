<?php
// Load .env and initialize common app settings
require_once __DIR__ . '/lib/Env.php';
Env::load(__DIR__ . '/../.env');
// Composer autoload (for PHPMailer, php-imap etc.)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Default timezone (override via APP_TZ in .env)
$tz = getenv('APP_TZ') ?: 'Europe/Berlin';
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

// Optional security headers
$sec = strtolower((string)(getenv('APP_SECURE_HEADERS') ?: 'false'));
if ($sec && $sec !== '0' && $sec !== 'false') {
    // do not override if already sent
    if (!headers_sent()) {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        if ($isHttps) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        // Permissive initial CSP to avoid breakage; tighten later as needed
        $csp = "default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self' https://www.google.com https://www.gstatic.com; frame-src https://www.google.com";
        header('Content-Security-Policy: ' . $csp);
        header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
    }
}
