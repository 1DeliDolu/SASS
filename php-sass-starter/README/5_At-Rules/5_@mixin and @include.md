## 🛠️ @mixin ve @include

Mixinler (mixins), stil sayfanız boyunca tekrar kullanılabilecek stilleri tanımlamanıza olanak tanır. Bu sayede `.float-left` gibi anlamsız sınıflar kullanmaktan kaçınmak ve stiller koleksiyonlarını kütüphaneler içinde dağıtmak kolaylaşır.

Mixinler `@mixin` kuralı ile tanımlanır: `@mixin <isim> { ... }` veya `@mixin isim(<argümanlar...>) { ... }`. Bir mixin’in adı `--` ile başlamayan herhangi bir Sass tanımlayıcısı olabilir ve en üst seviye ifadeler dışındaki tüm ifadeleri içerebilir. Bir mixin, tek bir stil kuralına eklenebilecek stilleri kapsüllemek, kendi iç stil kurallarını içerebilir, diğer kurallar içinde iç içe kullanılabilir veya sadece değişkenleri değiştirmek için kullanılabilir.

Mixinler mevcut bağlama `@include` kuralı ile eklenir: `@include <isim>` veya `@include <isim>(<argümanlar...>)`.

```scss
@mixin reset-list {
  margin: 0;
  padding: 0;
  list-style: none;
}

@mixin horizontal-list {
  @include reset-list;

  li {
    display: inline-block;
    margin: {
      left: -2px;
      right: 2em;
    }
  }
}

nav ul {
  @include horizontal-list;
}
```

👉 Bu örnek, `reset-list` mixin’ini başka bir mixin veya stil kuralı içinde nasıl kullanabileceğinizi gösterir.

💡 İlginç bilgi: Mixin adları, tüm Sass tanımlayıcıları gibi tire (`-`) ve alt çizgi (`_`) karakterlerini aynı kabul eder. Yani `reset-list` ve `reset_list` aynı mixin’i ifade eder.

---

## ⚙️ Argümanlar (Arguments)

Mixinler, her çağrıldıklarında davranışlarının özelleştirilmesini sağlayan argümanlar alabilir. Argümanlar mixin adından sonra parantez içinde yazılır. Mixin bu argümanlarla çağrıldığında, verilen değerler mixin gövdesi içinde ilgili değişkenler olarak kullanılabilir.

```scss
@mixin rtl($property, $ltr-value, $rtl-value) {
  #{$property}: $ltr-value;

  [dir=rtl] & {
    #{$property}: $rtl-value;
  }
}

.sidebar {
  @include rtl(float, left, right);
}
```

👉 Bu örnek, `rtl` mixin’ine argüman geçirilerek stilin dinamik olarak yön değiştirmesini sağlar.

---

## 📝 Opsiyonel Argümanlar (Optional Arguments)

Normalde, bir mixin’in tanımladığı tüm argümanlar çağrılırken verilmelidir. Ancak, bir argümana varsayılan değer tanımlayarak onu opsiyonel hale getirebilirsiniz.

```scss
@mixin replace-text($image, $x: 50%, $y: 50%) {
  text-indent: -99999em;
  overflow: hidden;
  text-align: left;

  background: {
    image: $image;
    repeat: no-repeat;
    position: $x $y;
  }
}

.mail-icon {
  @include replace-text(url("/images/mail.svg"), 0);
}
```

👉 Burada `$x` ve `$y` argümanları verilmezse varsayılan değerleri (%50) kullanılır.

---

## 🏷️ Anahtar Argümanlar (Keyword Arguments)

Mixinler çağrıldığında argümanlar, yalnızca konumlarıyla değil adlarıyla da geçirilebilir. Bu özellikle opsiyonel veya mantıksal (boolean) argümanlarda faydalıdır.

```scss
@mixin square($size, $radius: 0) {
  width: $size;
  height: $size;

  @if $radius != 0 {
    border-radius: $radius;
  }
}

.avatar {
  @include square(100px, $radius: 4px);
}
```

👉 Burada `$radius` anahtar argümanı kullanılarak mixin çağrılmıştır.

⚠️ Dikkat: Argüman adlarını değiştirmek kullanıcılarınızın kodunu bozabilir.

---

## 📦 İsteğe Bağlı Argüman Listeleri (Arbitrary Arguments)

Bazı mixin’lerin herhangi bir sayıda argüman alması gerekebilir. Eğer `@mixin` tanımındaki son argüman `...` ile biterse, eklenen tüm argümanlar liste olarak o değişkene atanır.

```scss
@mixin order($height, $selectors...) {
  @for $i from 0 to length($selectors) {
    #{nth($selectors, $i + 1)} {
      position: absolute;
      height: $height;
      margin-top: $i * $height;
    }
  }
}

@include order(150px, "input.name", "input.address", "input.zip");
```

👉 Bu örnek, dinamik olarak birçok seçiciye aynı yüksekliği uygular.

---

## 🗝️ Keyfi Anahtar Argümanlar (Arbitrary Keyword Arguments)

Bir mixin, `meta.keywords()` fonksiyonu kullanılarak istenilen sayıda anahtar argümanı kabul edebilir.

```scss
@use "sass:meta";

@mixin syntax-colors($args...) {
  @debug meta.keywords($args);
  // (string: #080, comment: #800, variable: #60b)

  @each $name, $color in meta.keywords($args) {
    pre span.stx-#{$name} {
      color: $color;
    }
  }
}

@include syntax-colors(
  $string: #080,
  $comment: #800,
  $variable: #60b,
)
```

👉 Burada mixin’e farklı türde anahtar argümanlar geçirilmiştir.

---

## 🔄 Keyfi Argümanların Geçirilmesi (Passing Arbitrary Arguments)

Liste (`...`) veya harita (`...`) ile keyfi argümanlar başka mixin’lere iletilebilir.

```scss
$form-selectors: "input.name", "input.address", "input.zip" !default;

@include order(150px, $form-selectors...);
```

👉 `$form-selectors` listesindeki tüm değerler mixin’e aktarılır.

---

## 📑 İçerik Blokları (Content Blocks)

Mixin’ler ayrıca bir stil bloğu da alabilir. Bu blok, mixin içinde `@content` ile çağrılır.

```scss
@mixin hover {
  &:not([disabled]):hover {
    @content;
  }
}

.button {
  border: 1px solid black;
  @include hover {
    border-width: 2px;
  }
}
```

👉 Burada `@content`, mixin’e geçirilen stil bloğunu temsil eder.

---

## 📤 İçerik Bloklarına Argüman Geçirme (Passing Arguments to Content Blocks)

Bir mixin, içerik bloğuna da argüman geçebilir.

```scss
@mixin media($types...) {
  @each $type in $types {
    @media #{$type} {
      @content($type);
    }
  }
}

@include media(screen, print) using ($type) {
  h1 {
    font-size: 40px;
    @if $type == print {
      font-family: Calluna;
    }
  }
}
```

👉 Bu örnekte içerik bloğu `media` mixin’inden `$type` argümanını alır.

---

## 📜 Girintili Mixin Sözdizimi (Indented Mixin Syntax)

Girintili sözdiziminde mixin’ler `=` ile tanımlanır ve `+` ile çağrılır.

```sass
=reset-list
  margin: 0
  padding: 0
  list-style: none

=horizontal-list
  +reset-list

  li
    display: inline-block
    margin:
      left: -2px
      right: 2em

nav ul
  +horizontal-list
```

👉 Bu sözdizimi daha kısa ama okunması zordur, genelde önerilmez.
