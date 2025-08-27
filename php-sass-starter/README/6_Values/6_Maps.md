## 🗺️ Haritalar (Maps)

Haritalar (maps), Sass’ta anahtar–değer (key–value) çiftlerini tutar ve ilgili anahtarla eşleşen değeri kolayca bulmayı sağlar.
Şu şekilde yazılır: `(<expression>: <expression>, <expression>: <expression>)`

* `:` işaretinden önceki ifade → **anahtar**
* `:` işaretinden sonraki ifade → **değer**
* Anahtarlar benzersiz olmalıdır, değerler tekrar edebilir.
* Listelerden farklı olarak, haritalar mutlaka **parantez** içinde yazılmalıdır.
* Boş harita → `()`

💡 İlginç bilgi:
Boş harita `()` → boş listeyle aynıdır. Çünkü tüm haritalar aynı zamanda liste olarak da sayılır.
Örn: `(1: 2, 3: 4)` listesi `(1 2, 3 4)` olarak da değerlendirilebilir.

Anahtarlar herhangi bir Sass değeri olabilir. Anahtar eşitliği `==` operatörüyle belirlenir.

⚠️ Dikkat!
Anahtar olarak genellikle **tırnaklı dizgeler** kullanmak en güvenlisidir. Çünkü renk adları gibi bazı değerler tırnaksız yazıldığında başka türler olarak algılanabilir.

---

## 🛠️ Haritaları Kullanmak

Haritalar CSS değerleri değildir, bu yüzden doğrudan kullanılamazlar. Sass, haritaları oluşturmak ve içlerindeki değerlere erişmek için fonksiyonlar sağlar.

---

### 🔎 Değer Arama (Look Up a Value)

`map.get($map, $key)` → anahtar için değer döner.
Anahtar yoksa `null` döner.

```scss
@use "sass:map";
$font-weights: ("regular": 400, "medium": 500, "bold": 700);

@debug map.get($font-weights, "medium"); // 500
@debug map.get($font-weights, "extra-bold"); // null
```

---

### 🔁 Her Çift için İşlem (Do Something for Every Pair)

`@each` → her anahtar–değer çifti için blok çalıştırır.

```scss
$icons: ("eye": "\f112", "start": "\f12e", "stop": "\f12f");

@each $name, $glyph in $icons {
  .icon-#{$name}:before {
    display: inline-block;
    font-family: "Icon Font";
    content: $glyph;
  }
}
```

---

### ➕ Haritaya Ekleme (Add to a Map)

`map.set($map, $key, $value)` → haritaya yeni çift ekler veya mevcut anahtarın değerini günceller.
Orijinal harita değişmez (immutable).

```scss
@use "sass:map";

$font-weights: ("regular": 400, "medium": 500, "bold": 700);

@debug map.set($font-weights, "extra-bold", 900);
// ("regular": 400, "medium": 500, "bold": 700, "extra-bold": 900)

@debug map.set($font-weights, "bold", 900);
// ("regular": 400, "medium": 500, "bold": 900)
```

İki haritayı birleştirmek için `map.merge($map1, $map2)` kullanılır.
Eğer aynı anahtar varsa, ikinci haritadaki değer kullanılır.

```scss
@use "sass:map";

$light-weights: ("lightest": 100, "light": 300);
$heavy-weights: ("medium": 500, "bold": 700);

@debug map.merge($light-weights, $heavy-weights);
// ("lightest": 100, "light": 300, "medium": 500, "bold": 700)

$weights: ("light": 300, "medium": 500);
@debug map.merge($weights, ("medium": 700));
// ("light": 300, "medium": 700)
```

---

## 🔒 Değişmezlik (Immutability)

Haritalar değişmezdir (immutable). İçerikleri değiştirilemez.

* `map.set()` ve `map.merge()` → yeni harita döner, orijinali değiştirmez.

Durum güncellemek için aynı değişkene yeni haritalar atanabilir.
Bu yaklaşım genellikle fonksiyonlar ve mixin’lerde kullanılır.

```scss
@use "sass:map";

$prefixes-by-browser: ("firefox": moz, "safari": webkit, "ie": ms);

@mixin add-browser-prefix($browser, $prefix) {
  $prefixes-by-browser: map.merge($prefixes-by-browser, ($browser: $prefix)) !global;
}

@include add-browser-prefix("opera", o);
@debug $prefixes-by-browser;
// ("firefox": moz, "safari": webkit, "ie": ms, "opera": o)
```
