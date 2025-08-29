<?php
class ProjectModel
{
    private $pdo;
    public function __construct()
    {
        require_once __DIR__ . '/../lib/Database.php';
        $this->pdo = Database::pdo();
    }

    public function getAll()
    {
        $sql = "SELECT p.*, u.mail AS musteri_mail FROM projects p LEFT JOIN users u ON u.id=p.musteri_id ORDER BY p.id DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function getByCustomer($userId)
    {
        $sql = "SELECT * FROM projects WHERE musteri_id=? ORDER BY id DESC";
        $st = $this->pdo->prepare($sql);
        $st->execute([$userId]);
        return $st->fetchAll();
    }

    public function getByAssignee($userId)
    {
        $sql = "SELECT p.* FROM projects p INNER JOIN project_assignments a ON a.project_id=p.id WHERE a.calisan_id=? ORDER BY p.id DESC";
        $st = $this->pdo->prepare($sql);
        $st->execute([$userId]);
        return $st->fetchAll();
    }

    public function get($id)
    {
        $st = $this->pdo->prepare("SELECT p.*, u.mail AS musteri_mail FROM projects p LEFT JOIN users u ON u.id=p.musteri_id WHERE p.id=?");
        $st->execute([$id]);
        return $st->fetch();
    }

    public function getAssignments($projectId)
    {
        $st = $this->pdo->prepare("SELECT a.*, u.mail, u.adi, u.soyadi FROM project_assignments a INNER JOIN users u ON u.id=a.calisan_id WHERE a.project_id=?");
        $st->execute([$projectId]);
        return $st->fetchAll();
    }

    public function create($ad, $aciklama, $musteri_id, $durum)
    {
        $st = $this->pdo->prepare("INSERT INTO projects (ad, aciklama, musteri_id, durum, olusturma_tarihi, guncelleme_tarihi) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $ok = $st->execute([$ad, $aciklama, $musteri_id ?: null, $durum]);
        if ($ok)
            return (int) $this->pdo->lastInsertId();
        return 0;
    }

    public function updateProject($id, $ad, $aciklama, $musteri_id, $durum)
    {
        $st = $this->pdo->prepare("UPDATE projects SET ad=?, aciklama=?, musteri_id=?, durum=?, guncelleme_tarihi=NOW() WHERE id=?");
        return $st->execute([$ad, $aciklama, $musteri_id ?: null, $durum, $id]);
    }

    public function delete($id)
    {
        // Zuerst Zuweisungen löschen
        $this->pdo->prepare("DELETE FROM project_assignments WHERE project_id=?")->execute([$id]);
        $st = $this->pdo->prepare("DELETE FROM projects WHERE id=?");
        return $st->execute([$id]);
    }

    public function addAssignment($project_id, $calisan_id, $rol_projede = null)
    {
        // Duplikate verhindern
        $st = $this->pdo->prepare("SELECT 1 FROM project_assignments WHERE project_id=? AND calisan_id=?");
        $st->execute([$project_id, $calisan_id]);
        if ($st->fetch())
            return true;
        $st = $this->pdo->prepare("INSERT INTO project_assignments (project_id, calisan_id, rol_projede, atama_tarihi) VALUES (?, ?, ?, NOW())");
        return $st->execute([$project_id, $calisan_id, $rol_projede]);
    }

    public function removeAssignment($project_id, $calisan_id)
    {
        $st = $this->pdo->prepare("DELETE FROM project_assignments WHERE project_id=? AND calisan_id=?");
        return $st->execute([$project_id, $calisan_id]);
    }

    // Milestones
    public function getMilestones($project_id)
    {
        $st = $this->pdo->prepare("SELECT * FROM project_milestones WHERE project_id=? ORDER BY due_date ASC, id ASC");
        $st->execute([$project_id]);
        return $st->fetchAll();
    }

    public function getNextMilestone($project_id)
    {
        $st = $this->pdo->prepare("SELECT * FROM project_milestones WHERE project_id=? AND completed_at IS NULL ORDER BY due_date ASC, id ASC LIMIT 1");
        $st->execute([$project_id]);
        return $st->fetch();
    }

    public function addMilestone($project_id, $baslik, $due_date)
    {
        $st = $this->pdo->prepare("INSERT INTO project_milestones (project_id, baslik, due_date, olusturma_tarihi, guncelleme_tarihi) VALUES (?, ?, ?, NOW(), NOW())");
        return $st->execute([$project_id, $baslik, $due_date]);
    }

    public function markMilestoneDone($id)
    {
        $st = $this->pdo->prepare("UPDATE project_milestones SET completed_at=NOW(), guncelleme_tarihi=NOW() WHERE id=?");
        return $st->execute([$id]);
    }
}
