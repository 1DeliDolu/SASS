<?php
// Basit router ve MVC başlangıcı
session_start();
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/ProjectController.php';
require_once __DIR__ . '/../app/models/MessageModel.php';
require_once __DIR__ . '/../app/mail/EmailRepo.php';

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
    case 'forgot_password':
        $controller->forgotPassword();
        break;
    case 'reset_password':
        $controller->resetPassword();
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
    // Mail module (FEHLER.md)
    case 'mail_inbox':
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['user'])) { header('Location: /index.php?action=login'); exit; }
        $me = $_SESSION['user'];
        $repo = new EmailRepo();
        $list = $repo->listInbox((string)($me['mail'] ?? ''));
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $msgId = isset($_GET['msg']) ? (int)$_GET['msg'] : 0;
        $thread = $id ? $repo->getThread($id, (string)($me['mail'] ?? '')) : ['thread'=>null,'messages'=>[]];
        $data = [
            'active' => 'inbox',
            'messages' => $list['messages'] ?? [],
            'thread' => $thread['thread'] ?? null,
            'threadMessages' => $thread['messages'] ?? [],
            'selectedMsgId' => $msgId,
        ];
        include __DIR__ . '/../app/views/mail/inbox.php';
        break;
    case 'mail_sent':
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['user'])) { header('Location: /index.php?action=login'); exit; }
        $me = $_SESSION['user'];
        $repo = new EmailRepo();
        $list = $repo->listSent((string)($me['mail'] ?? ''));
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $msgId = isset($_GET['msg']) ? (int)$_GET['msg'] : 0;
        $thread = $id ? $repo->getThread($id, (string)($me['mail'] ?? '')) : ['thread'=>null,'messages'=>[]];
        $data = [
            'active' => 'sent',
            'messages' => $list['messages'] ?? [],
            'thread' => $thread['thread'] ?? null,
            'threadMessages' => $thread['messages'] ?? [],
            'selectedMsgId' => $msgId,
        ];
        include __DIR__ . '/../app/views/mail/inbox.php';
        break;
    case 'mail_scheduled':
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['user'])) { header('Location: /index.php?action=login'); exit; }
        $me = $_SESSION['user'];
        $repo = new EmailRepo();
        $date = isset($_GET['date']) ? preg_replace('/[^0-9\-]/','', $_GET['date']) : null;
        $list = $repo->listScheduled((string)($me['mail'] ?? ''), $date);
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $msgId = isset($_GET['msg']) ? (int)$_GET['msg'] : 0;
        $thread = $id ? $repo->getThread($id, (string)($me['mail'] ?? '')) : ['thread'=>null,'messages'=>[]];
        $data = [
            'active' => 'scheduled',
            'messages' => $list['messages'] ?? [],
            'thread' => $thread['thread'] ?? null,
            'threadMessages' => $thread['messages'] ?? [],
            'selectedDate' => $date,
            'selectedMsgId' => $msgId,
        ];
        include __DIR__ . '/../app/views/mail/inbox.php';
        break;
    case 'mail_thread':
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['user'])) { header('Location: /index.php?action=login'); exit; }
        $me = $_SESSION['user'];
        $repo = new EmailRepo();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $data = $repo->getThread($id, (string)($me['mail'] ?? ''));
        include __DIR__ . '/../app/views/mail/thread.php';
        break;
    case 'mail_compose':
        // Provide user emails to compose view for dropdown selection and show mail sidebar
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['user'])) { header('Location: /index.php?action=login'); exit; }
        require_once __DIR__ . '/../app/models/UserModel.php';
        $um = new UserModel();
        $users = $um->getAllUsers();
        $data = ['users' => $users, 'active' => 'compose'];
        include __DIR__ . '/../app/views/mail/compose.php';
        break;
    case 'mail_send':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $repo = new EmailRepo();
            header('Content-Type: application/json');
            echo json_encode($repo->send($_POST, $_FILES));
            break;
        }
        http_response_code(405);
        echo 'Method Not Allowed';
        break;
    case 'messages':
        $controller->messages();
        break;
    case 'messages_partial':
        $controller->messagesPartial();
        break;
    case 'messages_archive':
        $controller->messagesArchive();
        break;
    case 'messages_unarchive':
        $controller->messagesUnarchive();
        break;
    case 'send_message':
        $controller->sendMessage();
        break;
    case 'admin':
        $admin->index();
        break;
    case 'admin_update_role':
        $admin->updateRole();
        break;
    case 'admin_user_new':
        $admin->userNew();
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
