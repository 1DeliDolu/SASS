## 🎨 Özellik Bildirimleri (property declarations)

Sass’te, CSS’de olduğu gibi, özellik bildirimleri (property declarations) bir seçiciye uyan elementlerin nasıl stilleneceğini tanımlar. Ancak Sass, bunları yazmayı kolaylaştırmak ve otomatikleştirmek için ek özellikler sunar. Her şeyden önce, bir bildirimin değeri herhangi bir SassScript ifadesi olabilir; bu ifade değerlendirilir ve sonuçta çıktıya dahil edilir.

### SCSS Sözdizimi

```scss
.circle {
  $size: 100px;
  width: $size;
  height: $size;
  border-radius: $size * 0.5;
}
```

👉 Bu örnek, değişkenlerle özellik bildirimlerini tanımlar.

---

## 🔀 Aradeğer Kullanımı (interpolation)

Bir özelliğin adı aradeğer (interpolation) içerebilir, bu da gerektiğinde dinamik olarak özellik üretmeyi mümkün kılar. Hatta tüm özellik adını aradeğer ile yazabilirsiniz!

### SCSS Sözdizimi

```scss
@mixin prefix($property, $value, $prefixes) {
  @each $prefix in $prefixes {
    -#{$prefix}-#{$property}: $value;
  }
  #{$property}: $value;
}

.gray {
  @include prefix(filter, grayscale(50%), moz webkit);
}
```

👉 Bu örnek, tarayıcı ön ekleriyle (`-moz-`, `-webkit-`) dinamik özellikler üretir.

---

## 🪆 İç İçe Yazım (nesting)

Birçok CSS özelliği aynı önekle başlar ve bir tür ad alanı (namespace) gibi davranır. Örneğin, `font-family`, `font-size` ve `font-weight` hepsi `font-` ile başlar. Sass, özellik bildirimlerinin iç içe yazılmasına izin vererek bunu daha kolay ve daha az tekrarlı hale getirir. Dış özellik adları içtekine eklenir ve tire ile ayrılır.

### SCSS Sözdizimi

```scss
.enlarge {
  font-size: 14px;
  transition: {
    property: font-size;
    duration: 4s;
    delay: 2s;
  }

  &:hover { font-size: 36px; }
}
```

👉 Bu yapı, `transition` özelliklerini iç içe tanımlar.

Bazı CSS özelliklerinin, ad alanını (namespace) özellik adı olarak kullanan kısayol sürümleri vardır. Bunlar için hem kısayol değeri hem de daha açık iç içe sürümlerini yazabilirsiniz.

### SCSS Sözdizimi

```scss
.info-page {
  margin: auto {
    bottom: 10px;
    top: 2px;
  }
}
```

👉 Bu örnek, `margin` özelliğinin kısayol ve iç içe tanımlamasını gösterir.

---

## 🙈 Gizli Bildirimler (hidden declarations)

Bazen bir özellik bildiriminin yalnızca belirli durumlarda görünmesini isteyebilirsiniz. Eğer bir bildirimin değeri `null` veya boş tırnaksız bir string ise, Sass o bildirimi hiç CSS’e dönüştürmez.

### SCSS Sözdizimi

```scss
$rounded-corners: false;

.button {
  border: 1px solid black;
  border-radius: if($rounded-corners, 5px, null);
}
```

👉 Bu örnekte, `border-radius` yalnızca değişken `true` olduğunda uygulanır.

---

## ⚙️ Özel Özellikler (custom properties)

Uyumluluk (SassScript Sözdizimi):

* Dart Sass: ✓
* LibSass: 3.5.0’dan itibaren
* Ruby Sass: 3.5.0’dan itibaren

CSS özel özellikleri (custom properties), diğer adıyla CSS değişkenleri (CSS variables), alışılmadık bir bildirim sözdizimine sahiptir: bildirim değerlerinde neredeyse her türlü metne izin verirler. Ayrıca bu değerlere JavaScript tarafından erişilebilir, bu nedenle herhangi bir değer kullanıcı açısından geçerli olabilir. Buna normalde SassScript olarak ayrıştırılacak değerler de dahildir.

Bu nedenle, Sass özel özellik bildirimlerini diğer özelliklerden farklı şekilde ayrıştırır. SassScript gibi görünenler de dahil olmak üzere tüm belirteçler (tokens) CSS’e olduğu gibi aktarılır. Tek istisna aradeğer (interpolation)’dir; dinamik değerleri özel bir özelliğe enjekte etmenin tek yolu budur.

### SCSS Sözdizimi

```scss
$primary: #81899b;
$accent: #302e24;
$warn: #dfa612;

:root {
  --primary: #{$primary};
  --accent: #{$accent};
  --warn: #{$warn};

  // Bu Sass değişkenine benziyor ama geçerli CSS, bu yüzden değerlendirilmez.
  --consumed-by-js: $primary;
}
```

👉 Bu örnek, Sass değişkenlerini özel CSS değişkenlerine dönüştürür.

⚠️ Dikkat!
Aradeğer, stringlerden tırnakları kaldırır. Bu, Sass değişkenlerinden gelen tırnaklı stringlerin özel özelliklerde değer olarak kullanılmasını zorlaştırır. Çözüm olarak, `meta.inspect()` fonksiyonunu kullanarak tırnakları koruyabilirsiniz.

### SCSS Sözdizimi

```scss
@use "sass:meta";

$font-family-sans-serif: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto;
$font-family-monospace: SFMono-Regular, Menlo, Monaco, Consolas;

:root {
  --font-family-sans-serif: #{meta.inspect($font-family-sans-serif)};
  --font-family-monospace: #{meta.inspect($font-family-monospace)};
}
```

👉 Bu örnek, `meta.inspect()` kullanarak font ailesi değişkenlerini özel CSS değişkenlerine güvenli şekilde aktarır.
