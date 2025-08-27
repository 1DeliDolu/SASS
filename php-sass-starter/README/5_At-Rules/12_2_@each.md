## 🔁 @each

`@each` kuralı, bir listedeki her eleman veya bir haritadaki (map) her çift için stilleri üretmeyi ya da kodu değerlendirmeyi kolaylaştırır. Küçük farklılıklar dışında tekrar eden stiller için idealdir. Genellikle `@each <variable> in <expression> { ... }` şeklinde yazılır, burada ifade bir liste döner. Blok listedeki her eleman için sırayla değerlendirilir ve ilgili değişken adına atanır.

### SCSS Sözdizimi

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

👉 Burada her `size` için ikon sınıfları (`.icon-40px`, `.icon-50px`, `.icon-80px`) otomatik oluşturulur.

---

## 🗺️ Haritalar ile (@each with maps)

`@each`, bir haritadaki her anahtar/değer (key/value) çifti üzerinde de döngü kurmak için kullanılabilir. Bunun için `@each <variable>, <variable> in <expression> { ... }` şeklinde yazılır. İlk değişken anahtarı, ikinci değişken ise değeri alır.

### SCSS Sözdizimi

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

👉 Bu örnekte her ikon adı ve Unicode değeri kullanılarak `.icon-eye`, `.icon-start`, `.icon-stop` sınıfları oluşturulur.

---

## 🔓 Destructuring (Parçalama)

Eğer iç içe listelerden oluşan bir listeniz varsa, `@each` ile iç listelerdeki değerleri otomatik olarak değişkenlere atayabilirsiniz. Bu işleme **destructuring** (parçalama) denir çünkü değişkenler iç listenin yapısıyla eşleşir. Her değişken adı listedeki karşılık gelen konumdaki değere atanır, eğer değer yoksa `null` atanır.

### SCSS Sözdizimi

```scss
$icons:
  "eye" "\f112" 12px,
  "start" "\f12e" 16px,
  "stop" "\f12f" 10px;

@each $name, $glyph, $size in $icons {
  .icon-#{$name}:before {
    display: inline-block;
    font-family: "Icon Font";
    content: $glyph;
    font-size: $size;
  }
}
```

👉 Bu örnekte her ikon için hem Unicode değeri hem de yazı tipi boyutu birlikte kullanılır.

---

💡 İlginç bilgi:
`@each`, destructuring (parçalama) desteklediği için haritalar listelerin listesi olarak kabul edilir. Bu nedenle `@each` için harita desteği özel bir işleme gerek kalmadan doğal olarak çalışır.
