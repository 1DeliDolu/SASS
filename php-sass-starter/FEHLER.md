Süper—tamamen **düz PHP (frameworksüz)** içinde çalışacak “Gmail benzeri” posta kutusu için **çıplak PHP + MySQL + Composer** tabanlı bir iskelet bırakıyorum. (İstersen bunu sonra Laravel’e taşıyabilirsin; ama bu haliyle tek başına çalışır.)

Aşağıdakiler: veritabanı şeması, dosya yapısı, Composer paketleri, minimal router, Gelen/Giden/Zamanlı e-postalar, IMAP senkron, PHPMailer ile gönderim, cron ile zamanlı gönderme ve **Sass (SCSS)** stilleri.

---

# 1) Dosya yapısı

```
project/
  public/
    index.php
    assets/
      app.css
  src/
    Config.php
    DB.php
    Mailer.php
    ImapSync.php
    MessageRepo.php
    Views.php
  views/
    layout.php
    list.php
    thread.php
    compose.php
  bin/
    cron_send.php
    cron_imap.php
  scss/
    app.scss
  .env
  composer.json
```

---

# 2) Composer paketleri

```json
{
  "require": {
    "phpmailer/phpmailer": "^6.9",
    "vlucas/phpdotenv": "^5.6",
    "php-imap/php-imap": "^5.0"
  },
  "autoload": {
    "psr-4": { "App\\": "src/" }
  }
}
```

Kurulum:

```bash
composer install
```

---

# 3) .env örneği

```
DB_DSN="mysql:host=127.0.0.1;dbname=mailapp;charset=utf8mb4"
DB_USER="root"
DB_PASS=""

MAIL_FROM="no-reply@yourapp.test"
MAIL_FROM_NAME="YourApp Mailer"

SMTP_HOST="127.0.0.1"
SMTP_PORT="1025"
SMTP_USER=""
SMTP_PASS=""
SMTP_ENCRYPTION=""

IMAP_HOST="imap.example.com"
IMAP_PORT="993"
IMAP_ENCRYPTION="ssl"
IMAP_USER="you@example.com"
IMAP_PASS="yourpass"
```

---

# 4) MySQL şeması (özet)

```sql
CREATE TABLE threads (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  subject VARCHAR(998),
  last_message_at DATETIME,
  is_archived TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX(last_message_at)
);

CREATE TABLE messages (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  thread_id BIGINT,
  direction ENUM('in','out') NOT NULL,
  status ENUM('received','queued','sent','failed','draft','scheduled') NOT NULL,
  from_email VARCHAR(254), from_name VARCHAR(190),
  to_json JSON, cc_json JSON, bcc_json JSON,
  subject VARCHAR(998),
  text_body MEDIUMTEXT, html_body MEDIUMTEXT,
  message_id VARCHAR(255), in_reply_to VARCHAR(255),
  headers_json JSON,
  sent_at DATETIME NULL, received_at DATETIME NULL,
  scheduled_at DATETIME NULL, fail_reason TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX(thread_id), INDEX(status), INDEX(scheduled_at),
  FULLTEXT(subject, text_body, html_body)
);

CREATE TABLE attachments (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  message_id BIGINT,
  file_name VARCHAR(255),
  mime VARCHAR(100),
  size INT,
  storage_path VARCHAR(255),
  cid VARCHAR(255) NULL
);
```

---

# 5) SCSS (Sass) – `scss/app.scss`

```scss
$mail-bg: #f5f7fb;
$accent: #2563eb;

body { margin: 0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background: $mail-bg; }

.mail-app {
  display: grid; grid-template-columns: 260px 420px 1fr; gap: 16px;
  height: 100vh; padding: 16px;

  .card { background:#fff; border-radius:14px; box-shadow:0 8px 24px rgba(0,0,0,.06); }

  .sidebar.card { padding:16px;
    .compose { width:100%; padding:12px 14px; border:0; border-radius:999px; color:#fff;
      font-weight:700; background: linear-gradient(135deg, $accent, lighten($accent,10%)); cursor:pointer; margin-bottom:12px; }
    nav a { display:block; padding:8px 10px; margin:4px 0; text-decoration:none; color:#333; border-radius:8px;
      &:hover{ background:#eef2ff } &.active{ background:#e0e7ff; color:$accent; font-weight:700; } }
  }

  .list.card { padding:10px; overflow:auto;
    .item { display:grid; grid-template-columns: 160px 1fr auto; gap:12px; align-items:center; padding:12px; border-radius:10px; cursor:pointer;
      &:hover{ background:#f8fafc } &.unread{ background:#eef2ff; .subject{font-weight:700} }
      .from{font-weight:600} .subject{ white-space:nowrap; overflow:hidden; text-overflow:ellipsis }
      .meta{ color:#888; font-size:12px; display:flex; gap:8px; align-items:center }
    }
  }

  .reader.card { padding:16px; overflow:auto; display:flex; flex-direction:column; gap:12px;
    .bubble{ max-width:70%; padding:12px 14px; border-radius:14px; line-height:1.5; box-shadow:0 4px 14px rgba(0,0,0,.06);
      &.me{ background:#e0f2fe; align-self:flex-end } &.you{ background:#f1f5f9; align-self:flex-start } }
  }

  @media (max-width: 1200px) { grid-template-columns: 220px 1fr; .reader.card{ display:none } }
}
```

Derleme:

```bash
sass scss/app.scss public/assets/app.css
```

---

# 6) Basit router – `public/index.php`

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Config; use App\DB; use App\Views; use App\MessageRepo;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__)); $dotenv->load();
Config::boot($_ENV); DB::boot(Config::$dbDsn, Config::$dbUser, Config::$dbPass);

$action = $_GET['a'] ?? 'inbox';

$repo = new MessageRepo();

switch ($action) {
  case 'inbox':    $data = $repo->listInbox();  Views::render('list', $data + ['active'=>'inbox']); break;
  case 'sent':     $data = $repo->listSent();   Views::render('list', $data + ['active'=>'sent']);  break;
  case 'scheduled':$data = $repo->listScheduled(); Views::render('list', $data + ['active'=>'scheduled']); break;
  case 'thread':   $id = (int)($_GET['id'] ?? 0); $data = $repo->getThread($id); Views::render('thread', $data); break;
  case 'compose':  Views::render('compose', []); break;
  case 'send':     if($_SERVER['REQUEST_METHOD']==='POST'){ echo json_encode($repo->send($_POST, $_FILES)); } break;
  default: http_response_code(404); echo "Not Found";
}
```

---

# 7) Altyapı sınıfları (kısaltılmış)

**`src/Config.php`**

```php
<?php
namespace App;
class Config {
  public static string $dbDsn; public static string $dbUser; public static string $dbPass;
  public static string $smtpHost; public static int $smtpPort; public static string $smtpUser; public static string $smtpPass; public static ?string $smtpEnc;
  public static string $from; public static string $fromName;
  public static function boot(array $e){
    self::$dbDsn=$e['DB_DSN']; self::$dbUser=$e['DB_USER']; self::$dbPass=$e['DB_PASS'];
    self::$smtpHost=$e['SMTP_HOST']; self::$smtpPort=(int)$e['SMTP_PORT']; self::$smtpUser=$e['SMTP_USER']; self::$smtpPass=$e['SMTP_PASS']; self::$smtpEnc=$e['SMTP_ENCRYPTION']?:null;
    self::$from=$e['MAIL_FROM']; self::$fromName=$e['MAIL_FROM_NAME'];
  }
}
```

**`src/DB.php`**

```php
<?php
namespace App; use PDO;
class DB {
  public static PDO $pdo;
  public static function boot($dsn,$u,$p){
    self::$pdo = new PDO($dsn,$u,$p,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
  }
}
```

**`src/Views.php`**

```php
<?php
namespace App;
class Views {
  public static function render(string $view, array $data){
    extract($data);
    include __DIR__ . '/../views/layout.php';
  }
}
```

**`src/Mailer.php` (PHPMailer ile gönderim)**

```php
<?php
namespace App; use PHPMailer\PHPMailer\PHPMailer;

class Mailer {
  public static function send(array $msg, array $attachments=[]){
    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->Host = Config::$smtpHost; $m->Port = Config::$smtpPort;
    if(Config::$smtpEnc){ $m->SMTPSecure = Config::$smtpEnc; }
    if(Config::$smtpUser){ $m->SMTPAuth = true; $m->Username = Config::$smtpUser; $m->Password = Config::$smtpPass; }
    $m->setFrom(Config::$from, Config::$fromName);
    foreach($msg['to'] as $to){ $m->addAddress($to); }
    foreach(($msg['cc']??[]) as $cc){ $m->addCC($cc); }
    foreach(($msg['bcc']??[]) as $bcc){ $m->addBCC($bcc); }
    $m->Subject = $msg['subject'] ?: '(No subject)';
    if(!empty($msg['html_body'])){ $m->isHTML(true); $m->Body = $msg['html_body']; $m->AltBody = $msg['text_body'] ?? strip_tags($msg['html_body']); }
    else { $m->Body = $msg['text_body'] ?? ''; }
    foreach ($attachments as $f){ $m->addAttachment($f['tmp_name'], $f['name']); }
    $m->send();
  }
}
```

**`src/MessageRepo.php`**

```php
<?php
namespace App; use App\DB; use PDO; use DateTime;

class MessageRepo {
  public function listInbox(){ return ['messages'=>$this->q("SELECT * FROM messages WHERE direction='in' AND status='received' ORDER BY COALESCE(received_at,created_at) DESC LIMIT 100")]; }
  public function listSent(){ return ['messages'=>$this->q("SELECT * FROM messages WHERE direction='out' AND status='sent' ORDER BY COALESCE(sent_at,created_at) DESC LIMIT 100")]; }
  public function listScheduled(){ return ['messages'=>$this->q("SELECT * FROM messages WHERE status='scheduled' ORDER BY scheduled_at ASC LIMIT 100")]; }

  public function getThread(int $threadId){
    $t = $this->q1("SELECT * FROM threads WHERE id=?",[$threadId]);
    $msgs = $this->q("SELECT * FROM messages WHERE thread_id=? ORDER BY created_at ASC",[$threadId]);
    return ['thread'=>$t,'messages'=>$msgs];
  }

  public function send(array $post, array $files){
    $to = array_filter(array_map('trim', explode(',', $post['to'] ?? '')));
    $cc = array_filter(array_map('trim', explode(',', $post['cc'] ?? '')));
    $bcc= array_filter(array_map('trim', explode(',', $post['bcc'] ?? '')));
    $subject = $post['subject'] ?? '(No subject)';
    $html = $post['html_body'] ?? null; $text = $post['text_body'] ?? null;
    $scheduled_at = !empty($post['scheduled_at']) ? (new DateTime($post['scheduled_at']))->format('Y-m-d H:i:s') : null;

    // thread bul/oluştur
    $threadId = $this->ensureThread($subject);

    DB::$pdo->prepare("INSERT INTO messages(thread_id,direction,status,to_json,cc_json,bcc_json,subject,html_body,text_body,scheduled_at,created_at,updated_at)
      VALUES(?, 'out', ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())")
      ->execute([$threadId, $scheduled_at ? 'scheduled' : 'queued', json_encode($to), json_encode($cc), json_encode($bcc), $subject, $html, $text, $scheduled_at]);

    $msgId = (int)DB::$pdo->lastInsertId();

    // ekler dosya sistemine
    if(!empty($files['attachments']['name'][0])){
      for($i=0; $i<count($files['attachments']['name']); $i++){
        if($files['attachments']['error'][$i]===UPLOAD_ERR_OK){
          $tmp = $files['attachments']['tmp_name'][$i];
          $name = $files['attachments']['name'][$i];
          $mime = $files['attachments']['type'][$i];
          $dest = 'storage/'.uniqid('',true).'_'.$name;
          @mkdir(dirname(__DIR__).'/storage',0777,true);
          move_uploaded_file($tmp, __DIR__ . '/../'.$dest);
          DB::$pdo->prepare("INSERT INTO attachments(message_id,file_name,mime,size,storage_path) VALUES(?,?,?,?,?)")
            ->execute([$msgId,$name,$mime,filesize(__DIR__.'/../'.$dest),$dest]);
        }
      }
    }

    // hemen kuyruğa ver (basitçe “hemen gönder”)
    if(!$scheduled_at){ $this->dispatchSend($msgId); }

    return ['ok'=>true,'id'=>$msgId];
  }

  private function dispatchSend(int $messageId){
    // Basit: hemen çağır. (Gerçekte job queue önerilir.)
    require_once __DIR__.'/Mailer.php';
    $msg = $this->q1("SELECT * FROM messages WHERE id=?",[$messageId]);
    $atts = $this->q("SELECT * FROM attachments WHERE message_id=?",[$messageId]);

    try{
      Mailer::send([
        'to'=>json_decode($msg['to_json']??'[]',true),
        'cc'=>json_decode($msg['cc_json']??'[]',true),
        'bcc'=>json_decode($msg['bcc_json']??'[]',true),
        'subject'=>$msg['subject'],'html_body'=>$msg['html_body'],'text_body'=>$msg['text_body']
      ], array_map(fn($a)=>['tmp_name'=>__DIR__.'/../'.$a['storage_path'],'name'=>$a['file_name']], $atts));
      DB::$pdo->prepare("UPDATE messages SET status='sent', sent_at=NOW(), updated_at=NOW() WHERE id=?")->execute([$messageId]);
    }catch(\Throwable $e){
      DB::$pdo->prepare("UPDATE messages SET status='failed', fail_reason=?, updated_at=NOW() WHERE id=?")->execute([$e->getMessage(), $messageId]);
    }
  }

  private function ensureThread(string $subject){
    $t = $this->q1("SELECT id FROM threads WHERE subject=? LIMIT 1", [$subject]);
    if($t) return (int)$t['id'];
    DB::$pdo->prepare("INSERT INTO threads(subject,last_message_at,created_at,updated_at) VALUES(?,NOW(),NOW(),NOW())")
      ->execute([$subject]);
    return (int)DB::$pdo->lastInsertId();
  }

  private function q($sql,$p=[]){ $st=DB::$pdo->prepare($sql); $st->execute($p); return $st->fetchAll(); }
  private function q1($sql,$p=[]){ $st=DB::$pdo->prepare($sql); $st->execute($p); return $st->fetch(); }
}
```

**IMAP senkron – `src/ImapSync.php`**

```php
<?php
namespace App;
use PhpImap\Mailbox;

class ImapSync {
  public static function run(){
    $mailbox = new Mailbox(
      '{'.$_ENV['IMAP_HOST'].':'.$_ENV['IMAP_PORT'].'/imap/'.$_ENV['IMAP_ENCRYPTION'].'}INBOX',
      $_ENV['IMAP_USER'], $_ENV['IMAP_PASS'], __DIR__.'/../storage/imap', 'UTF-8'
    );
    $mailsIds = $mailbox->searchMailbox('UNSEEN SINCE "'.date('d-M-Y', strtotime('-7 days')).'"');
    if(!$mailsIds) return;
    foreach($mailsIds as $id){
      $mail = $mailbox->getMail($id,false);
      $subject = $mail->subject ?: '(No subject)';
      $threadId = (new MessageRepo())->ensureThread($subject);
      DB::$pdo->prepare("INSERT INTO messages (thread_id,direction,status,from_email,from_name,to_json,subject,text_body,html_body,received_at,message_id,created_at,updated_at)
        VALUES(?, 'in','received', ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())")
        ->execute([$threadId, $mail->fromAddress, $mail->fromName, json_encode($mail->to), $subject, $mail->textPlain, $mail->textHtml, $mail->date, $mail->messageId]);

      $msgId = (int)DB::$pdo->lastInsertId();

      foreach($mail->getAttachments() as $att){
        $path = 'storage/'.uniqid('',true).'_'.$att->name;
        @file_put_contents(__DIR__.'/../'.$path, $att->getContents());
        DB::$pdo->prepare("INSERT INTO attachments(message_id,file_name,mime,size,storage_path,cid) VALUES(?,?,?,?,?,?)")
          ->execute([$msgId,$att->name,$att->mimeType,$att->size,$path,$att->contentId]);
      }
      DB::$pdo->prepare("UPDATE threads SET last_message_at=NOW(), updated_at=NOW() WHERE id=?")->execute([$threadId]);
    }
  }
}
```

---

# 8) Cron scriptleri

**Zamanlı gönderim – `bin/cron_send.php`**

```php
<?php
require __DIR__.'/../vendor/autoload.php';
use App\Config; use App\DB; use App\MessageRepo;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__)); $dotenv->load();
Config::boot($_ENV); DB::boot(Config::$dbDsn, Config::$dbUser, Config::$dbPass);

$rows = DB::$pdo->query("SELECT id FROM messages WHERE status='scheduled' AND scheduled_at <= NOW()")->fetchAll();
$repo = new MessageRepo();
foreach($rows as $r){ // her satırı gönder
  // basitçe MessageRepo içindeki dispatchSend'i tetiklemek için send akışını kopyalamıyor, küçük hack:
  DB::$pdo->prepare("UPDATE messages SET status='queued' WHERE id=?")->execute([$r['id']]);
  // dispatch gibi: private fonksiyona erişemeyiz; burada hızlı bir çağrı:
  // pratikte Mailer::send ile tekrar okuma
  $msg = DB::$pdo->prepare("SELECT * FROM messages WHERE id=?"); $msg->execute([$r['id']]); $m=$msg->fetch();
  $atts = DB::$pdo->prepare("SELECT * FROM attachments WHERE message_id=?"); $atts->execute([$r['id']]); $as=$atts->fetchAll();

  try{
    \App\Mailer::send([
      'to'=>json_decode($m['to_json']??'[]',true),
      'cc'=>json_decode($m['cc_json']??'[]',true),
      'bcc'=>json_decode($m['bcc_json']??'[]',true),
      'subject'=>$m['subject'],'html_body'=>$m['html_body'],'text_body'=>$m['text_body']
    ], array_map(fn($a)=>['tmp_name'=>__DIR__.'/../'.$a['storage_path'],'name'=>$a['file_name']], $as));
    DB::$pdo->prepare("UPDATE messages SET status='sent', sent_at=NOW(), updated_at=NOW() WHERE id=?")->execute([$r['id']]);
  }catch(Throwable $e){
    DB::$pdo->prepare("UPDATE messages SET status='failed', fail_reason=?, updated_at=NOW() WHERE id=?")->execute([$e->getMessage(), $r['id']]);
  }
}
```

**IMAP senk – `bin/cron_imap.php`**

```php
<?php
require __DIR__.'/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__)); $dotenv->load();
App\Config::boot($_ENV); App\DB::boot(App\Config::$dbDsn, App\Config::$dbUser, App\Config::$dbPass);
App\ImapSync::run();
```

**Cron ayarı (her dakika):**

```
* * * * * /usr/bin/php /path/to/project/bin/cron_send.php >/dev/null 2>&1
*/2 * * * * /usr/bin/php /path/to/project/bin/cron_imap.php >/dev/null 2>&1
```

---

# 9) Görünümler

**`views/layout.php`**

```php
<?php $active = $active ?? null; ?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <title>Mail App</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/assets/app.css" rel="stylesheet">
</head>
<body>
<div class="mail-app">
  <aside class="sidebar card">
    <a class="compose" href="?a=compose">Yeni Mesaj</a>
    <nav>
      <a href="?a=inbox" class="<?= $active==='inbox'?'active':'' ?>">Gelen Kutusu</a>
      <a href="?a=sent" class="<?= $active==='sent'?'active':'' ?>">Gönderilmiş</a>
      <a href="?a=scheduled" class="<?= $active==='scheduled'?'active':'' ?>">Zamanlı</a>
    </nav>
  </aside>

  <section class="list card">
    <?php include __DIR__ . '/list.php'; ?>
  </section>

  <section class="reader card">
    <?php if(isset($thread)) include __DIR__ . '/thread.php'; ?>
  </section>
</div>
</body>
</html>
```

**`views/list.php`**

```php
<?php if(empty($messages)): ?>
  <p style="padding:16px">Hiç mesaj yok.</p>
<?php else: foreach($messages as $m): ?>
  <article class="item <?= ($m['status']==='received' && empty($m['seen']))?'unread':'' ?>" onclick="location='?a=thread&id=<?= (int)$m['thread_id'] ?>'">
    <div class="from"><?= htmlspecialchars($m['from_name'] ?: $m['from_email'] ?: 'Siz') ?></div>
    <div class="subject"><?= htmlspecialchars($m['subject'] ?: '(No subject)') ?></div>
    <div class="meta">
      <time><?= htmlspecialchars(($m['received_at'] ?? $m['sent_at'] ?? $m['created_at'])) ?></time>
    </div>
  </article>
<?php endforeach; endif; ?>
```

**`views/thread.php`**

```php
<?php foreach(($messages ?? []) as $m): ?>
  <div class="bubble <?= $m['direction']==='out' ? 'me':'you' ?>">
    <?= $m['html_body'] ? $m['html_body'] : nl2br(htmlspecialchars($m['text_body'] ?? '')) ?>
  </div>
<?php endforeach; ?>
```

**`views/compose.php`**

```php
<!doctype html><html lang="tr"><head><meta charset="utf-8"><link href="/assets/app.css" rel="stylesheet"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Yeni Mesaj</title></head><body>
<div class="mail-app">
  <aside class="sidebar card"><a class="compose" href="?a=inbox">Geri</a></aside>
  <section class="list card" style="padding:16px">
    <form method="post" action="?a=send" enctype="multipart/form-data" onsubmit="sendForm(event)">
      <label>Alıcı(lar) (virgülle): <input name="to" style="width:100%"></label><br><br>
      <label>Konu: <input name="subject" style="width:100%"></label><br><br>
      <label>Zamanlı gönder (YYYY-MM-DD HH:MM): <input name="scheduled_at" placeholder=""></label><br><br>
      <label>Ekler: <input type="file" name="attachments[]" multiple></label><br><br>
      <label>İçerik (HTML): <textarea name="html_body" rows="10" style="width:100%"></textarea></label><br><br>
      <button type="submit">Gönder</button>
    </form>
    <script>
      async function sendForm(e){
        e.preventDefault();
        const fd = new FormData(e.target);
        const res = await fetch('?a=send',{method:'POST',body:fd});
        const j = await res.json();
        alert(j.ok?'Gönderildi/kuyruğa alındı':'Hata');
        location='?a=<?= isset($_GET['from'])?htmlspecialchars($_GET['from']):'inbox' ?>';
      }
    </script>
  </section>
  <section class="reader card"></section>
</div>
</body></html>
```

---

# 10) Güvenlik ve notlar

* Gelen HTML’leri **sanitize** et (ör. `HTML Purifier`) – burada basit tuttuk.
* SMTP/IMAP bilgilerini `.env`’de tut, repoya koyma.
* Ek dosyalarında MIME/uzantı doğrulaması yap, virüs taraması (ClamAV) ekleyebilirsin.
* Full-text arama için InnoDB FT ya da Elasticsearch kullanabilirsin.
* Queue yoksa bile cron’la temel zamanlı gönderim sağlanıyor; üretimde gerçek kuyruk (Supervisor) önerilir.

---

İstersen bir **ZIP halinde tam çalışan örnek** (tüm dosyalar dahil) de oluşturabilirim. Ya da “Gmail etiketleri” (Yıldızlı, Çöp vb.) ve arama kutusu ekleyelim. Hangi yönden ilerleyelim?
