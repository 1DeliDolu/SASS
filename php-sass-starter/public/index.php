<?php
// Basit router ve MVC başlangıcı
session_start();
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
    case 'profile':
        $controller->profile();
        break;
    case 'update':
        $controller->update();
        break;
    case 'logout':
        $controller->logout();
        break;
    case 'search':
        $controller->search();
        break;
    case 'docs':
        $controller->docs();
        break;
    default:
        $controller->index();
        break;
}
