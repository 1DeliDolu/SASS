## 🖥️ Sass Kurulumu (Install Sass)

### 🖱️ Uygulamalar (Applications)

Mac, Windows ve Linux için birkaç dakika içinde Sass ile çalışmaya başlamanızı sağlayacak birçok uygulama bulunmaktadır. Çoğu ücretsiz indirilebilir, ancak bazıları ücretlidir (ve kesinlikle buna değerdir).

* CodeKit (Ücretli) – Mac
* Prepros (Ücretli) – Mac, Windows, Linux

### 📚 Kütüphaneler (Libraries)

Sass ekibi, standart JavaScript API’sini destekleyen iki Node.js paketini geliştirmektedir:

* `sass` paketi saf JavaScript’tir, biraz daha yavaştır ancak Node.js’in desteklediği tüm platformlarda çalışır.
* `sass-embedded` paketi Dart VM etrafında bir JS API’si sağlar, daha hızlıdır ancak yalnızca Windows, Mac OS ve Linux’u destekler.

Ayrıca topluluk tarafından geliştirilen bazı dil sarmalayıcıları (wrappers) da bulunmaktadır:

* Ruby
* Swift
* Java, örneğin:

  * Bir Gradle eklentisi.
  * Sass CLI’yi saran hafif bir Maven eklentisi. Kullanılacak Sass sürümünü belirtir. CLI argümanları `<args>` listesi ile geçirilir.
  * Dart Sass’ı içeren kapsamlı bir Maven eklentisi. Sabit bir dart-sass sürümü ile gelir. CLI argümanları Maven parametreleri olarak kullanıma sunulur.

### ⌨️ Komut Satırı (Command Line)

Sass’ı komut satırına kurduğunuzda, `.sass` ve `.scss` dosyalarını `.css` dosyalarına derlemek için `sass` yürütülebilir dosyasını çalıştırabilirsiniz. Örneğin:

```
sass source/stylesheets/index.scss build/stylesheets/index.css
```

👉 Bu komut, `index.scss` dosyasını `index.css` dosyasına derler.

Önce aşağıdaki seçeneklerden biri ile Sass’ı kurun, ardından kurulumun doğru yapıldığından emin olmak için:

```
sass --version
```

👉 Bu komut, kurulu Sass sürümünü (örneğin `1.91.0`) gösterir.

Daha fazla bilgi için:

```
sass --help
```

👉 Bu komut, komut satırı arayüzü hakkında detaylı yardım gösterir.

### 🌍 Her Yerde Kurulum (Standalone)

Windows, Mac veya Linux için GitHub’dan işletim sisteminize uygun paketi indirip `PATH` değişkeninize ekleyerek Sass’ı kurabilirsiniz.
Ek bağımlılık yoktur, başka bir şey yüklemenize gerek kalmaz.

### 🌍 Her Yerde Kurulum (npm)

Node.js kullanıyorsanız, Sass’ı npm ile şu komutla kurabilirsiniz:

```
npm install -g sass
```

👉 Bu, Sass’ın saf JavaScript implementasyonunu kurar. Biraz daha yavaştır ama aynı arayüze sahiptir. Daha hızlı bir implementasyona geçmeniz gerekirse kolayca değiştirebilirsiniz.

### 🪟 Windows’ta Kurulum (Chocolatey)

Windows için `Chocolatey` paket yöneticisini kullanıyorsanız, Dart Sass’ı şu komutla kurabilirsiniz:

```
choco install sass
```

👉 Bu komut, Sass’ı Chocolatey aracılığıyla Windows’a kurar.

### 🍎 Mac OS X veya Linux’ta Kurulum (Homebrew)

Mac OS X veya Linux için `Homebrew` paket yöneticisini kullanıyorsanız, Dart Sass’ı şu komutla kurabilirsiniz:

```
brew install sass/sass/sass
```

👉 Bu komut, Homebrew üzerinden Sass kurulumunu gerçekleştirir.
