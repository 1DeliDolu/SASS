<?php
class AdminController
{
    private function loadAuth()
    {
        require_once __DIR__ . '/../lib/Auth.php';
    }
    private function requireAdmin()
    {
        $this->loadAuth();
        Auth::requireRole('admin');
    }

    public function index()
    {
        $this->requireAdmin();
        require_once __DIR__ . '/../models/UserModel.php';
        $userModel = new UserModel();
        $users = [];
        try {
            $users = $userModel->getAllUsers();
        } catch (Exception $e) {
            $users = [];
        }
        $data = [
            'title' => 'Admin Paneli',
            'users' => $users,
        ];
        include __DIR__ . '/../views/admin/index.php';
    }

    public function projects()
    {
        $this->requireAdmin();
        require_once __DIR__ . '/../models/ProjectModel.php';
        $pm = new ProjectModel();
        $projects = $pm->getAll();
        $data = ['title' => 'Projeler (Yönetim)', 'projects' => $projects];
        include __DIR__ . '/../views/admin/projects/index.php';
    }

    public function projectCreate()
    {
        $this->requireAdmin();
        require_once __DIR__ . '/../models/UserModel.php';
        $um = new UserModel();
        $musteriler = $um->getUsersByRole('musteri');
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/ProjectModel.php';
            $pm = new ProjectModel();
            $ad = $_POST['ad'] ?? '';
            $aciklama = $_POST['aciklama'] ?? '';
            $musteri_id = (int)($_POST['musteri_id'] ?? 0);
            $durum = $_POST['durum'] ?? 'yeni';
            if (trim($ad) === '') { $error = 'Proje adı gerekli.'; }
            if ($error === '') {
                $id = $pm->create($ad, $aciklama, $musteri_id ?: null, $durum);
                if ($id > 0) {
                    // Notify customer about new project
                    try {
                        require_once __DIR__ . '/../lib/Mailer.php';
                        $proj = $pm->get($id);
                        if (!empty($proj['musteri_mail'])) {
                            $sub = 'Yeni proje oluşturuldu: ' . (string)($proj['ad'] ?? '');
                            $html = '<p>Yeni proje oluşturuldu.</p><ul>' .
                                    '<li>Ad: ' . htmlspecialchars((string)($proj['ad'] ?? '')) . '</li>' .
                                    '<li>Durum: ' . htmlspecialchars((string)($proj['durum'] ?? '')) . '</li>' .
                                    '</ul>';
                            Mailer::send((string)$proj['musteri_mail'], $sub, $html);
                        }
                    } catch (Throwable $e) { /* ignore mail errors */ }
                    header('Location: /index.php?action=admin_projects');
                    exit;
                }
                $error = 'Kayıt başarısız.';
            }
        }
        $data = ['title' => 'Proje Ekle', 'musteriler' => $musteriler, 'error' => $error];
        include __DIR__ . '/../views/admin/projects/create.php';
    }

    public function projectEdit()
    {
        $this->requireAdmin();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        require_once __DIR__ . '/../models/ProjectModel.php';
        require_once __DIR__ . '/../models/UserModel.php';
        $pm = new ProjectModel();
        $um = new UserModel();
        $project = $pm->get($id);
        if (!$project) { http_response_code(404); echo 'Proje bulunamadı'; return; }
        $musteriler = $um->getUsersByRole('musteri');
        $calisanlar = $um->getUsersByRole('calisan');
        $assignments = $pm->getAssignments($id);
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ad = $_POST['ad'] ?? '';
            $aciklama = $_POST['aciklama'] ?? '';
            $musteri_id = (int)($_POST['musteri_id'] ?? 0);
            $durum = $_POST['durum'] ?? 'yeni';
            if (trim($ad) === '') { $error = 'Proje adı gerekli.'; }
            if ($error === '') {
                $ok = $pm->updateProject($id, $ad, $aciklama, $musteri_id ?: null, $durum);
                if ($ok) {
                    // Notify changes (basic)
                    try {
                        require_once __DIR__ . '/../lib/Mailer.php';
                        $new = $pm->get($id);
                        $subject = 'Proje güncellendi: ' . (string)($new['ad'] ?? '');
                        $html = '<p>Proje güncellendi.</p>' .
                                '<ul><li>Ad: ' . htmlspecialchars((string)($new['ad'] ?? '')) . '</li>' .
                                '<li>Durum: ' . htmlspecialchars((string)($new['durum'] ?? '')) . '</li></ul>';
                        if (!empty($new['musteri_mail'])) { Mailer::send((string)$new['musteri_mail'], $subject, $html); }
                        $assigns = $pm->getAssignments($id);
                        $to = [];
                        foreach ($assigns as $a) { if (!empty($a['mail'])) { $to[] = (string)$a['mail']; } }
                        if (!empty($to)) { Mailer::sendMany($to, $subject, $html); }
                    } catch (Throwable $e) { /* ignore */ }
                    header('Location: /index.php?action=admin_projects');
                    exit;
                }
                $error = 'Güncelleme başarısız.';
            }
        }

        $data = [
            'title' => 'Proje Düzenle',
            'project' => $project,
            'musteriler' => $musteriler,
            'calisanlar' => $calisanlar,
            'assignments' => $assignments,
            'error' => $error,
        ];
        include __DIR__ . '/../views/admin/projects/edit.php';
    }

    public function projectDelete()
    {
        $this->requireAdmin();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id > 0) {
            require_once __DIR__ . '/../models/ProjectModel.php';
            $pm = new ProjectModel();
            // collect info before delete
            $proj = $pm->get($id);
            $assigns = $pm->getAssignments($id);
            $pm->delete($id);
            // Notify customer and assigned employees
            try {
                require_once __DIR__ . '/../lib/Mailer.php';
                $sub = 'Proje silindi: ' . (string)($proj['ad'] ?? '');
                $html = '<p>Proje silindi.</p>';
                if (!empty($proj['musteri_mail'])) { Mailer::send((string)$proj['musteri_mail'], $sub, $html); }
                $to = [];
                foreach ($assigns as $a) { if (!empty($a['mail'])) { $to[] = (string)$a['mail']; } }
                if (!empty($to)) { Mailer::sendMany($to, $sub, $html); }
            } catch (Throwable $e) { /* ignore */ }
        }
        header('Location: /index.php?action=admin_projects');
        exit;
    }

    public function assignmentAdd()
    {
        $this->requireAdmin();
        $project_id = (int)($_POST['project_id'] ?? 0);
        $calisan_id = (int)($_POST['calisan_id'] ?? 0);
        $rol_projede = $_POST['rol_projede'] ?? null;
        if ($project_id && $calisan_id) {
            require_once __DIR__ . '/../models/ProjectModel.php';
            $pm = new ProjectModel();
            $pm->addAssignment($project_id, $calisan_id, $rol_projede);
            // Notify assigned employee
            try {
                require_once __DIR__ . '/../models/UserModel.php';
                require_once __DIR__ . '/../lib/Mailer.php';
                $um = new UserModel();
                $user = $um->getUser($calisan_id);
                $project = $pm->get($project_id);
                if (!empty($user['mail'])) {
                    $sub = 'Projeye atandınız: ' . (string)($project['ad'] ?? '');
                    $html = '<p>Projeye atandınız.</p>';
                    Mailer::send((string)$user['mail'], $sub, $html);
                }
            } catch (Throwable $e) { /* ignore */ }
        }
        header('Location: /index.php?action=admin_project_edit&id=' . $project_id);
        exit;
    }

    public function assignmentRemove()
    {
        $this->requireAdmin();
        $project_id = (int)($_POST['project_id'] ?? 0);
        $calisan_id = (int)($_POST['calisan_id'] ?? 0);
        if ($project_id && $calisan_id) {
            require_once __DIR__ . '/../models/ProjectModel.php';
            $pm = new ProjectModel();
            $pm->removeAssignment($project_id, $calisan_id);
            // Notify removed employee
            try {
                require_once __DIR__ . '/../models/UserModel.php';
                require_once __DIR__ . '/../lib/Mailer.php';
                $um = new UserModel();
                $user = $um->getUser($calisan_id);
                $project = $pm->get($project_id);
                if (!empty($user['mail'])) {
                    $sub = 'Projeden çıkarıldınız: ' . (string)($project['ad'] ?? '');
                    $html = '<p>Projeden çıkarıldınız.</p>';
                    Mailer::send((string)$user['mail'], $sub, $html);
                }
            } catch (Throwable $e) { /* ignore */ }
        }
        header('Location: /index.php?action=admin_project_edit&id=' . $project_id);
        exit;
    }

    public function updateRole()
    {
        $this->requireAdmin();
        require_once __DIR__ . '/../models/UserModel.php';
        $userModel = new UserModel();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $role = $_POST['role'] ?? '';
        if ($id > 0 && $role) {
            $userModel->setUserRole($id, $role);
        }
        header('Location: /index.php?action=admin');
        exit;
    }

    public function userCreate()
    {
        $this->requireAdmin();
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adi = trim($_POST['adi'] ?? '');
            $soyadi = trim($_POST['soyadi'] ?? '');
            $mail = trim($_POST['mail'] ?? '');
            $sifre = (string)($_POST['sifre'] ?? '');
            $role = trim($_POST['role'] ?? 'calisan');
            if ($adi === '' || $soyadi === '' || $mail === '' || $sifre === '') {
                $error = 'Lütfen tüm alanları doldurun.';
            } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $error = 'Geçersiz e-posta adresi.';
            } else {
                require_once __DIR__ . '/../models/UserModel.php';
                $um = new UserModel();
                $exists = $um->getUserByMail($mail);
                if ($exists) {
                    $error = 'Bu e-posta zaten kayıtlı.';
                } else {
                    $ok = $um->createUserWithRole($adi, $soyadi, $mail, $sifre, $role);
                    if ($ok) {
                        header('Location: /index.php?action=admin');
                        exit;
                    }
                    $error = 'Kullanıcı eklenemedi.';
                }
            }
        }
        $_SESSION['admin_user_create_error'] = $error;
        header('Location: /index.php?action=admin');
        exit;
    }

    public function milestoneAdd()
    {
        $this->requireAdmin();
        $project_id = (int)($_POST['project_id'] ?? 0);
        $baslik = trim($_POST['baslik'] ?? '');
        $due_date = trim($_POST['due_date'] ?? '');
        if (strpos($due_date, 'T') !== false) { $due_date = str_replace('T', ' ', $due_date); }
        if ($project_id && $baslik !== '' && $due_date !== '') {
            require_once __DIR__ . '/../models/ProjectModel.php';
            $pm = new ProjectModel();
            $pm->addMilestone($project_id, $baslik, $due_date);
            // Notify customer and assigned employees
            try {
                require_once __DIR__ . '/../lib/Mailer.php';
                $project = $pm->get($project_id);
                $assigns = $pm->getAssignments($project_id);
                $sub = 'Milestone eklendi: ' . (string)$baslik;
                $html = '<p>Yeni milestone eklendi: <strong>' . htmlspecialchars($baslik) . '</strong></p>' .
                        '<p>Termin: ' . htmlspecialchars($due_date) . '</p>';
                if (!empty($project['musteri_mail'])) { Mailer::send((string)$project['musteri_mail'], $sub, $html); }
                $to = [];
                foreach ($assigns as $a) { if (!empty($a['mail'])) { $to[] = (string)$a['mail']; } }
                if (!empty($to)) { Mailer::sendMany($to, $sub, $html); }
            } catch (Throwable $e) { /* ignore */ }
        }
        header('Location: /index.php?action=admin_project_edit&id=' . $project_id);
        exit;
    }

    public function milestoneDone()
    {
        $this->requireAdmin();
        $project_id = (int)($_POST['project_id'] ?? 0);
        $milestone_id = (int)($_POST['milestone_id'] ?? 0);
        if ($milestone_id) {
            require_once __DIR__ . '/../models/ProjectModel.php';
            $pm = new ProjectModel();
            $pm->markMilestoneDone($milestone_id);
            // Notify
            try {
                require_once __DIR__ . '/../lib/Mailer.php';
                $project = $pm->get($project_id);
                $assigns = $pm->getAssignments($project_id);
                $sub = 'Milestone tamamlandı';
                $html = '<p>Bir milestone tamamlandı.</p>';
                if (!empty($project['musteri_mail'])) { Mailer::send((string)$project['musteri_mail'], $sub, $html); }
                $to = [];
                foreach ($assigns as $a) { if (!empty($a['mail'])) { $to[] = (string)$a['mail']; } }
                if (!empty($to)) { Mailer::sendMany($to, $sub, $html); }
            } catch (Throwable $e) { /* ignore */ }
        }
        header('Location: /index.php?action=admin_project_edit&id=' . $project_id);
        exit;
    }

    public function userNew()
    {
        $this->requireAdmin();
        include __DIR__ . '/../views/admin/users/create.php';
    }
}
