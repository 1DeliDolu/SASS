## 📋 Listeler (Lists)

Listeler, diğer değerlerin ardışık bir dizisidir. Sass’ta listelerin elemanları:

* **Virgül** ile (`Helvetica, Arial, sans-serif`)
* **Boşluk** ile (`10px 15px 0 0`)
* **Eğik çizgi** ile ayrılabilir.

Sass’ta listeler özel parantez gerektirmez; boşluk veya virgülle ayrılmış her ifade bir liste olarak kabul edilir.
İsteğe bağlı olarak **köşeli parantez** `[line1 line2]` kullanılabilir (özellikle `grid-template-columns` için yararlıdır).

Listeyi daha karmaşık hale getirmek için **parantezler** kullanılır. Örneğin:

* `(1, 2), (3, 4)` → İki liste içerir, her biri iki sayıdan oluşur.

* `adjust-font-stack((Helvetica, Arial, sans-serif))` → Tek argüman olarak üç yazı tipi içeren liste geçirilir.

* Tek elemanlı liste: `(<expression>,)` veya `[<expression>]`

* Sıfır elemanlı liste: `()` veya `[]`

* Liste içinde olmayan tek bir değer, Sass fonksiyonları tarafından listeymiş gibi işlenir.

⚠️ Dikkat!
Köşeli parantezsiz boş listeler geçerli CSS değildir, bu yüzden özellik değerinde kullanılamazlar.

---

## ➗ Eğik Çizgi ile Ayrılmış Listeler (Slash-Separated Lists)

Sass’ta listeler eğik çizgi (`/`) ile de ayrılabilir. Bu yazım genellikle:

* `font: 12px/30px` → `font-size` ve `line-height` kısayolu
* `hsl(80 100% 50% / 0.5)` → opaklık içeren renk

⚠️ Ancak, doğrudan `/` kullanılamaz çünkü Sass’ta `/` bölme (division) için tarihsel olarak kullanılmıştır.
Bu yüzden slash listeleri sadece `list.slash()` ile oluşturulabilir.

---

## 🛠️ Listeleri Kullanmak

Sass, listelerle çalışmak için birçok fonksiyon sağlar:

### 🔢 İndeksler (Indexes)

Listelerde indeksler `1`’den başlar (çoğu dilde `0`’dan başlar).
Negatif indeksler sondan başa sayılır:

* `-1` → son eleman
* `-2` → sondan ikinci

---

### 🔎 Eleman Erişimi (Access an Element)

`list.nth($list, $n)` → listedeki belirtilen indeks değerini döner.

```scss
@use 'sass:list';

@debug list.nth(10px 12px 16px, 2); // 12px
@debug list.nth([line1, line2, line3], -1); // line3
```

---

### 🔁 Her Eleman için İşlem (Do Something for Every Element)

`@each` → listedeki her eleman için blok çalıştırır.

```scss
$sizes: 40px, 50px, 80px;

@each $size in $sizes {
  .icon-#{$size} {
    font-size: $size;
    height: $size;
    width: $size;
  }
}
```

---

### ➕ Listeye Eleman Ekleme (Add to a List)

`list.append($list, $val)` → listeye yeni eleman ekler.
Orijinal liste değişmez (immutable).

```scss
@debug append(10px 12px 16px, 25px); // 10px 12px 16px 25px
@debug append([col1-line1], col1-line2); // [col1-line1, col1-line2]
```

---

### 🔍 Liste İçinde Eleman Bulma (Find an Element in a List)

`list.index($list, $value)` → değerin listedeki indeksini döner.
Yoksa `null` döner.

```scss
@use 'sass:list';

@debug list.index(1px solid red, 1px); // 1
@debug list.index(1px solid red, solid); // 2
@debug list.index(1px solid red, dashed); // null
```

`null` falsey olduğu için `@if` ile birlikte kullanılabilir.

```scss
@use "sass:list";

$valid-sides: top, bottom, left, right;

@mixin attach($side) {
  @if not list.index($valid-sides, $side) {
    @error "#{$side} is not a valid side. Expected one of #{$valid-sides}.";
  }

  // ...
}
```

---

## 🔒 Değişmezlik (Immutability)

Listeler değişmezdir, yani içerikleri değiştirilemez.
Liste fonksiyonları her zaman **yeni liste döner**, orijinali değiştirmez.

Değerler yeni listelere atanarak güncellenebilir.

```scss
@use "sass:list";
@use "sass:map";

$prefixes-by-browser: ("firefox": moz, "safari": webkit, "ie": ms);

@function prefixes-for-browsers($browsers) {
  $prefixes: ();
  @each $browser in $browsers {
    $prefixes: list.append($prefixes, map.get($prefixes-by-browser, $browser));
  }
  @return $prefixes;
}

@debug prefixes-for-browsers("firefox" "ie"); // moz ms
```

---

## 🗂️ Argüman Listeleri (Argument Lists)

Bir `mixin` veya `function`, **sınırsız argüman** alacak şekilde tanımlanırsa, alınan değer özel bir liste türü olan **argüman listesi**dir.

* Normal liste gibi davranır.
* Eğer anahtar-argümanlar verilmişse, `meta.keywords()` ile **map** olarak erişilebilir.

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
