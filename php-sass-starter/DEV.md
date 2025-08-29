Geliştirme Planı (DEV)
======================

Bu dosya, projenin sonraki iterasyonlarında uygulanacak mimari ve görevleri içerir. Amaç; rollere bağlı yetkilendirme, proje yönetimi ve kullanıcı/ müşteri akışlarını eklemektir.

Roller ve Yetkiler
- Admin: Tüm kullanıcıları ve projeleri yönetir; atama yapar, roller verir, rapor alır.
- Çalışan: Atandığı projeleri ve kendi görevlerini görür/ günceller.
- Müşteri: Kendi projelerini, hangi çalışana verildiğini ve durumlarını görür; kendi bilgilerini günceller.

Veri Modeli (Öneri)
- users
  - id, adi, soyadi, mail (unique), sifre, role ENUM('admin','calisan','musteri')
  - olusturma_tarihi, guncelleme_tarihi, son_giris_tarihi
- projects
  - id, ad, aciklama, musteri_id (FK → users.id, role='musteri'), durum ENUM('yeni','devam','tamam','iptal')
  - olusturma_tarihi, guncelleme_tarihi
- project_assignments
  - id, project_id (FK → projects.id), calisan_id (FK → users.id, role='calisan')
  - rol_projede (örn: 'sorumlu','uye'), atama_tarihi, durum (opsiyonel)

SQL Taslakları
- users tablosuna role kolonu ekleme:
  ALTER TABLE users ADD COLUMN role ENUM('admin','calisan','musteri') NOT NULL DEFAULT 'calisan' AFTER sifre;
- projects tablosu:
  CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(200) NOT NULL,
    aciklama TEXT NULL,
    musteri_id INT NULL,
    durum ENUM('yeni','devam','tamam','iptal') NOT NULL DEFAULT 'yeni',
    olusturma_tarihi DATETIME NOT NULL,
    guncelleme_tarihi DATETIME NOT NULL,
    CONSTRAINT fk_projects_musteri FOREIGN KEY (musteri_id) REFERENCES users(id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
- project_assignments tablosu:
  CREATE TABLE project_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    calisan_id INT NOT NULL,
    rol_projede VARCHAR(50) NULL,
    atama_tarihi DATETIME NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_pa_project FOREIGN KEY (project_id) REFERENCES projects(id),
    CONSTRAINT fk_pa_user FOREIGN KEY (calisan_id) REFERENCES users(id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

Yetkilendirme (ACL) Stratejisi
- Session bazlı $_SESSION['user']['role'] kontrolü.
- Yardımcılar:
  - requireRole(...roles) → Aksi halde 403/ yönlendirme.
  - canAccessProject(user, project) → Admin true; Çalışan atanmışsa true; Müşteri kendi projesi ise true.
- Router seviyesinde korumalar; controller girişinde erken çıkış.

Controller ve Sayfalar (Plan)
- AdminController
  - Kullanıcı listesi/rol yönetimi (admin atar), müşteri/çalışan oluşturma
  - Proje CRUD, atama yönetimi (çalışan ↔ proje)
- ProjectController
  - index: Kullanıcı rolüne göre görünür projeler (admin=hepsi, çalışanın atandıkları, müşterinin sahip olduğu)
  - show: Proje detayı (atanan çalışanlar, müşteri, durum, notlar)
  - create/update/delete (rol: admin)
  - assign/unassign (rol: admin)
- AccountController
  - me: Kullanıcının kendi sayfası (kendi bilgileri ve projeleri)
  - update: Bilgileri güncelle (mevcut akış genişletilecek)

Görünümler (UI)
- Navbar
  - Rol bazlı linkler: Admin Paneli, Projelerim, Müşteri Projeleri vb.
- Paneller
  - Admin Paneli: kullanıcılar, projeler, atamalar tabloları
  - Çalışan Paneli: “Atandığım Projeler” listesi, duruma göre filtre
  - Müşteri Paneli: “Projelerim” ve hangi çalışana atandığı
- Proje Sayfası
  - Özet (müşteri, durum), atananlar, aktiviteler (ileride)

İş Akışları
- Kullanıcı Oluşturma
  - Admin kullanıcı oluşturur ve role atar (kayıt sayfası default olarak müşteri/çalışan yerine admin panelinden yönetim tercih edilir).
- Proje Oluşturma
  - Admin yeni proje açar; müşteri (kullanıcı) ile ilişkilendirir; çalışan ataması yapar.
- Görüntüleme
  - Çalışan sadece atandığı projeleri görür.
  - Müşteri sadece kendi projelerini görür ve kime verildiğini görür.

Güvenlik ve Kalite
- Şifre politikası aktif (12+ ve karma kurallar).
- PDO prepared statements kullanımı devam.
- CSRF koruması: Formlara token ekleme (yapılacak).
- Giriş denemesi sınırlama ve audit log (yapılacak).

Yol Haritası (TODO)
1) DB şema güncellemeleri ve seed (roller, örnek kullanıcılar + projeler)
2) Model katmanı: ProjectModel, AssignmentModel
3) Controller ve rotalar: Admin/Project/Account
4) Görünümler: Admin Paneli, Proje listesi/detayı, rol bazlı navbar
5) ACL yardımcıları ve controller korumaları
6) CSRF ve temel audit log
7) UX geliştirmeleri (filtreler, sayfalama, arama)

Kabul Kriterleri (Örnek)
- Admin giriş yaptı: kullanıcı/ proje/ atama CRUD tam.
- Çalışan giriş yaptı: yalnızca atandığı projeler listeleniyor; başka erişimler engelli.
- Müşteri giriş yaptı: yalnızca kendi projelerini görüyor; projede atanan çalışan(lar) listeleniyor.
- Tüm formlar CSRF token ile korunuyor (eklenecek).

Açık Sorular
- Kayıt akışında rol seçimi açık mı olmalı, yoksa admin mi atayacak?
- Proje durumları ve iş akışı (kanban/ durum geçmişi) detaylandırılacak mı?
- Müşteri tarafında faturalama/ sözleşme verisi tutulacak mı?

Teknik Notlar
- PHP 7.4 uyumluluğu korunacak (strpos tercihleri vb.).
- Windows/ Linux path uyumluluğu (docs modülünde uygulandı) genel yaklaşıma örnek olacak.
- Sass tarafında `sass:color` modülü benimsendi, yeni stiller bu yapıda yazılacak.
