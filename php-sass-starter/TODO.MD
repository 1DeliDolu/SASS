# TODO (E‑posta Bildirimleri)

Amaç: Admin panelinde projelerde yapılan değişikliklerde ilgili çalışan(lar)a ve müşteri(ye) otomatik e‑posta bildirimi göndermek.

Ön Hazırlık
- [ ] Mail yapılandırması için .env değişkenlerini ekle/ayarla:
  - `SMTP_HOST`, `SMTP_PORT`, `SMTP_USER`, `SMTP_PASS`, `SMTP_SECURE` (tls/ssl), `SMTP_FROM`, `SMTP_FROM_NAME`
  - Opsiyonel: `MAIL_ENABLED=true|false` (dev ortamında kapatmak için)
- [ ] Basit bir `Mailer` yardımcı sınıfı ekle (ör. PHPMailer veya native SMTP):
  - `send(to, subject, htmlBody, textBody = null)`
  - Ortak header/footer şablonu
  - Hata durumunda log kaydı ve sessiz geri dönüş (Admin’e uyarı göstermek opsiyonel)

Tetikleyiciler (Olaylar)
- [ ] Proje oluşturma (admin_project_create)
  - Müşteriye: “Yeni proje oluşturuldu”
  - Proje atanmış çalışanlar varsa: “Yeni projeye atandınız”
- [ ] Proje güncelleme (admin_project_edit POST)
  - Müşteriye: ad/açıklama/durum değişiklikleri özeti
  - Çalışan(lar)a: yalnızca anlamlı değişiklikleri (durum, müşteri, ad) içeren bildirim
- [ ] Proje silme (admin_project_delete)
  - Müşteri ve çalışan(lar)a bilgilendirme (opsiyonel/uyarı tonunda)
- [ ] Atama ekleme/çıkarma (admin_assignment_add / admin_assignment_remove)
  - Eklenen çalışana: projeye atandınız
  - Kaldırılan çalışana: projeden çıkarıldınız
- [ ] Milestone ekleme/tamamlama (admin_milestone_add / admin_milestone_done)
  - Müşteriye ve atanmış çalışan(lar)a bilgilendirme (başlık ve termin ile)

Uygulama Adımları
- [ ] `app/lib/Mailer.php` ekle (SMTP ile), `.env` okumayı `Env::load` üzerinden yap
- [ ] `app/views/emails/` altında basit şablonlar oluştur
  - `project_created.php`, `project_updated.php`, `assignment_added.php`, `assignment_removed.php`, `milestone_added.php`, `milestone_done.php`
- [ ] AdminController’da ilgili aksiyonlara bildirim çağrılarını ekle
  - Kullanıcı listelerini getir (müşteri + atanmış çalışanlar) ve `Mailer::send` ile gönder
- [ ] Güncelleme alanlarını karşılaştır (diff) ve e‑postada sadece değişen alanları özetle
- [ ] .env örneğini güncelle (`.env.example`)
- [ ] `MAIL_ENABLED=false` iken e‑posta yerine loga yaz (dev kolaylığı)

Kalite / Güvenlik
- [ ] E‑posta adreslerini trim/validate et
- [ ] Konu ve içerikte HTML özel karakterlerini kaçır
- [ ] Yoğun gönderim için (opsiyonel) kuyruklama/geri deneme taslağı
- [ ] Hata logu ve Admin panelinde kısa uyarı göstergesi (opsiyonel)

Test / Doğrulama
- [ ] Dev ortamında `MAIL_ENABLED=false` ile log dosyasını kontrol et
- [ ] Örnek bir SMTP (Mailhog/PaperCut veya gerçek SMTP) ile uçtan uca doğrula
- [ ] Her tetikleyici için en az bir olumlu/olumsuz senaryo test et

Zamanlama (öneri)
- Gün 1: Mailer, .env, şablon yapısı
- Gün 2: Tetikleyici entegrasyonları (create/update/delete)
- Gün 3: Atama ve milestone bildirimleri, test ve iyileştirme

Ek İyileştirmeler (Öneriler)
- [ ] Güncelleme e-postasında alan bazlı ayrıntılı diff (eski → yeni) gösterimi
- [ ] E‑posta içeriklerini I18n ile TR/DE yerelleştirme (konu + gövde)
- [ ] Mailer’ı gerçek SMTP gönderime geçirmek (PHPMailer ya da native SMTP soketi; TLS/SSL ve kimlik doğrulama desteği)
- [ ] `app/views/emails/` altında ortak header/footer’lı HTML e‑posta şablonları (markalama/tema ile)
- [ ] Bildirim tercihleri (kullanıcı bazlı aç/kapat) ve yoğun gönderime karşı hız sınırlama
- [ ] “Test e‑postası gönder” ve “tekrar gönder” yardımcı aksiyonları (sadece admin)
