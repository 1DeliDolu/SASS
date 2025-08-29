<?php
// Lightweight DB inspector: reads users, projects, project_assignments
// Uses .env and Database.php without running Seeder/bootstrap.

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../app/lib/Env.php';
Env::load(__DIR__ . '/../.env');

require_once __DIR__ . '/../app/lib/Database.php';

header('Content-Type: application/json; charset=utf-8');

function ok($data) {
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    exit(0);
}

function fail($msg, $ctx = []) {
    http_response_code(500);
    echo json_encode(['error' => $msg, 'context' => $ctx], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    exit(1);
}

try {
    $pdo = Database::pdo();
} catch (Throwable $e) {
    fail('DB connection failed', ['message' => $e->getMessage()]);
}

try {
    // Counts
    $counts = [
        'users' => (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
        'projects' => (int)$pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn(),
        'project_assignments' => (int)$pdo->query('SELECT COUNT(*) FROM project_assignments')->fetchColumn(),
    ];

    // Samples
    $users = $pdo->query('SELECT id, adi, soyadi, mail, role, son_giris_tarihi FROM users ORDER BY id ASC LIMIT 20')->fetchAll(PDO::FETCH_ASSOC);
    $projects = $pdo->query('SELECT id, ad, musteri_id, durum, olusturma_tarihi FROM projects ORDER BY id DESC LIMIT 20')->fetchAll(PDO::FETCH_ASSOC);
    $assignments = $pdo->query('SELECT project_id, calisan_id, rol_projede, atama_tarihi FROM project_assignments ORDER BY atama_tarihi DESC LIMIT 50')->fetchAll(PDO::FETCH_ASSOC);

    // Integrity checks the user cares about
    $sameRoleType = [];

    // Project -> customer relation check: projects.musteri_id must refer to a user with role/type = musteri
    $invalidProjectCustomer = [];
    if (!empty($projects)) {
        $stmt = $pdo->prepare('SELECT id, role FROM users WHERE id=? LIMIT 1');
        foreach ($projects as $p) {
            $mid = $p['musteri_id'];
            if ($mid === null) { continue; }
            $stmt->execute([$mid]);
            $u = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$u) {
                $invalidProjectCustomer[] = ['project_id' => (int)$p['id'], 'musteri_id' => (int)$mid, 'reason' => 'missing user'];
                continue;
            }
            $isCustomer = ($u['role'] === 'musteri');
            if (!$isCustomer) {
                $invalidProjectCustomer[] = ['project_id' => (int)$p['id'], 'musteri_id' => (int)$mid, 'reason' => 'user not customer'];
            }
        }
    }

    // Assignments referential integrity
    $invalidAssignments = [];
    if (!empty($assignments)) {
        $pStmt = $pdo->prepare('SELECT 1 FROM projects WHERE id=?');
        $uStmt = $pdo->prepare('SELECT role FROM users WHERE id=?');
        foreach ($assignments as $a) {
            $pid = (int)$a['project_id'];
            $uid = (int)$a['calisan_id'];
            $pStmt->execute([$pid]);
            if (!$pStmt->fetchColumn()) {
                $invalidAssignments[] = ['project_id' => $pid, 'calisan_id' => $uid, 'reason' => 'missing project'];
                continue;
            }
            $uStmt->execute([$uid]);
            $ur = $uStmt->fetch(PDO::FETCH_ASSOC);
            if (!$ur) {
                $invalidAssignments[] = ['project_id' => $pid, 'calisan_id' => $uid, 'reason' => 'missing user'];
                continue;
            }
            $isEmployee = ($ur['role'] === 'calisan');
            if (!$isEmployee) {
                $invalidAssignments[] = ['project_id' => $pid, 'calisan_id' => $uid, 'reason' => 'user not employee'];
            }
        }
    }

    ok([
        'db' => [
            'host' => getenv('DB_HOST'),
            'name' => getenv('DB_NAME'),
            'user' => getenv('DB_USER'),
        ],
        'counts' => $counts,
        'samples' => [
            'users' => $users,
            'projects' => $projects,
            'project_assignments' => $assignments,
        ],
        'checks' => [
            'users_with_same_role_and_type' => $sameRoleType,
            'invalid_project_customer_links' => $invalidProjectCustomer,
            'invalid_assignments' => $invalidAssignments,
        ],
    ]);
} catch (Throwable $e) {
    fail('Query failed', ['message' => $e->getMessage()]);
}
