## 🛠️ @function

Fonksiyonlar (functions), SassScript değerleri üzerinde tekrar kullanılabilir karmaşık işlemler tanımlamanıza olanak tanır. Bu sayede okunabilir bir şekilde ortak formülleri ve davranışları soyutlamak kolaylaşır.

Fonksiyonlar `@function` at-kuralı ile tanımlanır. Yazımı: `@function <isim>(<argümanlar...>) { ... }`. Bir fonksiyon adı `--` ile başlamayan herhangi bir Sass tanımlayıcısı olabilir. Yalnızca evrensel ifadeler ve fonksiyon çağrısının sonucunda kullanılacak değeri belirten `@return` at-kuralını içerebilir. Fonksiyonlar, normal CSS fonksiyon sözdizimi kullanılarak çağrılır.

```scss
@function fibonacci($n) {
  $sequence: 0 1;
  @for $_ from 1 through $n {
    $new: nth($sequence, length($sequence)) + nth($sequence, length($sequence) - 1);
    $sequence: append($sequence, $new);
  }
  @return nth($sequence, length($sequence));
}

.sidebar {
  float: left;
  margin-left: fibonacci(4) * 1px;
}
```

👉 Bu örnekte Fibonacci dizisini hesaplayan bir Sass fonksiyonu tanımlanmıştır.

💡 İlginç bilgi: Fonksiyon adları, tüm Sass tanımlayıcıları gibi, tire (`-`) ve alt çizgi (`_`) karakterlerini aynı kabul eder. Yani `scale-color` ve `scale_color` aynı fonksiyona karşılık gelir.

⚠️ Dikkat: Fonksiyonların global değişkenler ayarlamak gibi yan etkileri olması teknik olarak mümkün olsa da bu şiddetle önerilmez. Yan etkiler için `@mixin` kullanın, fonksiyonları yalnızca değer hesaplamak için kullanın.

---

## ⚙️ Argümanlar (Arguments)

Argümanlar, fonksiyonların her çağrıldığında özelleştirilebilmesini sağlar. Argümanlar `@function` tanımı içinde fonksiyon adından sonra parantez içinde değişken isimleri listelenerek belirtilir. Fonksiyon çağrısı yapılırken aynı sayıda argüman geçilmelidir.

💡 İlginç bilgi: Argüman listeleri sondaki virgülü de kabul eder! Bu, stillerinizi yeniden düzenlerken sözdizimi hatalarını önlemeyi kolaylaştırır.

---

## 🧩 Opsiyonel Argümanlar (Optional Arguments)

Normalde, bir fonksiyonun tanımladığı her argüman çağrıldığında geçilmelidir. Ancak, varsayılan bir değer tanımlayarak argümanı opsiyonel hale getirebilirsiniz. Bu değer, değişken adı, ardından iki nokta (`:`) ve bir SassScript ifadesiyle belirtilir.

```scss
@function invert($color, $amount: 100%) {
  $inverse: change-color($color, $hue: hue($color) + 180);
  @return mix($inverse, $color, $amount);
}

$primary-color: #036;
.header {
  background-color: invert($primary-color, 80%);
}
```

👉 Bu fonksiyon, renkleri tersine çevirir ve varsayılan `amount` değerini kullanabilir.

💡 İlginç bilgi: Varsayılan değerler herhangi bir SassScript ifadesi olabilir, hatta önceki argümanlara atıfta bulunabilir.

---

## 🏷️ Anahtar Kelime Argümanları (Keyword Arguments)

Fonksiyon çağrılırken, argümanlar listedeki pozisyonlarına göre geçilebildiği gibi, isimleriyle de geçirilebilir. Bu, özellikle çok sayıda opsiyonel argüman içeren fonksiyonlarda faydalıdır.

```scss
$primary-color: #036;
.banner {
  background-color: $primary-color;
  color: scale-color($primary-color, $lightness: +40%);
}
```

👉 Bu örnekte `scale-color` fonksiyonuna `lightness` anahtar argümanı adıyla geçirilmiştir.

⚠️ Dikkat: Argüman adları değiştirildiğinde eski kullanıcılarınızın kodu bozulabilir. Eski ismi opsiyonel olarak bir süre daha desteklemek iyi bir yaklaşımdır.

---

## 📋 Keyfi Argümanlar Alma (Taking Arbitrary Arguments)

Bazen bir fonksiyonun herhangi bir sayıda argüman alması yararlı olabilir. Eğer `@function` tanımındaki son argüman `...` ile biterse, ekstra tüm argümanlar bu argümanda bir liste olarak toplanır.

```scss
@function sum($numbers...) {
  $sum: 0;
  @each $number in $numbers {
    $sum: $sum + $number;
  }
  @return $sum;
}

.micro {
  width: sum(50px, 30px, 100px);
}
```

👉 Bu fonksiyon kendisine verilen tüm sayıları toplar.

---

## 🗂️ Keyfi Anahtar Argümanlar Alma (Taking Arbitrary Keyword Arguments)

Argüman listeleri ayrıca keyfi anahtar argümanlarını da kabul edebilir. `meta.keywords()` fonksiyonu bir argüman listesi alır ve fazla geçirilen anahtarları bir map olarak döndürür.

💡 İlginç bilgi: Eğer `meta.keywords()` fonksiyonunu hiç çağırmazsanız, argüman listeniz fazla anahtar kabul etmez. Bu, yazım hatalarını önlemeye yardımcı olur.

---

## 🔄 Keyfi Argümanlar Geçme (Passing Arbitrary Arguments)

Tıpkı fonksiyonların keyfi argüman alabilmesi gibi, siz de başka bir fonksiyona liste veya map kullanarak argüman geçebilirsiniz.

```scss
$widths: 50px, 30px, 100px;
.micro {
  width: min($widths...);
}
```

👉 Burada `min()` fonksiyonuna liste `...` ile açılarak aktarılmıştır.

```scss
@function fg($args...) {
  @warn "The fg() function is deprecated. Call foreground() instead.";
  @return foreground($args...);
}
```

👉 Bu örnekte `fg()` fonksiyonu, `foreground()` fonksiyonuna bir takma ad olarak tanımlanmıştır.

---

## 🔙 @return

`@return`, bir fonksiyon çağrısının sonucunda döndürülecek değeri belirler. Yalnızca `@function` içinde kullanılabilir ve her fonksiyon bir `@return` ile bitmelidir.

```scss
@use "sass:string";

@function str-insert($string, $insert, $index) {
  @if string.length($string) == 0 {
    @return $insert;
  }

  $before: string.slice($string, 0, $index);
  $after: string.slice($string, $index);
  @return $before + $insert + $after;
}
```

👉 Bu örnek, bir string içerisine belirli bir konumda başka bir string ekler.

---

## 📚 Diğer Fonksiyonlar (Other Functions)

Kullanıcı tanımlı fonksiyonlara ek olarak, Sass her zaman kullanılabilen geniş bir dahili fonksiyon kütüphanesi sağlar. Ayrıca, ana dilde özel fonksiyonlar tanımlamak da mümkündür. Elbette, normal CSS fonksiyonlarını da çağırabilirsiniz.

### 🌐 Düz CSS Fonksiyonları (Plain CSS Functions)

Kullanıcı tanımlı veya dahili olmayan herhangi bir fonksiyon çağrısı, düz CSS fonksiyonuna derlenir.

```scss
@debug var(--main-bg-color); // var(--main-bg-color)

$primary: #f2ece4;
$accent: #e1d7d2;
@debug radial-gradient($primary, $accent); // radial-gradient(#f2ece4, #e1d7d2)
```

👉 Burada Sass, CSS fonksiyonlarını olduğu gibi çıktı olarak üretir.

⚠️ Dikkat: Bilinmeyen bir fonksiyon adı yazım hatasıyla CSS fonksiyonuna dönüşebilir. Çıktınızı kontrol etmek için bir CSS linter kullanmak faydalıdır.

💡 İlginç bilgi: `calc()` ve `element()` gibi bazı CSS fonksiyonlarının özel sözdizimleri vardır. Sass bu tür fonksiyonları tırnaksız string olarak işler.
