<?php
// Basit router ve MVC başlangıcı
session_start();
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/ProjectController.php';

$controller = new HomeController();
$admin = new AdminController();
$project = new ProjectController();
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
    case 'admin':
        $admin->index();
        break;
    case 'admin_update_role':
        $admin->updateRole();
        break;
    case 'admin_user_create':
        $admin->userCreate();
        break;
    case 'admin_projects':
        $admin->projects();
        break;
    case 'admin_project_create':
        $admin->projectCreate();
        break;
    case 'admin_project_edit':
        $admin->projectEdit();
        break;
    case 'admin_project_delete':
        $admin->projectDelete();
        break;
    case 'admin_assignment_add':
        $admin->assignmentAdd();
        break;
    case 'admin_assignment_remove':
        $admin->assignmentRemove();
        break;
    case 'admin_milestone_add':
        $admin->milestoneAdd();
        break;
    case 'admin_milestone_done':
        $admin->milestoneDone();
        break;
    case 'projects':
        $project->index();
        break;
    case 'project_show':
        $project->show();
        break;
    default:
        $controller->index();
        break;
}
