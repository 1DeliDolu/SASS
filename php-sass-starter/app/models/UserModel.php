<?php
// MySQL bağlantı ve kullanıcı modeli
class UserModel
{
    private $pdo;

    public function __construct()
    {
        $host = 'localhost';
        $db = 'sass';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int) $e->getCode());
        }
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

    public function updateUser($id, $adi, $soyadi, $mail, $sifre)
    {
        $sql = "UPDATE users SET adi=?, soyadi=?, mail=?, sifre=?, guncelleme_tarihi=NOW() WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$adi, $soyadi, $mail, password_hash($sifre, PASSWORD_DEFAULT), $id]);
    }

    // Şifre değişmeden güncelleme için esnek sürüm.
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
            // son_giris_tarihi kolonu yoksa sessizce geç.
            return false;
        }
    }
}
