## 🛠️ Sass’i Kurma (setup)

Sass’i kullanmaya başlamadan önce onu projenize kurmanız gerekir. Sadece burada göz atmak istiyorsanız devam edebilirsiniz, ancak önce Sass’i yüklemenizi tavsiye ederiz. Her şeyi nasıl ayarlayacağınızı öğrenmek için buraya gidin.

---

## 🔄 Ön İşleme (preprocessing)

CSS tek başına eğlenceli olabilir, ancak stil sayfaları giderek daha büyük, daha karmaşık ve bakım yapılması daha zor hale geliyor. İşte burada bir ön işlemci (preprocessor) yardımcı olur. Sass, CSS’te henüz bulunmayan özelliklere sahiptir: iç içe yazım (nesting), mixinler (mixins), kalıtım (inheritance) ve sağlam, bakımı kolay CSS yazmanıza yardımcı olacak diğer güzel özellikler.

Sass ile oynamaya başladığınızda, Sass dosyanız ön işlenmiş (preprocessed) bir Sass dosyası olarak alınır ve normal bir CSS dosyası şeklinde kaydedilir. Bu dosyayı web sitenizde kullanabilirsiniz.

---

## 💻 Terminal Üzerinden Derleme (compiling via terminal)

Bunu gerçekleştirmenin en doğrudan yolu terminalinizden geçer. Sass kurulduktan sonra `sass` komutunu kullanarak Sass’i CSS’e derleyebilirsiniz. Sass’e hangi dosyadan oluşturulacağını ve CSS’in nereye kaydedileceğini belirtmeniz gerekir. Örneğin, terminalinizde şu komutu çalıştırmak:

```
sass input.scss output.css
```

tek bir Sass dosyası olan `input.scss` dosyasını alır ve onu `output.css` olarak derler.
👉 Bu komut, tek bir Sass dosyasını CSS dosyasına dönüştürür.

---

## 👀 Dosyaları İzlemek (--watch bayrağı)

Bireysel dosyaları veya dizinleri `--watch` bayrağıyla da izleyebilirsiniz. `watch` bayrağı Sass’e kaynak dosyalarınızı değişiklikler için izlemesini ve her kaydettiğinizde CSS’i yeniden derlemesini söyler. Örneğin, `input.scss` dosyanızı manuel olarak derlemek yerine izlemek isterseniz, komutunuza `watch` bayrağını eklemeniz yeterlidir:

```
sass --watch input.scss output.css
```

👉 Bu komut, `input.scss` dosyası her kaydedildiğinde otomatik olarak `output.css` dosyasına yeniden derlenmesini sağlar.

Klasörleri izlemek ve çıktıyı klasörlere almak için giriş ve çıkış olarak klasör yollarını kullanabilir, bunları iki nokta (`:`) ile ayırabilirsiniz. Örneğin:

```
sass --watch app/sass:public/stylesheets
```

👉 Bu komut, `app/sass` klasöründeki tüm dosyaları değişikliklere karşı izler ve CSS’i `public/stylesheets` klasörüne derler.

---

## 💡 Eğlenceli Gerçek (fun fact)

Sass’in iki farklı sözdizimi (syntax) vardır!

* **SCSS (.scss)** sözdizimi en yaygın kullanılanıdır. Bu, CSS’in bir üst kümesidir; yani tüm geçerli CSS aynı zamanda geçerli SCSS’tir.
* **Girintili sözdizimi (.sass)** daha alışılmadık bir biçimdir: ifadeleri iç içe yerleştirmek için süslü parantezler yerine girinti kullanır ve ayırıcı olarak noktalı virgül yerine yeni satırlar kullanır.

Tüm örneklerimiz her iki sözdiziminde de mevcuttur.


## 🎨 Değişkenler (variables)

Değişkenleri (variables), stil sayfanız boyunca tekrar kullanmak istediğiniz bilgileri saklamanın bir yolu olarak düşünebilirsiniz. Renkler, yazı tipi kümeleri veya tekrar kullanmak istediğiniz herhangi bir CSS değerini saklayabilirsiniz. Sass, bir şeyi değişken yapmak için `$` sembolünü kullanır. İşte bir örnek:

```
$font-stack: Helvetica, sans-serif;
$primary-color: #333;

body {
  font: 100% $font-stack;
  color: $primary-color;
}
```

👉 Bu örnekte, `$font-stack` ve `$primary-color` için tanımladığımız değerler Sass tarafından işlenir ve normal CSS çıktısında değişken değerleri doğrudan CSS’e yerleştirilir. Bu, özellikle marka renkleriyle çalışırken ve bunları site genelinde tutarlı tutmak istediğinizde son derece güçlüdür.

---

## 🧩 İç İçe Yazım (nesting)

HTML yazarken açık bir hiyerarşi ve görsel iç içe yapıya sahip olduğunu fark etmişsinizdir. Ancak CSS’in böyle bir özelliği yoktur.

Sass, CSS seçicilerinizi (selectors) HTML’inizin görsel hiyerarşisine uygun şekilde iç içe yazmanıza olanak tanır. Ancak aşırı derecede iç içe kurallar yazmak fazla nitelikli CSS (over-qualified CSS) ile sonuçlanır ve bu durum bakımı zorlaştırabilir. Bu nedenle genellikle kötü bir uygulama olarak kabul edilir.

Bunu göz önünde bulundurarak, bir sitenin gezinme (navigation) stillerine dair tipik bir örnek şöyledir:

```
nav {
  ul {
    margin: 0;
    padding: 0;
    list-style: none;
  }

  li { display: inline-block; }

  a {
    display: block;
    padding: 6px 12px;
    text-decoration: none;
  }
}
```

👉 Burada `ul`, `li` ve `a` seçicilerinin `nav` seçicisinin içinde iç içe yazıldığını görebilirsiniz. Bu, CSS’inizi düzenlemenin ve okunabilirliğini artırmanın harika bir yoludur.

---

## 📂 Parçalar (partials)

Küçük CSS parçacıkları içeren kısmi (partial) Sass dosyaları oluşturabilirsiniz. Bu parçaları diğer Sass dosyalarına dahil edebilirsiniz. Bu yöntem, CSS’inizi modüler hale getirmenin ve bakımını kolaylaştırmanın harika bir yoludur.

Bir parça (partial), başında alt çizgi bulunan bir Sass dosyasıdır. Örneğin, `_partial.scss` olarak adlandırabilirsiniz. Alt çizgi Sass’e bunun sadece bir parça dosya olduğunu ve bağımsız bir CSS dosyası olarak üretilmemesi gerektiğini bildirir. Sass parçaları `@use` kuralı ile kullanılır.


## 📦 Modüller (modules)

Uyumluluk:

* Dart Sass: 1.23.0’dan itibaren
* LibSass: ✗
* Ruby Sass: ✗

Tüm Sass kodunuzu tek bir dosyada yazmak zorunda değilsiniz. `@use` kuralı ile istediğiniz gibi bölebilirsiniz. Bu kural, başka bir Sass dosyasını bir modül (module) olarak yükler, bu da Sass dosyanızda o dosyanın değişkenlerine (variables), mixinlerine (mixins) ve fonksiyonlarına (functions) dosya adına dayalı bir ad alanı (namespace) kullanarak erişebileceğiniz anlamına gelir. Bir dosyayı kullanmak, aynı zamanda o dosyanın ürettiği CSS’in derlenmiş çıktınıza dahil edilmesini de sağlar!

```scss
// _base.scss
$font-stack: Helvetica, sans-serif;
$primary-color: #333;

body {
  font: 100% $font-stack;
  color: $primary-color;
}

// styles.scss
@use 'base';

.inverse {
  background-color: base.$primary-color;
  color: white;
}
```

👉 Burada `styles.scss` dosyasında `@use 'base';` kullandığımıza dikkat edin. Bir dosya kullanırken uzantısını eklemenize gerek yoktur. Sass bunu otomatik olarak algılar.

---

## 🛠️ Mixinler (mixins)

CSS’te bazı şeyleri yazmak biraz zahmetli olabilir, özellikle de CSS3 ve mevcut birçok tarayıcı öneki (vendor prefixes) ile birlikte. Bir mixin, site genelinde tekrar kullanmak istediğiniz CSS bildirim grupları oluşturmanıza olanak tanır. Bu, Sass’i çok daha DRY (Don’t Repeat Yourself – Kendini Tekrar Etme) hale getirir. Ayrıca mixininizi daha esnek yapmak için içine değerler de geçebilirsiniz. İşte tema (theme) için bir örnek:

```scss
@mixin theme($theme: DarkGray) {
  background: $theme;
  box-shadow: 0 0 1px rgba($theme, .25);
  color: #fff;
}

.info {
  @include theme;
}
.alert {
  @include theme($theme: DarkRed);
}
.success {
  @include theme($theme: DarkGreen);
}
```

👉 Bir mixin oluşturmak için `@mixin` yönergesini kullanır ve ona bir isim verirsiniz. Burada mixinimize `theme` adını verdik. Ayrıca parantezler içinde `$theme` değişkenini kullanıyoruz, böylece istediğimiz temayı parametre olarak geçebiliriz. Mixini oluşturduktan sonra, `@include` ifadesi ile mixin adını yazarak CSS bildirimleri içinde kullanabilirsiniz.


## ♻️ Extend / Kalıtım (extend/inheritance)

`@extend` kullanmak, bir seçicideki (selector) CSS özelliklerini başka bir seçiciyle paylaşmanıza olanak tanır. Aşağıdaki örnekte hata (error), uyarı (warning) ve başarı (success) mesajları için basit bir yapı oluşturacağız. Burada `@extend` ile birlikte kullanılan bir diğer özellik de **placeholder sınıflarıdır (placeholder classes)**. Placeholder sınıfı, yalnızca genişletildiğinde (extended) çıktıya yazılan özel bir sınıftır ve derlenen CSS’inizi düzenli ve temiz tutmanıza yardımcı olur.

```scss
/* Bu CSS çıktıya yazılır çünkü %message-shared genişletilmiştir. */
%message-shared {
  border: 1px solid #ccc;
  padding: 10px;
  color: #333;
}

// Bu CSS çıktıya yazılmaz çünkü %equal-heights hiç genişletilmemiştir.
%equal-heights {
  display: flex;
  flex-wrap: wrap;
}

.message {
  @extend %message-shared;
}

.success {
  @extend %message-shared;
  border-color: green;
}

.error {
  @extend %message-shared;
  border-color: red;
}

.warning {
  @extend %message-shared;
  border-color: yellow;
}
```

👉 Bu kod, `.message`, `.success`, `.error` ve `.warning` sınıflarının `%message-shared` ile aynı şekilde davranmasını sağlar. Yani `%message-shared`’in göründüğü her yerde bu sınıflar da aynı özelliklere sahip olur. Büyü, derlenen CSS’te gerçekleşir: her sınıf `%message-shared`’de tanımlanan CSS özelliklerini alır. Bu sayede HTML öğelerinde birden fazla sınıf adı yazmak zorunda kalmazsınız.

Sass’te, placeholder sınıflarının yanı sıra çoğu basit CSS seçicisini de genişletebilirsiniz. Ancak, yanlışlıkla başka bir yerde iç içe yazılmış bir sınıfı genişletmemek için placeholder sınıflarını kullanmak en güvenli yöntemdir.

Not: `%equal-heights` içindeki CSS çıktıya yazılmadı, çünkü `%equal-heights` hiç genişletilmedi.

---

## ➗ Operatörler (operators)

CSS içinde matematik yapmak oldukça faydalıdır. Sass, birkaç standart matematiksel operatöre sahiptir: `+`, `-`, `*`, `math.div()`, `%`. Aşağıdaki örnekte, bir makale (article) ve yan alan (aside) için genişlik hesaplamak üzere basit matematik işlemleri yapıyoruz.

```scss
@use "sass:math";

.container {
  display: flex;
}

article[role="main"] {
  width: math.div(600px, 960px) * 100%;
}

aside[role="complementary"] {
  width: math.div(300px, 960px) * 100%;
  margin-left: auto;
}
```

👉 Burada 960px tabanlı çok basit bir akışkan (fluid) grid oluşturduk. Sass’teki matematik işlemleri, piksel değerlerini kolayca yüzde değerlerine dönüştürmenize olanak tanır.

