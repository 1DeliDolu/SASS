<?php
// Basit router ve MVC başlangıcı
require_once __DIR__ . '/../app/controllers/HomeController.php';

$controller = new HomeController();
$action = $_GET['action'] ?? '';
switch ($action) {
    case 'register':
        $controller->register();
        break;
    case 'login':
        $controller->login();
        break;
    default:
        $controller->index();
        break;
}