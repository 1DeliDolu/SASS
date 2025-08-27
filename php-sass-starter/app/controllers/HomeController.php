<?php
// Basit bir ana controller
class HomeController
{
    public function index()
    {
        $data = ['title' => 'SASS MVC Başlangıç', 'message' => 'Hoşgeldiniz!'];
        include __DIR__ . '/../views/home.php';
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/UserModel.php';
            $userModel = new UserModel();
            $adi = $_POST['adi'] ?? '';
            $soyadi = $_POST['soyadi'] ?? '';
            $mail = $_POST['mail'] ?? '';
            $sifre = $_POST['sifre'] ?? '';
            $success = $userModel->createUser($adi, $soyadi, $mail, $sifre);
            $data = ['success' => $success];
            include __DIR__ . '/../views/register.php';
        } else {
            include __DIR__ . '/../views/register.php';
        }
    }

    public function login()
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/UserModel.php';
            $userModel = new UserModel();
            $mail = $_POST['mail'] ?? '';
            $sifre = $_POST['sifre'] ?? '';
            $user = $this->findUserByMail($userModel, $mail);
            if ($user && password_verify($sifre, $user['sifre'])) {
                // Giriş başarılı
                session_start();
                $_SESSION['user'] = $user;
                header('Location: /index.php');
                exit;
            } else {
                $error = 'E-posta veya şifre hatalı!';
            }
        }
        include __DIR__ . '/../views/login.php';
    }

    private function findUserByMail($userModel, $mail)
    {
        $users = $userModel->getAllUsers();
        foreach ($users as $user) {
            if ($user['mail'] === $mail) {
                return $user;
            }
        }
        return null;
    }
}
