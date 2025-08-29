<?php
// MySQL Verbindung und Benutzermodell
class UserModel
{
    private $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../lib/Database.php';
        $this->pdo = Database::pdo();
    }

    public function getUserByMail($mail)
    {
        $sql = "SELECT * FROM users WHERE mail=? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$mail]);
        return $stmt->fetch();
    }

    public function createUser($adi, $soyadi, $mail, $sifre)
    {
        $sql = "INSERT INTO users (adi, soyadi, mail, sifre, olusturma_tarihi, guncelleme_tarihi) VALUES (?, ?, ?, ?, NOW(), NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$adi, $soyadi, $mail, password_hash($sifre, PASSWORD_DEFAULT)]);
    }

    public function createUserWithRole($adi, $soyadi, $mail, $sifre, $role = 'calisan')
    {
        $allowed = ['admin','calisan','musteri'];
        if (!in_array($role, $allowed, true)) {
            $role = 'calisan';
        }
        $sql = "INSERT INTO users (adi, soyadi, mail, sifre, role, olusturma_tarihi, guncelleme_tarihi) VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$adi, $soyadi, $mail, password_hash($sifre, PASSWORD_DEFAULT), $role]);
    }

    public function updateUser($id, $adi, $soyadi, $mail, $sifre)
    {
        $sql = "UPDATE users SET adi=?, soyadi=?, mail=?, sifre=?, guncelleme_tarihi=NOW() WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$adi, $soyadi, $mail, password_hash($sifre, PASSWORD_DEFAULT), $id]);
    }

    // Flexible Version für Update ohne Passwortänderung.
    public function updateUserFields($id, $adi, $soyadi, $mail, $sifre = null)
    {
        if ($sifre === null || $sifre === '') {
            $sql = "UPDATE users SET adi=?, soyadi=?, mail=?, guncelleme_tarihi=NOW() WHERE id=?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$adi, $soyadi, $mail, $id]);
        } else {
            $sql = "UPDATE users SET adi=?, soyadi=?, mail=?, sifre=?, guncelleme_tarihi=NOW() WHERE id=?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$adi, $soyadi, $mail, password_hash($sifre, PASSWORD_DEFAULT), $id]);
        }
    }

    public function getUser($id)
    {
        $sql = "SELECT * FROM users WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAllUsers()
    {
        $sql = "SELECT * FROM users";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function updateLastLogin($id)
    {
        try {
            $sql = "UPDATE users SET son_giris_tarihi=NOW() WHERE id=?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            // Wenn die Spalte son_giris_tarihi nicht existiert, stillschweigend überspringen.
            return false;
        }
    }

    public function setUserRole($id, $role)
    {
        $allowed = ['admin', 'calisan', 'musteri'];
        if (!in_array($role, $allowed, true))
            return false;
        // Versuche zuerst die Spalte type zu aktualisieren, falls nicht, dann die Spalte role.
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET type=?, guncelleme_tarihi=NOW() WHERE id=?");
            if ($stmt->execute([$role, $id]))
                return true;
        } catch (PDOException $e) {
            // Ignorieren und role aktualisieren
        }
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET role=?, guncelleme_tarihi=NOW() WHERE id=?");
            return $stmt->execute([$role, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getUsersByRole($role)
    {
        // Versuche zuerst mit der Spalte role; falls nicht vorhanden, dann mit type.
        try {
            $sql = "SELECT * FROM users WHERE role=? ORDER BY id DESC";
            $st = $this->pdo->prepare($sql);
            $st->execute([$role]);
            return $st->fetchAll();
        } catch (PDOException $e) {
            try {
                $sql = "SELECT * FROM users WHERE type=? ORDER BY id DESC";
                $st = $this->pdo->prepare($sql);
                $st->execute([$role]);
                return $st->fetchAll();
            } catch (PDOException $e2) {
                return [];
            }
        }
    }
}
