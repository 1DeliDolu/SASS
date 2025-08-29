<?php
class Seeder
{
    public static function run(): void
    {
        require_once __DIR__ . '/Database.php';
        $pdo = Database::pdo();

        // Seed users (admin, 2 customers, 2 employees)
        $users = [
            ['adi' => 'Admin', 'soyadi' => 'User', 'mail' => 'admin@example.com',  'role' => 'admin',   'pass' => 'Admin123!@#%'],
            ['adi' => 'Musteri', 'soyadi' => 'Bir', 'mail' => 'musteri1@example.com','role' => 'musteri','pass' => 'Customer123!@#'],
            ['adi' => 'Musteri', 'soyadi' => 'Iki', 'mail' => 'musteri2@example.com','role' => 'musteri','pass' => 'Customer123!@#'],
            ['adi' => 'Calisan', 'soyadi' => 'Bir', 'mail' => 'calisan1@example.com','role' => 'calisan','pass' => 'Employee123!@#'],
            ['adi' => 'Calisan', 'soyadi' => 'Iki', 'mail' => 'calisan2@example.com','role' => 'calisan','pass' => 'Employee123!@#'],
        ];

        $userIds = [];
        foreach ($users as $u) {
            $id = self::getUserIdByMail($pdo, $u['mail']);
            if (!$id) {
                $stmt = $pdo->prepare('INSERT INTO users (adi, soyadi, mail, sifre, role, olusturma_tarihi, guncelleme_tarihi) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
                $stmt->execute([
                    $u['adi'], $u['soyadi'], $u['mail'], password_hash($u['pass'], PASSWORD_DEFAULT), $u['role']
                ]);
                $id = (int)$pdo->lastInsertId();
            }
            $userIds[$u['mail']] = (int)$id;
        }

        // Seed projects
        $projects = [
            ['ad' => 'Kurumsal Website', 'aciklama' => 'Şirket için vitrin site.',   'musteri_mail' => 'musteri1@example.com', 'durum' => 'yeni'],
            ['ad' => 'Mobil Uygulama',    'aciklama' => 'iOS/Android hibrit uygulama','musteri_mail' => 'musteri2@example.com', 'durum' => 'devam'],
            ['ad' => 'E-Ticaret',         'aciklama' => 'Ödeme ve ürün yönetimi.',  'musteri_mail' => 'musteri1@example.com', 'durum' => 'yeni'],
        ];

        $projectIds = [];
        foreach ($projects as $p) {
            $existingId = self::getProjectIdByName($pdo, $p['ad']);
            if ($existingId) {
                $projectIds[$p['ad']] = (int)$existingId;
                continue;
            }
            $musteriId = $userIds[$p['musteri_mail']] ?? null;
            $stmt = $pdo->prepare('INSERT INTO projects (ad, aciklama, musteri_id, durum, olusturma_tarihi, guncelleme_tarihi) VALUES (?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([$p['ad'], $p['aciklama'], $musteriId, $p['durum']]);
            $projectIds[$p['ad']] = (int)$pdo->lastInsertId();
        }

        // Seed assignments (assign employees to projects)
        $assignments = [
            ['project' => 'Kurumsal Website', 'calisan_mail' => 'calisan1@example.com', 'rol' => 'frontend'],
            ['project' => 'Kurumsal Website', 'calisan_mail' => 'calisan2@example.com', 'rol' => 'backend'],
            ['project' => 'Mobil Uygulama',   'calisan_mail' => 'calisan1@example.com', 'rol' => 'mobile'],
            ['project' => 'E-Ticaret',        'calisan_mail' => 'calisan2@example.com', 'rol' => 'fullstack'],
        ];

        foreach ($assignments as $a) {
            $pid = $projectIds[$a['project']] ?? null;
            $uid = $userIds[$a['calisan_mail']] ?? null;
            if (!$pid || !$uid) continue;
            // upsert-like behavior
            $check = $pdo->prepare('SELECT 1 FROM project_assignments WHERE project_id=? AND calisan_id=?');
            $check->execute([$pid, $uid]);
            if (!$check->fetchColumn()) {
                $ins = $pdo->prepare('INSERT INTO project_assignments (project_id, calisan_id, rol_projede, atama_tarihi) VALUES (?, ?, ?, NOW())');
                $ins->execute([$pid, $uid, $a['rol']]);
            }
        }

        // Seed milestones (2 future per project if none exist)
        try {
            foreach ($projectIds as $name => $pid) {
                $cnt = 0;
                try {
                    $st = $pdo->prepare('SELECT COUNT(*) FROM project_milestones WHERE project_id=?');
                    $st->execute([$pid]);
                    $cnt = (int)$st->fetchColumn();
                } catch (PDOException $e) {
                    $cnt = 0;
                }
                if ($cnt === 0) {
                    $ins = $pdo->prepare('INSERT INTO project_milestones (project_id, baslik, due_date, olusturma_tarihi, guncelleme_tarihi) VALUES (?, ?, ?, NOW(), NOW())');
                    $ins->execute([$pid, 'Ön Taslak', date('Y-m-d H:i:s', strtotime('+10 days'))]);
                    $ins->execute([$pid, 'Teslim Hazırlığı', date('Y-m-d H:i:s', strtotime('+20 days'))]);
                }
            }
        } catch (PDOException $e) {
            // ignore in dev seed
        }
    }

    private static function getUserIdByMail(PDO $pdo, string $mail): ?int
    {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE mail=? LIMIT 1');
        $stmt->execute([$mail]);
        $id = $stmt->fetchColumn();
        return $id ? (int)$id : null;
    }

    private static function getProjectIdByName(PDO $pdo, string $name): ?int
    {
        $stmt = $pdo->prepare('SELECT id FROM projects WHERE ad=? LIMIT 1');
        $stmt->execute([$name]);
        $id = $stmt->fetchColumn();
        return $id ? (int)$id : null;
    }
}
