<?php
require_once __DIR__ . '/../app/lib/Env.php';
Env::load(__DIR__ . '/../.env');
require_once __DIR__ . '/../app/mail/EmailRepo.php';
$repo = new EmailRepo();
$repo->runScheduler();
echo "OK\n";

