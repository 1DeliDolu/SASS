PHP + SASS Starter
===================

Bu küçük proje PHP ile sunulan bir sayfa örneği ve SASS ile derlenen CSS içerir.

Gereksinimler
- PHP (7.4+ önerilir)
- Node.js ve npm (SASS CLI için) veya Dart Sass ikili dosyası

Kurulum ve kullanım (WSL üzerinde örnek)

1) Proje dizinine girin

```bash
cd /mnt/d/SASS/php-sass-starter
```

2) Node bağımlılıklarını yükleyin

```bash
npm install
```

3) SASS derlemesi çalıştırın
- Tek seferlik derleme:

```bash
npm run sass
```

- Geliştirme (izleme):

```bash
npm run sass:watch
```

4) PHP yerel sunucusunu başlatın

```bash
php -S localhost:8000 -t public
```

5) Tarayıcıda açın: http://localhost:8000

Notlar
- Derlenen CSS `public/css/style.css` içinde oluşturulur.
- Eğer Node yoksa, Dart Sass ikili sürümünü kullanabilirsiniz: https://sass-lang.com/install

Yeni Özellikler (Özet)
- Navbar: modern arama kutusu, durum bazlı bağlantılar (`Giriş Yap`/`Kayıt Ol` veya `Profil`/`Çıkış`).
- Giriş gereksinimi: Proje içeriği (Docs) ve arama sonuçları için login zorunlu.
- Geri yönlendirme: Login/Register sonrası, talep edilen sayfaya otomatik dönüş.
- Profil: Kullanıcı bilgileri görüntüleme ve güncelleme (şifre isteğe bağlı).
- Son giriş tarihi: Başarılı girişte `son_giris_tarihi` güncellenir ve profile yansır.
- Docs gezgini: `README/` altındaki klasör/dosyaları listeler; Markdown’ı HTML’e çevirir.
- Docs ekleri: Breadcrumbs, başlık anchor’ları ve İçindekiler (TOC), sticky yanda panel.
- Arama: `README/` içinde tam metin arama, snippet’lı sonuçlar; tıklanabilir dosya linki.
- Sayfa içi arama: Yazdıkça menü ve içerik üzerinde vurgulama (JS ile).
- SASS: `lighten()/darken()` çağrıları `sass:color` modülünden `color.adjust()` ile güncellendi.

Rotalar
- `GET /index.php` → Ana sayfa (sol menüde README klasörleri)
- `GET /index.php?action=docs&path=...` → Doküman klasörü veya Markdown dosyası
- `GET /index.php?action=search&q=...` → README içinde arama (login gerekir)
- `GET|POST /index.php?action=login` → Giriş (başarılıysa redirect)
- `GET|POST /index.php?action=register` → Kayıt (başarılıysa otomatik giriş + redirect)
- `GET /index.php?action=profile` → Profil
- `GET|POST /index.php?action=update` → Bilgileri güncelleme
- `GET /index.php?action=logout` → Çıkış

Erişim ve Yönlendirme
- Docs ve Search sayfaları login gerektirir. Giriş yapılmamışsa “Erişim Gerekli” sayfası görünür.
- İstek yapılan URL session’a `redirect_after_login` olarak kaydedilir ve login/register sonrası otomatik yönlendirilir.
- Ayrıca “Giriş Yap/Kayıt Ol” linklerine `next=/...` parametresi eklenir; bu da yönlendirmeyi sağlar.

Şifre Politikası
- En az 12 karakter.
- En az 1 büyük harf, 1 küçük harf, 1 rakam, 1 özel karakter.
- Register ve profil güncellemede (şifre alanı dolu ise) doğrulanır.

Veritabanı (MySQL)
- Bağlantı: `app/models/UserModel.php` içinde `dbname=sass`, kullanıcı `root`, şifre boş varsayımıyla tanımlı (lokalde değiştirin).
- Beklenen tablo:
  ```sql
  CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    adi VARCHAR(100) NOT NULL,
    soyadi VARCHAR(100) NOT NULL,
    mail VARCHAR(190) NOT NULL UNIQUE,
    sifre VARCHAR(255) NOT NULL,
    olusturma_tarihi DATETIME NOT NULL,
    guncelleme_tarihi DATETIME NOT NULL,
    son_giris_tarihi DATETIME NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ```
- Mevcut tabloda `son_giris_tarihi` yoksa ekleyin:
  ```sql
  ALTER TABLE users ADD COLUMN son_giris_tarihi DATETIME NULL;
  ```

Docs Görüntüleyici (README/)
- `README/` kökünden başlar, alt klasörleri listeler ve `.md` dosyalarını işler.
- Markdown desteği: başlıklar (h1–h6), listeler, linkler, görseller, basit tablolar, inline/code blokları.
- Başlıklara otomatik `id` atanır; sağ/sol düzende TOC ve breadcrumbs vardır.
- Windows ve Linux yolları normalize edilmiştir; linkler ileri eğik çizgi (`/`) kullanır.

Arama Özellikleri
- Navbar’daki arama kutusu ile README’de tam metin arama yapılır (login gerekir).
- Sonuçlarda dosya adı “insan okunur” gösterilir, altında gerçek yol ve eşleşme sayısı yer alır.
- Sonuç linkleri `docs` sayfasını doğru `path` ile açar; sayfa içi vurgulama href’leri bozmaz.

SASS Notları
- `sass:color` modülü kullanılarak `color.adjust()` tercih edildi.
- Body yatay padding responsive: 10px → 16px (≥640px) → 24px (≥1024px) → 32px (≥1280px).
- CSS derlemek için: `npm run sass` veya izlemek için `npm run sass:watch`.

Bilinen Kısıtlar
- Markdown dönüştürücü basittir; tüm Markdown özelliklerini kapsamıyor olabilir.
- Geliştirme fikri: Başlıklarda aktif TOC vurgusu, docs menüsünde ağaç görünümü, gelişmiş Markdown.

Ekran Görselleri
![Ana Sayfa](public/img/home.png)
![Giriş](public/img/login.png)
![Kayıt](public/img/register.png)
![Docs + TOC](public/img/docs-toc.png)
![Arama Sonuçları](public/img/search.png)
![Erişim Gerekli](public/img/auth-required.png)

Not: Görselleri `public/img/` altına yerleştirin; yukarıdaki yollarla otomatik yüklenir.

Klasör Yapısı (Özet)
```
php-sass-starter/
├─ app/
│  ├─ controllers/
│  │  └─ HomeController.php
│  ├─ models/
│  │  └─ UserModel.php
│  └─ views/
│     ├─ nav-bar.php, home.php, login.php, register.php, profile.php, update.php
│     ├─ docs.php, search.php, auth-required.php
├─ public/
│  ├─ index.php
│  ├─ css/ (sass çıktısı)
│  ├─ js/  (search.js)
│  └─ img/ (ekran görüntüleri — size ait)
├─ README/ (doküman klasörü ve .md dosyaları)
├─ scss/ (style.scss)
├─ package.json
└─ README.md (bu dosya)
```
