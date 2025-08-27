## 🔀 Enterpolasyon (Interpolation)

Enterpolasyon (interpolation), bir Sass stil sayfasında (stylesheet) neredeyse her yerde kullanılabilir. Bir SassScript ifadesinin sonucunu CSS içine gömmek için ifadeyi `#{}` içine almanız yeterlidir. Aşağıdaki yerlerde kullanılabilir:

* Stil kurallarındaki seçiciler (selectors)
* Bildirimlerdeki özellik adları (property names)
* Özel özellik değerleri (custom property values)
* CSS at-rules
* `@extend`
* Düz CSS `@import` ifadeleri
* Tırnaklı veya tırnaksız stringler
* Özel fonksiyonlar (special functions)
* Düz CSS fonksiyon adları
* Yüksek sesli yorumlar (loud comments)

```scss
@mixin corner-icon($name, $top-or-bottom, $left-or-right) {
  .icon-#{$name} {
    background-image: url("/icons/#{$name}.svg");
    position: absolute;
    #{$top-or-bottom}: 0;
    #{$left-or-right}: 0;
  }
}

@include corner-icon("mail", top, left);
```

👉 Bu örnekte `#{$name}` enterpolasyonu kullanılarak sınıf adı ve dosya yolu dinamik oluşturulmaktadır.

---

## 🧩 SassScript İçinde (In SassScript)

Uyumluluk (Modern Söz Dizimi):

* Dart Sass: ✓
* LibSass: ✗
* Ruby Sass: 4.0.0’dan itibaren (yayınlanmamış)

Enterpolasyon, SassScript içinde tırnaksız stringlere SassScript eklemek için kullanılabilir. Bu, özellikle dinamik adlar (örneğin animasyonlar için) üretirken veya `/` ile ayrılmış değerler kullanırken faydalıdır. Enterpolasyon SassScript içinde her zaman tırnaksız string döndürür.

```scss
@mixin inline-animation($duration) {
  $name: inline-#{unique-id()};

  @keyframes #{$name} {
    @content;
  }

  animation-name: $name;
  animation-duration: $duration;
  animation-iteration-count: infinite;
}

.pulse {
  @include inline-animation(2s) {
    from { background-color: yellow }
    to { background-color: red }
  }
}
```

👉 Bu örnekte `unique-id()` ile her seferinde benzersiz bir animasyon adı oluşturulmaktadır.

💡 İlginç bilgi:
Enterpolasyon, string içine değer gömmek için faydalıdır, ancak SassScript ifadelerinde çoğunlukla gerek yoktur. Örneğin `color: #{$accent}` yerine `color: $accent` yazmak yeterlidir.

⚠️ Dikkat!
Sayılarla enterpolasyon kullanmak neredeyse her zaman kötü bir fikirdir. Enterpolasyon tırnaksız string döndürür, bu da matematiksel işlemlerde kullanılamaz ve Sass’ın birim kontrolünü devre dışı bırakır.

Bunun yerine Sass’ın güçlü birim aritmetiğini kullanın. Örneğin `#{$width}px` yerine `$width * 1px` yazın — ya da daha iyisi `$width` değişkenini doğrudan `px` cinsinden tanımlayın.

---

## 📝 Tırnaklı Stringler (Quoted Strings)

Çoğu durumda enterpolasyon, ifadenin bir özellik değeri olarak kullanılması durumunda ortaya çıkacak metni aynen enjekte eder. Ancak bir istisna vardır: **tırnaklı stringlerin tırnak işaretleri kaldırılır** (bu stringler listelerde olsa bile).

Bu özellik, SassScript içinde kullanılamayan sözdizimini (örneğin seçiciler) içeren tırnaklı stringler yazmayı ve bunları stil kurallarına enterpole etmeyi mümkün kılar.

```scss
.example {
  unquoted: #{"string"};
}
```

👉 Bu örnekte `"string"` ifadesi tırnaksız hale dönüştürülmüştür.

⚠️ Dikkat!
Bu özelliği, tırnaklı stringleri tırnaksız stringlere dönüştürmek için kullanmak cazip gelebilir. Ancak bunu yapmak için `string.unquote()` fonksiyonunu kullanmak çok daha açık ve net olur.
`#{$string}` yerine `string.unquote($string)` yazın!
