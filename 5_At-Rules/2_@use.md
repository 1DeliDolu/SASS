## 📥 @use

Uyumluluk:

* Dart Sass: 1.23.0’dan itibaren
* LibSass: ✗
* Ruby Sass: ✗

`@use` kuralı, diğer Sass stil sayfalarından (stylesheets) mixinler, fonksiyonlar ve değişkenler yükler ve birden fazla stil sayfasındaki CSS’i birleştirir. `@use` ile yüklenen stil sayfalarına **modül (module)** denir. Sass ayrıca birçok yararlı fonksiyon içeren yerleşik (built-in) modüller de sağlar.

En basit `@use` kuralı `@use "<url>"` şeklindedir. Bu, verilen URL’deki modülü yükler. Bu şekilde yüklenen stiller, kaç defa yüklenirse yüklensin, derlenmiş CSS çıktısına yalnızca bir kez eklenir.

⚠️ Dikkat!
Bir stil sayfasındaki `@use` kuralları, `@forward` dışındaki tüm kurallardan önce gelmelidir (stil kuralları dahil). Ancak, modülleri yapılandırmak için `@use` kurallarından önce değişkenler tanımlayabilirsiniz.

```scss
// foundation/_code.scss
code {
  padding: .25em;
  line-height: 0;
}
// foundation/_lists.scss
ul, ol {
  text-align: left;

  & & {
    padding: {
      bottom: 0;
      left: 0;
    }
  }
}
// style.scss
@use 'foundation/code';
@use 'foundation/lists';
```

👉 Burada iki ayrı stil sayfası (`_code.scss` ve `_lists.scss`) `@use` ile yüklenmiştir.

---

## 📦 Üyeleri Yükleme (Loading Members)

Başka bir modülden değişkenlere, fonksiyonlara ve mixinlere şu şekilde erişebilirsiniz:

* `namespace.variable`
* `namespace.function()`
* `@include namespace.mixin()`

Varsayılan olarak namespace, modül URL’sinin son bileşenidir.

`@use` ile yüklenen üyeler (değişkenler, fonksiyonlar, mixinler) yalnızca onları yükleyen stil sayfasında görünür. Başka stil sayfaları da bu üyelere erişmek istiyorsa kendi `@use` kurallarını yazmalıdır. Bu, her bir üyenin nereden geldiğini açıkça görmeyi kolaylaştırır.

Birçok dosyadaki üyeleri tek bir yerden yüklemek isterseniz `@forward` kuralını kullanabilirsiniz.

💡 İlginç bilgi:
`@use` üyelerin adlarına namespace eklediği için `$radius` veya `$width` gibi çok basit isimler seçmek güvenlidir. Bu, eski `@import` kuralından farklıdır. `@import` kuralı çakışmaları önlemek için `$mat-corner-radius` gibi uzun isimler yazmaya teşvik ederdi. `@use` sayesinde stilleriniz daha net ve okunabilir kalır.

```scss
// src/_corners.scss
$radius: 3px;

@mixin rounded {
  border-radius: $radius;
}
// style.scss
@use "src/corners";

.button {
  @include corners.rounded;
  padding: 5px + corners.$radius;
}
```

👉 Burada `corners` modülü yüklenmiş ve hem değişken hem mixin namespace üzerinden çağrılmıştır.

---

## 🏷️ Namespace Seçme (Choosing a Namespace)

Varsayılan olarak bir modülün namespace’i, dosya uzantısı olmadan URL’nin son bileşenidir. Ancak bazen farklı bir namespace seçmek isteyebilirsiniz. Örneğin:

* Sıkça kullandığınız bir modül için daha kısa bir isim tercih edebilirsiniz.
* Aynı dosya adına sahip birden fazla modül yüklüyor olabilirsiniz.

Bunu `@use "<url>" as <namespace>` şeklinde yapabilirsiniz.

```scss
// src/_corners.scss
$radius: 3px;

@mixin rounded {
  border-radius: $radius;
}
// style.scss
@use "src/corners" as c;

.button {
  @include c.rounded;
  padding: 5px + c.$radius;
}
```

👉 Burada `corners` modülü `c` namespace’i ile yüklenmiştir.

Bir modülü namespace olmadan yüklemek için `@use "<url>" as *` yazabilirsiniz. Ancak bunu sadece kendi yazdığınız stil sayfalarında yapmanız önerilir; aksi halde ad çakışmalarına yol açabilecek yeni üyeler eklenebilir.

```scss
// src/_corners.scss
$radius: 3px;

@mixin rounded {
  border-radius: $radius;
}
// style.scss
@use "src/corners" as *;

.button {
  @include rounded;
  padding: 5px + $radius;
}
```

👉 Bu örnekte namespace kullanılmamış ve üyeler doğrudan çağrılmıştır.

---

## 🔒 Özel Üyeler (Private Members)

Bir stil sayfası yazarı olarak tanımladığınız tüm üyelerin (değişken, fonksiyon, mixin) dışarıya açık olmasını istemeyebilirsiniz. Sass, özel üye tanımlamayı kolaylaştırır: Bir üyenin adını `-` veya `_` ile başlatın.

Bu üyeler tanımlandıkları stil sayfasında normal şekilde çalışır, ancak modülün **genel API’sinin** bir parçası olmaz. Yani modülünüzü `@use` ile yükleyen stil sayfaları bu üyelere erişemez!

💡 İlginç bilgi:
Bir üyeyi yalnızca tek bir modüle değil, tüm pakete özel yapmak istiyorsanız, o modülü paketinizin giriş noktalarından (`entrypoints`) hiç `@forward` etmeyin. Böylece kullanıcılar o üyeyi göremez, fakat modülün geri kalanını yine aktarabilirsiniz.

```scss
// src/_corners.scss
$-radius: 3px;

@mixin rounded {
  border-radius: $-radius;
}
// style.scss
@use "src/corners";

.button {
  @include corners.rounded;

  // Bu bir hatadır! $-radius `_corners.scss` dışında görünmez.
  padding: 5px + corners.$-radius;
}
```

👉 `$-radius` özel bir değişkendir ve dışarıdan erişilemez.


## ⚙️ Yapılandırma (Configuration)

Bir stil sayfası, `!default` bayrağı ile tanımlanmış değişkenleri yapılandırılabilir hale getirebilir. Bir modülü yapılandırarak yüklemek için şu şekilde yazılır:

`@use <url> with (<variable>: <value>, <variable>: <value>)`

Bu şekilde verilen değerler, değişkenlerin varsayılan değerlerini geçersiz kılar.

```scss
// _library.scss
$black: #000 !default;
$border-radius: 0.25rem !default;
$box-shadow: 0 0.5rem 1rem rgba($black, 0.15) !default;

code {
  border-radius: $border-radius;
  box-shadow: $box-shadow;
}
// style.scss
@use 'library' with (
  $black: #222,
  $border-radius: 0.1rem
);
```

👉 Burada `library` modülü varsayılan değerler yerine özel değerlerle yüklenmiştir.

Bir modül, kaç defa yüklenirse yüklensin aynı yapılandırmayı (veya yapılandırma olmamasını) korur. Bu yüzden `@use ... with` yalnızca bir modül için bir kez, ilk yüklemede kullanılabilir. Bu, tüm derleme boyunca geçerli olacak “tema” yapıları oluşturmayı da mümkün kılar.

```scss
// components/_button.scss
@use "../theme";

button {
  color: theme.$text-color;
  background-color: theme.$background-color;
}
// _theme.scss
$text-color: black !default;
$background-color: white !default;
// dark.scss
@use "theme" with (
  $text-color: white,
  $background-color: black,
);

@use "components/button";
// Daha fazla bileşen genelde burada yüklenir.
```

👉 Bu örnekte tema yapılandırması ile farklı stil temaları oluşturulmuştur.

---

## 🧩 Mixinlerle Kullanım (With Mixins)

`@use ... with` ile modül yapılandırmak kullanışlıdır, özellikle `@import` için yazılmış kütüphanelerde. Ancak çok esnek değildir. Eğer birden fazla değişkeni aynı anda yapılandırmak, haritalar (maps) ile değer geçmek veya modül yüklendikten sonra yapılandırmayı güncellemek istiyorsanız, bunun yerine mixinler yazmanız önerilir.

```scss
// _library.scss
$-black: #000;
$-border-radius: 0.25rem;
$-box-shadow: null;

/// Kullanıcı `$-box-shadow` yapılandırdıysa onu döndürür.
/// Aksi halde `$-black`'ten türetilmiş bir değer döner.
@function -box-shadow() {
  @return $-box-shadow or (0 0.5rem 1rem rgba($-black, 0.15));
}

@mixin configure($black: null, $border-radius: null, $box-shadow: null) {
  @if $black {
    $-black: $black !global;
  }
  @if $border-radius {
    $-border-radius: $border-radius !global;
  }
  @if $box-shadow {
    $-box-shadow: $box-shadow !global;
  }
}

@mixin styles {
  code {
    border-radius: $-border-radius;
    box-shadow: -box-shadow();
  }
}
// style.scss
@use 'library';

@include library.configure(
  $black: #222,
  $border-radius: 0.1rem
);

@include library.styles;
```

👉 Burada mixinler ile yapılandırma daha esnek hale getirilmiştir.

---

## 🔄 Değişkenleri Yeniden Atama (Reassigning Variables)

Bir modül yüklendikten sonra, onun değişkenlerine yeniden değer atanabilir.

```scss
// _library.scss
$color: red;
// _override.scss
@use 'library';
library.$color: blue;
// style.scss
@use 'library';
@use 'override';
@debug library.$color;  //=> blue
```

👉 `library.$color` sonradan `blue` değerine atanmıştır.

⚠️ Dikkat!
Yerleşik modül değişkenleri (örneğin `math.$pi`) yeniden atanamaz.

---

## 📍 Modülü Bulma (Finding the Module)

Her stil sayfası için tam URL yazmak zahmetli olacağından Sass, modül bulmayı kolaylaştıran bir algoritma kullanır:

* Dosya uzantısını yazmak zorunda değilsiniz. `@use "variables"` ifadesi otomatik olarak `variables.scss`, `variables.sass` veya `variables.css` dosyalarından birini yükler.
* Sass, dosyaları dosya yoluna göre değil URL’ye göre yükler. Bu nedenle Windows’ta bile ters eğik çizgi (`\`) yerine ileri eğik çizgi (`/`) kullanılmalıdır.
* URL’ler büyük-küçük harfe duyarlıdır. `Styles.scss` ve `styles.scss` farklı modüller sayılır.

---

## 📂 Yükleme Yolları (Load Paths)

Tüm Sass implementasyonları, kullanıcıların yükleme yolları (load paths) tanımlamasına izin verir. Sass, modül ararken bu yolları da kontrol eder.

Örneğin `node_modules/susy/sass` yükleme yolu olarak verilirse, `@use "susy"` ifadesi `node_modules/susy/sass/susy.scss` dosyasını yükler.

Ancak her zaman önce mevcut dosyaya göre göreli yol aranır. Yükleme yolları yalnızca uygun bir göreli dosya bulunmazsa kullanılır.

💡 İlginç bilgi:
Bazı dillerin aksine Sass’ta göreli importlar için `./` yazmak zorunda değilsiniz. Göreli importlar her zaman kullanılabilir.

---

## 📑 Parçalar (Partials)

Bir modül olarak yüklenmesi amaçlanan, ancak tek başına derlenmemesi gereken Sass dosyaları `_` ile başlar (ör. `_code.scss`). Bunlara **parçalar (partials)** denir.

`@use` ile yüklenirken `_` işareti yazılmak zorunda değildir.

---

## 📦 Dizin Dosyaları (Index Files)

Bir klasöre `_index.scss` veya `_index.sass` yazarsanız, bu dosya klasörün kendisi yüklendiğinde otomatik olarak yüklenir.

```scss
// foundation/_code.scss
code {
  padding: .25em;
  line-height: 0;
}
// foundation/_lists.scss
ul, ol {
  text-align: left;

  & & {
    padding: {
      bottom: 0;
      left: 0;
    }
  }
}
// foundation/_index.scss
@use 'code';
@use 'lists';
// style.scss
@use 'foundation';
```

👉 Burada `foundation` klasörü `@use 'foundation'` ile tek seferde yüklenmiştir.

---

## 📦 pkg: URL’ler (pkg: URLs)

Sass, çeşitli paket yöneticileri tarafından dağıtılan stil sayfalarını yüklemek için `pkg:` URL şemasını kullanır.

`pkg:` URL’ler sayesinde stil sayfaları farklı dil ekosistemleri arasında taşınabilir hale gelir. Örneğin, `@use 'pkg:library'` yazdığınızda, ilgili paket yöneticisinin mantığına göre doğru dosya yüklenir.

💡 İlginç bilgi:
`pkg:` URL’leri sadece `@use` için değil, `@forward`, `meta.load-css()` ve hatta eski `@import` kuralında da kullanılabilir.

---

## 🟢 Node.js Paket Yükleyici (Node.js Package Importer)

Uyumluluk:

* Dart Sass: 1.71.0’dan itibaren
* LibSass: ✗
* Ruby Sass: ✗

Sass en çok Node.js ekosisteminde kullanıldığından, Node.js’in aynı algoritmasını kullanan yerleşik bir `pkg:` yükleyici vardır. Bu varsayılan olarak etkin değildir, ancak kolayca açılabilir:

* JavaScript API kullanıyorsanız: `new NodePackageImporter()` ekleyin.
* Dart API kullanıyorsanız: `NodePackageImporter()` ekleyin.
* Komut satırında: `--pkg-importer=node` parametresini geçin.

Node.js `pkg:` yükleyici, paketlerin `package.json` dosyasındaki şu alanları sırayla kontrol eder:

1. `"exports"` alanı (`"sass"`, `"style"`, `"default"` koşulları ile).
2. `"sass"` veya `"style"` alanı (bir Sass dosyası yolu).
3. Paket kökündeki index dosyası.

```json
{
  "exports": {
    ".": {
      "sass": "styles/index.scss"
    },
    "./button.scss": {
      "sass": "styles/button.scss"
    },
    "./accordion.scss": {
      "sass": "styles/accordion.scss"
    }
  }
}
```

Veya desenlerle:

```json
{
  "exports": {
    ".": {
      "sass": "styles/index.scss"
    },
    "./*.scss": {
      "sass": "styles/*.scss"
    }
  }
}
```

---

## 🎨 CSS Yükleme (Loading CSS)

Sass, `.sass` ve `.scss` dosyalarının yanı sıra normal `.css` dosyalarını da yükleyebilir.

```scss
// code.css
code {
  padding: .25em;
  line-height: 0;
}
// style.scss
@use 'code';
```

👉 CSS dosyaları modül olarak yüklendiğinde Sass özellikleri kullanılamaz, yalnızca saf CSS işlenir.

---

## 🔄 @import ile Farkları (Differences From @import)

`@use` kuralı, eski `@import` kuralının yerini almak için tasarlanmıştır, ancak bilerek farklı çalışır. Önemli farklar:

* `@use`, değişkenleri, fonksiyonları ve mixinleri sadece **geçerli dosya kapsamında** kullanılabilir hale getirir, global alana eklemez.
* `@use`, her dosyayı yalnızca **bir kez** yükler.
* `@use`, dosyanızın başında yer almalıdır, stil kuralları içine gömülemez.
* Her `@use` kuralı yalnızca **tek bir URL** içerebilir.
* `@use` URL’si her zaman tırnak içinde yazılmalıdır (girintili sözdizimde bile).
