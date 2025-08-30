<?php
// Basit bir ana controller
class HomeController
{
    public function index()
    {
        $data = ['title' => 'SASS MVC Başlangıç', 'message' => 'Willkommen!'];
        $data['docsDirs'] = $this->listReadmeDirs();
        include __DIR__ . '/../views/home.php';
    }

    public function register()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        // next param gelirse kaydet
        if (isset($_GET['next']) && !empty($_GET['next']) && strpos($_GET['next'], '/') === 0) {
            $_SESSION['redirect_after_login'] = $_GET['next'];
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/UserModel.php';
            $userModel = new UserModel();
            $adi = $_POST['adi'] ?? '';
            $soyadi = $_POST['soyadi'] ?? '';
            $mail = $_POST['mail'] ?? '';
            $sifre = $_POST['sifre'] ?? '';
            // Şifre politikası kontrolü
            $pwdError = $this->validatePassword($sifre);
            if ($pwdError) {
                $data = ['error' => $pwdError];
                include __DIR__ . '/../views/register.php';
                return;
            }
            // E‑posta var mı kontrol et
            if ($this->emailExists($userModel, $mail)) {
                $data = ['error' => 'Bu e‑posta ile kayıtlı bir kullanıcı zaten var.'];
                include __DIR__ . '/../views/register.php';
                return;
            }
            $success = $userModel->createUser($adi, $soyadi, $mail, $sifre);
            if ($success) {
                // Otomatik giriş ve yönlendirme
                $user = method_exists($userModel, 'getUserByMail') ? $userModel->getUserByMail($mail) : null;
                if ($user) {
                    if (method_exists($userModel, 'updateLastLogin')) {
                        $userModel->updateLastLogin($user['id']);
                        $user = $userModel->getUser($user['id']);
                    }
                    $_SESSION['user'] = $user;
                }
                $dest = $_SESSION['redirect_after_login'] ?? null;
                if ($dest) {
                    unset($_SESSION['redirect_after_login']);
                    if (strpos($dest, '/') === 0) {
                        header('Location: ' . $dest);
                        exit;
                    }
                }
                header('Location: /index.php?action=profile');
                exit;
            } else {
                $data = [
                    'success' => false,
                    'error' => 'Kayıt başarısız oldu.'
                ];
                include __DIR__ . '/../views/register.php';
            }
        } else {
            include __DIR__ . '/../views/register.php';
        }
    }

    public function login()
    {
        $error = '';
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        // next param üzerinden hedefi kaydet (isteğe bağlı)
        if (isset($_GET['next']) && !empty($_GET['next']) && strpos($_GET['next'], '/') === 0) {
            $_SESSION['redirect_after_login'] = $_GET['next'];
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/UserModel.php';
            $userModel = new UserModel();
            $mail = $_POST['mail'] ?? '';
            $sifre = $_POST['sifre'] ?? '';
            // Mail ile kullanıcı bul
            if (method_exists($userModel, 'getUserByMail')) {
                $user = $userModel->getUserByMail($mail);
            } else {
                $user = $this->findUserByMail($userModel, $mail);
            }
            if ($user && password_verify($sifre, $user['sifre'])) {
                // Giriş başarılı
                if (session_status() !== PHP_SESSION_ACTIVE) {
                    session_start();
                }
                // Son giriş tarihini güncelle
                if (method_exists($userModel, 'updateLastLogin')) {
                    $userModel->updateLastLogin($user['id']);
                    // Güncel veriyi tekrar çek (son_giris_tarihi dahil)
                    $user = $userModel->getUser($user['id']);
                }
                $_SESSION['user'] = $user;
                $dest = $_SESSION['redirect_after_login'] ?? null;
                if ($dest) {
                    unset($_SESSION['redirect_after_login']);
                    if (strpos($dest, '/') === 0) {
                        header('Location: ' . $dest);
                        exit;
                    }
                }
                header('Location: /index.php?action=profile');
                exit;
            } else {
                $error = 'E-posta veya şifre hatalı!';
            }
        }
        include __DIR__ . '/../views/login.php';
    }

    public function forgotPassword()
    {
        $data = ['title' => 'Passwort zurücksetzen'];
        $status = null;
        $error = null;
        // Prepare CSRF token
        require_once __DIR__ . '/../lib/Csrf.php';
        $data['csrf'] = Csrf::token('forgot');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tok = $_POST['csrf'] ?? '';
            if (!Csrf::validate('forgot', $tok)) {
                $error = 'Geçersiz oturum anahtarı. Lütfen tekrar deneyin.';
                $data['error'] = $error;
                include __DIR__ . '/../views/forgot-password.php';
                return;
            }
            // Optional: reCAPTCHA
            require_once __DIR__ . '/../lib/Captcha.php';
            if (!Captcha::verify($_POST['g-recaptcha-response'] ?? null, $_SERVER['REMOTE_ADDR'] ?? null)) {
                $error = 'Doğrulama başarısız. Lütfen tekrar deneyin.';
                $data['error'] = $error;
                include __DIR__ . '/../views/forgot-password.php';
                return;
            }
            // Rate limit
            require_once __DIR__ . '/../models/ThrottleModel.php';
            $thr = new ThrottleModel();
            $mail = isset($_POST['mail']) ? trim((string)$_POST['mail']) : '';
            if ($mail === '' || !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $error = 'Lütfen geçerli bir e‑posta adresi girin.';
            } else {
                $ip = $_SERVER['REMOTE_ADDR'] ?? '';
                if ($thr->tooMany('forgot_password:ip', $ip ?: 'none', 10, 3600) || $thr->tooMany('forgot_password:mail', strtolower($mail), 5, 3600)) {
                    // still log hit but return generic
                    $thr->hit('forgot_password:blocked', strtolower($mail), $ip);
                    $status = 'Eğer bu e‑posta sistemimizde kayıtlıysa, sıfırlama bağlantısı gönderildi.';
                    $data['status'] = $status;
                    include __DIR__ . '/../views/forgot-password.php';
                    return;
                }
                require_once __DIR__ . '/../models/UserModel.php';
                $um = new UserModel();
                $user = $um->getUserByMail($mail);
                if ($user) {
                    // Token üret ve e‑posta gönder
                    require_once __DIR__ . '/../models/PasswordResetModel.php';
                    $pr = new PasswordResetModel();
                    $rec = $pr->create((int)$user['id'], $mail, 3600);
                    $token = $rec['token'];
                    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                    $link = $scheme . '://' . $host . '/index.php?action=reset_password&token=' . urlencode($token);
                    $subject = 'Parola sıfırlama isteği';
                    $html = '<div style="font-family:Inter,Arial,sans-serif;line-height:1.6;color:#111;">'
                        . '<h2 style="margin:0 0 12px 0;">Parolanızı sıfırlayın</h2>'
                        . '<p>Bu isteği siz yaptıysanız, aşağıdaki butona tıklayın. Bağlantı 1 saat içinde geçerlidir.</p>'
                        . '<p style="margin:18px 0;"><a href="' . htmlspecialchars($link) . '" style="background:#e67e22;color:#fff;padding:10px 16px;border-radius:8px;text-decoration:none;font-weight:700;">Parolayı Sıfırla</a></p>'
                        . '<p>Buton çalışmazsa, bu bağlantıyı kopyalayın:</p>'
                        . '<p style="word-break:break-all"><a href="' . htmlspecialchars($link) . '">' . htmlspecialchars($link) . '</a></p>'
                        . '</div>';
                    $text = "Parolanızı sıfırlamak için bu bağlantıyı açın (1 saat geçerli):\n" . $link;
                    require_once __DIR__ . '/../lib/Mailer.php';
                    Mailer::send($mail, $subject, $html, $text);
                }
                // log hit for throttle
                $thr->hit('forgot_password:mail', strtolower($mail), $ip ?? '');
                if (!empty($ip)) $thr->hit('forgot_password:ip', $ip, $ip);
                // Her durumda aynı mesaj (bilgi sızıntısını engellemek için)
                $status = 'Eğer bu e‑posta sistemimizde kayıtlıysa, sıfırlama bağlantısı gönderildi.';
            }
        }
        $data['status'] = $status;
        $data['error'] = $error;
        // reCAPTCHA site-key for view
        require_once __DIR__ . '/../lib/Captcha.php';
        $data['recaptcha_site_key'] = Captcha::siteKey();
        include __DIR__ . '/../views/forgot-password.php';
    }

    public function resetPassword()
    {
        $data = ['title' => 'Yeni parola belirle'];
        $token = isset($_REQUEST['token']) ? trim((string)$_REQUEST['token']) : '';
        $error = null;
        $status = null;
        $valid = false;
        $resetRow = null;
        if ($token !== '') {
            require_once __DIR__ . '/../models/PasswordResetModel.php';
            $pr = new PasswordResetModel();
            $resetRow = $pr->findValidByToken($token);
            $valid = $resetRow !== null;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF check
            require_once __DIR__ . '/../lib/Csrf.php';
            $tok = $_POST['csrf'] ?? '';
            if (!Csrf::validate('reset', $tok)) {
                $error = 'Geçersiz oturum anahtarı. Lütfen tekrar deneyin.';
            } else {
            $pwd1 = $_POST['sifre1'] ?? '';
            $pwd2 = $_POST['sifre2'] ?? '';
            if (!$valid) {
                $error = 'Geçersiz veya süresi dolmuş bağlantı.';
            } elseif ($pwd1 !== $pwd2) {
                $error = 'Şifreler eşleşmiyor.';
            } else {
                $pwdError = $this->validatePassword($pwd1);
                if ($pwdError) {
                    $error = $pwdError;
                } else {
                    // Update password and mark token used
                    require_once __DIR__ . '/../models/UserModel.php';
                    $um = new UserModel();
                    $userId = (int)($resetRow['user_id'] ?? 0);
                    $user = $um->getUser($userId);
                    if (!$user) {
                        $error = 'Kullanıcı bulunamadı.';
                    } else {
                        $um->updateUserFields($userId, $user['adi'], $user['soyadi'], $user['mail'], $pwd1);
                        $pr->markUsed((int)$resetRow['id']);
                        // güvenlik: diğer aktif tokenleri de iptal et
                        $pr->invalidateAllForUser($userId);
                        $status = 'Parolanız güncellendi. Giriş yapabilirsiniz.';
                        // Token artık geçersiz sayılmalı
                        $valid = false;
                    }
                }
            }
            }
        }
        // Prepare CSRF token for view form
        require_once __DIR__ . '/../lib/Csrf.php';
        $data['csrf'] = Csrf::token('reset');
        $data['token'] = $token;
        $data['valid'] = $valid;
        $data['status'] = $status;
        $data['error'] = $error;
        include __DIR__ . '/../views/reset-password.php';
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

    private function emailExists($userModel, $mail)
    {
        if (!$mail) {
            return false;
        }
        if (method_exists($userModel, 'getUserByMail')) {
            $u = $userModel->getUserByMail($mail);
            return !empty($u);
        }
        // Geriye dönük: tüm kullanıcıları gez
        $users = $userModel->getAllUsers();
        foreach ($users as $user) {
            if (isset($user['mail']) && $user['mail'] === $mail) {
                return true;
            }
        }
        return false;
    }

    public function profile()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user'])) {
            header('Location: /index.php?action=login');
            exit;
        }
        $user = $_SESSION['user'];
        $data = [ 'title' => 'Profilim', 'user' => $user ];
        include __DIR__ . '/../views/profile.php';
    }

    public function messages()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user'])) {
            header('Location: /index.php?action=login');
            exit;
        }
        $me = $_SESSION['user'];
        require_once __DIR__ . '/../models/UserModel.php';
        require_once __DIR__ . '/../models/MessageModel.php';
        $userModel = new UserModel();
        $msgModel = new MessageModel();
        $showArchived = isset($_GET['archived']) && $_GET['archived'] === '1';
        if ($showArchived) {
            $allUsers = $msgModel->getArchivedPartners((int)$me['id']);
        } else {
            $allUsers = array_values(array_filter($userModel->getAllUsers(), function ($u) use ($me) {
                return isset($u['id']) && $u['id'] !== $me['id'];
            }));
            // Filter out archived partners from the list
            $allUsers = array_values(array_filter($allUsers, function ($u) use ($msgModel, $me) {
                return !$msgModel->isArchived((int)$me['id'], (int)$u['id']);
            }));
        }
        $withId = isset($_GET['with']) ? (int) $_GET['with'] : 0;
        $withUser = null;
        $conversation = [];
        if ($withId > 0) {
            foreach ($allUsers as $u) {
                if ((int)$u['id'] === $withId) {
                    $withUser = $u;
                    break;
                }
            }
            if ($withUser) {
                // Mark incoming as read first, then fetch conversation
                $msgModel->markAsRead((int)$me['id'], (int)$withUser['id']);
                $conversation = $msgModel->getConversation((int)$me['id'], $withId, 200);
            }
        }
        $data = [
            'title' => 'Mesajlar',
            'user' => $me,
            'users' => $allUsers,
            'withUser' => $withUser,
            'conversation' => $conversation,
            'archived' => $showArchived,
            'isArchived' => $withId && $withUser ? $msgModel->isArchived((int)$me['id'], (int)$withUser['id']) : false,
        ];
        include __DIR__ . '/../views/messages.php';
    }

    public function messagesPartial()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo 'Unauthorized';
            return;
        }
        $me = $_SESSION['user'];
        $withId = isset($_GET['with']) ? (int) $_GET['with'] : 0;
        if ($withId <= 0) {
            http_response_code(400);
            echo 'Bad request';
            return;
        }
        require_once __DIR__ . '/../models/MessageModel.php';
        $msgModel = new MessageModel();
        $msgModel->markAsRead((int)$me['id'], $withId);
        $conversation = $msgModel->getConversation((int)$me['id'], $withId, 200);
        $data = [
            'user' => $me,
            'conversation' => $conversation,
        ];
        header('Content-Type: text/html; charset=UTF-8');
        include __DIR__ . '/../views/partials/conversation.php';
    }

    public function messagesArchive()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user'])) {
            header('Location: /index.php?action=login');
            exit;
        }
        $me = $_SESSION['user'];
        $withId = isset($_POST['with_id']) ? (int)$_POST['with_id'] : 0;
        require_once __DIR__ . '/../models/MessageModel.php';
        $msgModel = new MessageModel();
        if ($withId > 0) {
            $msgModel->archiveConversation((int)$me['id'], $withId);
        }
        header('Location: /index.php?action=messages');
        exit;
    }

    public function messagesUnarchive()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user'])) {
            header('Location: /index.php?action=login');
            exit;
        }
        $me = $_SESSION['user'];
        $withId = isset($_POST['with_id']) ? (int)$_POST['with_id'] : 0;
        require_once __DIR__ . '/../models/MessageModel.php';
        $msgModel = new MessageModel();
        if ($withId > 0) {
            $msgModel->unarchiveConversation((int)$me['id'], $withId);
        }
        $redir = '/index.php?action=messages';
        if ($withId > 0) { $redir .= '&with=' . $withId; }
        header('Location: ' . $redir);
        exit;
    }

    public function sendMessage()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user'])) {
            header('Location: /index.php?action=login');
            exit;
        }
        $me = $_SESSION['user'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $toId = isset($_POST['to_id']) ? (int) $_POST['to_id'] : 0;
            $content = isset($_POST['message']) ? trim((string) $_POST['message']) : '';
            require_once __DIR__ . '/../models/MessageModel.php';
            $msgModel = new MessageModel();
            if ($toId > 0 && $content !== '') {
                // Mesajı iç mesaja kaydet ve arşivliyse aç
                $msgModel->unarchiveConversation((int)$me['id'], $toId);
                $msgModel->sendMessage((int)$me['id'], $toId, $content);
                // E‑posta olarak da gönder (FEHLER.md doğrultusunda SMTP)
                require_once __DIR__ . '/../lib/Mailer.php';
                $toEmail = '';
                // resolve mail of receiver
                require_once __DIR__ . '/../models/UserModel.php';
                $um = new UserModel();
                $u = $um->getUser($toId);
                if ($u && !empty($u['mail'])) {
                    $toEmail = $u['mail'];
                }
                if ($toEmail !== '') {
                    $subject = 'Yeni mesaj - ' . (($me['adi'] ?? '') . ' ' . ($me['soyadi'] ?? ''));
                    $html = '<p>' . nl2br(htmlspecialchars($content)) . '</p>';
                    $text = $content;
                    Mailer::send($toEmail, $subject, $html, $text);
                }
            }
            $redir = '/index.php?action=messages';
            if ($toId > 0) {
                $redir .= '&with=' . $toId;
            }
            header('Location: ' . $redir);
            exit;
        }
        header('Location: /index.php?action=messages');
        exit;
    }

    public function update()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user'])) {
            header('Location: /index.php?action=login');
            exit;
        }
        require_once __DIR__ . '/../models/UserModel.php';
        $userModel = new UserModel();

        $user = $_SESSION['user'];
        $message = '';
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adi = $_POST['adi'] ?? '';
            $soyadi = $_POST['soyadi'] ?? '';
            $mail = $_POST['mail'] ?? '';
            $sifre = $_POST['sifre'] ?? '';
            // Şifre güncellenecekse politikayı doğrula
            if ($sifre !== '') {
                $pwdError = $this->validatePassword($sifre);
                if ($pwdError) {
                    $error = $pwdError;
                    $data = ['title' => 'Bilgileri Güncelle', 'user' => $user, 'message' => $message, 'error' => $error];
                    include __DIR__ . '/../views/update.php';
                    return;
                }
            }
            $ok = false;
            if (method_exists($userModel, 'updateUserFields')) {
                $ok = $userModel->updateUserFields($user['id'], $adi, $soyadi, $mail, $sifre === '' ? null : $sifre);
            } else {
                // Geriye dönük uyumluluk: boş şifre gelirse mevcut şifreyi korumak için doğrudan updateUser çağırmayalım.
                if ($sifre === '') {
                    $ok = $userModel->updateUser($user['id'], $adi, $soyadi, $mail, '');
                } else {
                    $ok = $userModel->updateUser($user['id'], $adi, $soyadi, $mail, $sifre);
                }
            }
            if ($ok) {
                $user = $userModel->getUser($user['id']);
                $_SESSION['user'] = $user;
                $_SESSION['flash'] = 'Bilgileriniz güncellendi.';
                header('Location: /index.php?action=profile');
                exit;
            } else {
                $error = 'Güncelleme başarısız oldu.';
            }
        }
        $data = ['title' => 'Bilgileri Güncelle', 'user' => $user, 'message' => $message, 'error' => $error];
        include __DIR__ . '/../views/update.php';
    }

    private function validatePassword($sifre)
    {
        if (!is_string($sifre)) {
            return 'Geçersiz şifre.';
        }
        if (strlen($sifre) < 12) {
            return 'Şifre en az 12 karakter olmalı.';
        }
        if (!preg_match('/[A-Z]/', $sifre)) {
            return 'Şifre en az bir büyük harf içermeli.';
        }
        if (!preg_match('/[a-z]/', $sifre)) {
            return 'Şifre en az bir küçük harf içermeli.';
        }
        if (!preg_match('/[0-9]/', $sifre)) {
            return 'Şifre en az bir rakam içermeli.';
        }
        // Özel karakter: harf ve rakam dışı
        if (!preg_match('/[^a-zA-Z0-9]/', $sifre)) {
            return 'Şifre en az bir özel karakter içermeli.';
        }
        return null;
    }

    public function logout()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        header('Location: /index.php');
        exit;
    }

    public function search()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user'])) {
            if (isset($_SERVER['REQUEST_URI'])) {
                $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            }
            $data = ['title' => 'Erişim Gerekli', 'reason' => 'Arama sonuçlarını görmek için giriş yapın.'];
            include __DIR__ . '/../views/auth-required.php';
            return;
        }
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $results = [];
        if ($q !== '') {
            $results = $this->searchDocs($q);
        }
        $data = [
            'title' => 'Arama',
            'q' => $q,
            'results' => $results,
        ];
        include __DIR__ . '/../views/search.php';
    }

    public function docs()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user'])) {
            if (isset($_SERVER['REQUEST_URI'])) {
                $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            }
            $data = ['title' => 'Erişim Gerekli', 'reason' => 'Projelerin içeriğini görmek için lütfen giriş yapın veya kayıt olun.'];
            include __DIR__ . '/../views/auth-required.php';
            return;
        }
        $base = realpath(__DIR__ . '/../../README');
        if (!$base) {
            http_response_code(404);
            echo 'README klasörü bulunamadı.';
            return;
        }
        $path = isset($_GET['path']) ? trim($_GET['path']) : '';
        // URL'den gelen yolu FS için normalize et
        $norm = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $target = $path ? realpath($base . DIRECTORY_SEPARATOR . $norm) : $base;
        if ($target === false || strpos($target, $base) !== 0) {
            http_response_code(404);
            echo 'Geçersiz yol.';
            return;
        }

        if (is_dir($target)) {
            $listing = $this->listDir($target, $base);
            $breadcrumbs = $this->buildBreadcrumbs(str_replace('\\', '/', $path));
            $data = [
                'title' => 'Dokümanlar',
                'path' => str_replace('\\', '/', $path),
                'listing' => $listing,
                'breadcrumbs' => $breadcrumbs,
                'docsDirs' => $this->listReadmeDirs(),
            ];
            include __DIR__ . '/../views/docs.php';
            return;
        }

        // Dosya ise Markdown'ı işle
        $content = @file_get_contents($target);
        if ($content === false) {
            http_response_code(404);
            echo 'Dosya okunamadı.';
            return;
        }
        $toc = [];
        $html = $this->mdToHtml($content, $toc);
        $breadcrumbs = $this->buildBreadcrumbs(str_replace('\\', '/', $path));
        $data = [
            'title' => basename($target),
            'path' => str_replace('\\', '/', $path),
            'html' => $html,
            'toc' => $toc,
            'breadcrumbs' => $breadcrumbs,
            'docsDirs' => $this->listReadmeDirs(),
        ];
        include __DIR__ . '/../views/docs.php';
    }

    private function listReadmeDirs()
    {
        $base = realpath(__DIR__ . '/../../README');
        if (!$base)
            return [];
        $dirs = array_filter(glob($base . DIRECTORY_SEPARATOR . '*'), 'is_dir');
        // sırala
        natcasesort($dirs);
        $out = [];
        foreach ($dirs as $dir) {
            // Başlık üret
            $name = basename($dir);
            $display = preg_replace('/^\d+[_\-]?/', '', $name);
            $display = str_replace(['_', '-'], ' ', $display);
            $display = trim($display);
            $out[] = [
                'name' => $display,
                'path' => basename($dir),
            ];
        }
        return $out;
    }

    private function listDir($dir, $base)
    {
        $items = scandir($dir);
        $files = [];
        foreach ($items as $it) {
            if ($it === '.' || $it === '..')
                continue;
            $full = $dir . DIRECTORY_SEPARATOR . $it;
            if (is_dir($full)) {
                $rel = ltrim(str_replace($base, '', $full), DIRECTORY_SEPARATOR);
                $rel = str_replace('\\', '/', $rel);
                $files[] = [
                    'type' => 'dir',
                    'name' => $this->prettyName($it),
                    'path' => $rel,
                ];
            } else if (strtolower(pathinfo($full, PATHINFO_EXTENSION)) === 'md') {
                $rel = ltrim(str_replace($base, '', $full), DIRECTORY_SEPARATOR);
                $rel = str_replace('\\', '/', $rel);
                $files[] = [
                    'type' => 'file',
                    'name' => $this->prettyName($it),
                    'path' => $rel,
                ];
            }
        }
        // natural sort by path
        usort($files, function ($a, $b) {
            return strnatcasecmp($a['path'], $b['path']); });
        // URL'ler için ileri eğik çizgi kullan
        foreach ($files as &$f) {
            $f['path'] = ltrim(str_replace('\\', '/', $f['path']), '/');
        }
        return $files;
    }

    private function prettyName($name)
    {
        $n = preg_replace('/^\d+[_\-]?/', '', $name);
        $n = preg_replace('/\.md$/i', '', $n);
        $n = str_replace(['_', '-'], ' ', $n);
        return trim($n);
    }

    private function mdToHtml($md, &$toc = null)
    {
        // Basit ve güvenli dönüştürücü: başlıklar, listeler, linkler, görseller, tablolar, kod blokları
        $escaped = htmlspecialchars($md, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        // Kod bloklarını korumak için placeholder'lara al
        $codeBlocks = [];
        $escaped = preg_replace_callback('/```([\s\S]*?)```/m', function ($m) use (&$codeBlocks) {
            $idx = count($codeBlocks);
            $codeBlocks[$idx] = '<pre><code>' . $m[1] . '</code></pre>';
            return "@@CODEBLOCK{$idx}@@";
        }, $escaped);

        // Başlıklar #..######
        for ($i = 6; $i >= 1; $i--) {
            $pattern = '/^' . str_repeat('#', $i) . ' (.+)$/m';
            $replace = '<h' . $i . '>$1</h' . $i . '>';
            $escaped = preg_replace($pattern, $replace, $escaped);
        }

        // Görseller ![alt](src)
        $escaped = preg_replace('/!\[([^\]]*)\]\(([^\)]+)\)/', '<img alt="$1" src="$2" />', $escaped);
        // Linkler [text](href)
        $escaped = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2" target="_blank" rel="noopener noreferrer">$1<\/a>', $escaped);

        // Listeler - veya * ile başlayan satırlar
        $escaped = preg_replace('/^(?:- |\* )(.*)$/m', '<li>$1</li>', $escaped);
        // Ardışık <li> bloklarını <ul> içine al
        $escaped = preg_replace_callback('/(?:<li>.*<\/li>\r?\n?)+/m', function ($m) {
            $items = trim($m[0]);
            return '<ul>' . $items . '</ul>';
        }, $escaped);

        // Tablolar: basit pipe tablosu
        // Header | Header\n---|---\nrow|row
        $escaped = preg_replace_callback('/(^.+\|.+$)\r?\n(^[-:\s|]+$)\r?\n([\s\S]+?)(?=\n\n|$)/m', function ($m) {
            $makeRow = function ($line, $cellTag = 'td') {
                $cells = array_map('trim', explode('|', $line));
                $out = '';
                foreach ($cells as $c) {
                    if ($c === '')
                        continue;
                    $out .= "<{$cellTag}>{$c}</{$cellTag}>";
                }
                return '<tr>' . $out . '</tr>';
            };
            $thead = $makeRow($m[1], 'th');
            $tbodyLines = preg_split('/\r?\n/', trim($m[3]));
            $tbody = '';
            foreach ($tbodyLines as $ln) {
                if (trim($ln) === '' || strpos($ln, '|') === false)
                    continue;
                $tbody .= $makeRow($ln, 'td');
            }
            return '<table><thead>' . $thead . '</thead><tbody>' . $tbody . '</tbody></table>';
        }, $escaped);

        // Inline code `code`
        $escaped = preg_replace('/`([^`]+)`/', '<code>$1</code>', $escaped);

        // Paragraflar: blok olmayan parçaları <p> içine al
        $parts = preg_split('/\n\s*\n/', $escaped);
        foreach ($parts as &$p) {
            if (!preg_match('/^\s*<(h\d|ul|pre|table|li|\/li|img|p)/i', $p)) {
                $p = '<p>' . $p . '</p>';
            }
        }
        $html = implode("\n", $parts);

        // Başlıklara id ekle ve TOC üret
        $toc = [];
        $html = preg_replace_callback('/<h([1-6])>(.*?)<\/h\1>/', function ($m) use (&$toc) {
            $level = (int) $m[1];
            $text = strip_tags($m[2]);
            $id = $this->slugify($text);
            $toc[] = ['level' => $level, 'text' => $text, 'id' => $id];
            return '<h' . $level . ' id="' . $id . '">' . $m[2] . '</h' . $level . '>';
        }, $html);

        // Kod bloklarını geri koy
        foreach ($codeBlocks as $i => $block) {
            $html = str_replace("@@CODEBLOCK{$i}@@", $block, $html);
        }

        return $html;
    }

    private function slugify($text)
    {
        $text = mb_strtolower($text, 'UTF-8');
        $tr = ['ş' => 's', 'Ş' => 's', 'ı' => 'i', 'İ' => 'i', 'ğ' => 'g', 'Ğ' => 'g', 'ü' => 'u', 'Ü' => 'u', 'ö' => 'o', 'Ö' => 'o', 'ç' => 'c', 'Ç' => 'c'];
        $text = strtr($text, $tr);
        $text = preg_replace('/[^a-z0-9\s-]/u', '', $text);
        $text = preg_replace('/\s+/', '-', trim($text));
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }

    private function buildBreadcrumbs($path)
    {
        $crumbs = [];
        $crumbs[] = ['name' => 'README', 'path' => ''];
        if ($path === '' || $path === false)
            return $crumbs;
        $parts = preg_split('#[\\/]#', $path);
        $acc = '';
        foreach ($parts as $idx => $part) {
            if ($part === '')
                continue;
            $acc = $acc === '' ? $part : $acc . '/' . $part;
            $crumbs[] = [
                'name' => $this->prettyName($part),
                'path' => $acc,
            ];
        }
        return $crumbs;
    }

    private function searchDocs($q)
    {
        $base = realpath(__DIR__ . '/../../README');
        if (!$base) {
            return [];
        }
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base));
        $out = [];
        foreach ($rii as $file) {
            if ($file->isDir())
                continue;
            $path = $file->getPathname();
            if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'md')
                continue;
            $lines = @file($path, FILE_IGNORE_NEW_LINES);
            if ($lines === false)
                continue;
            $matches = [];
            $count = 0;
            foreach ($lines as $idx => $line) {
                if (stripos($line, $q) !== false) {
                    $count++;
                    $matches[] = [
                        'line' => $idx + 1,
                        'text' => $line,
                    ];
                    if (count($matches) >= 5)
                        break; // her dosyadan en fazla 5 satır
                }
            }
            if ($count > 0) {
                $rel = ltrim(str_replace($base . DIRECTORY_SEPARATOR, '', $path), DIRECTORY_SEPARATOR);
                $rel = str_replace('\\', '/', $rel);
                $display = $this->prettyName(basename($rel));
                $out[] = [
                    'file' => $rel,
                    'display' => $display,
                    'count' => $count,
                    'matches' => $matches,
                ];
            }
        }
        return $out;
    }
}
