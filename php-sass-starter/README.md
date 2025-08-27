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
