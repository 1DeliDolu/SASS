<?php
require_once __DIR__ . '/../lib/Auth.php';

class ProjectController
{
    public function index()
    {
        Auth::requireLogin();
        require_once __DIR__ . '/../models/ProjectModel.php';
        $pm = new ProjectModel();
        $user = $_SESSION['user'];
        $role = $user['role'] ?? 'calisan';
        if ($role === 'admin') {
            $projects = $pm->getAll();
        } elseif ($role === 'musteri') {
            $projects = $pm->getByCustomer($user['id']);
        } else {
            $projects = $pm->getByAssignee($user['id']);
        }
        // Attach next milestone if any
        if (is_array($projects)) {
            foreach ($projects as &$p) {
                $p['next_milestone'] = $pm->getNextMilestone($p['id']);
            }
            unset($p);
        }
        $data = ['title' => 'Projeler', 'projects' => $projects];
        include __DIR__ . '/../views/projects/index.php';
    }

    public function show()
    {
        Auth::requireLogin();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(404);
            echo 'Proje bulunamadı';
            return;
        }
        require_once __DIR__ . '/../models/ProjectModel.php';
        $pm = new ProjectModel();
        $project = $pm->get($id);
        if (!$project) {
            http_response_code(404);
            echo 'Proje bulunamadı';
            return;
        }
        $assignments = $pm->getAssignments($id);
        $data = ['title' => 'Proje Detayı', 'project' => $project, 'assignments' => $assignments];
        include __DIR__ . '/../views/projects/show.php';
    }
}
