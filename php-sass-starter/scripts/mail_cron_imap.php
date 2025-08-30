<?php
require_once __DIR__ . '/../app/lib/Env.php';
Env::load(__DIR__ . '/../.env');
// Attempt autoload of composer if available
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
require_once __DIR__ . '/../app/mail/ImapSync.php';
ImapSync::run();
echo "OK\n";

