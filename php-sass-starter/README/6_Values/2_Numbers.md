## 🔢 Sayılar (Numbers)

Sass’ta sayılar iki bileşenden oluşur: sayının kendisi ve birimleri.
Örneğin `16px` ifadesinde sayı `16`, birim ise `px`’dir.

Sayılar birimsiz olabileceği gibi karmaşık (complex) birimlere de sahip olabilir. Ayrıntılar için **Birimler (Units)** bölümüne bakınız.

### SCSS Sözdizimi

```scss
@debug 100; // 100
@debug 0.8; // 0.8
@debug 16px; // 16px
@debug 5px * 2px; // 10px*px (okunuş: kare piksel)
```

Sass sayıları, bilimsel gösterim (scientific notation) dahil CSS sayılarıyla aynı biçimleri destekler.
Bilimsel gösterim, sayı ile 10’un kuvveti arasına `e` konularak yazılır.
Tarayıcı desteği sorunlu olduğundan, Sass bu biçimi her zaman açılmış haliyle derler.

### SCSS Sözdizimi

```scss
@debug 5.2e3; // 5200
@debug 6e-2; // 0.06
```

⚠️ Dikkat!
Sass, tam sayılar (integers) ve ondalıklı sayılar (decimals) arasında ayrım yapmaz. Örneğin:
`math.div(5, 2)` → `2.5` döner.
Bu davranış JavaScript ile aynıdır, fakat birçok programlama dilinden farklıdır.

---

## 📐 Birimler (Units)

Sass, gerçek dünyadaki birim hesaplamalarını taklit eden güçlü bir birim desteğine sahiptir:

* İki sayı çarpıldığında, birimleri de çarpılır.
* Bir sayı diğerine bölündüğünde, sonuç birincinin **pay** (numerator) birimlerini ve ikincinin **payda** (denominator) birimlerini alır.
* Bir sayının payda ve/veya pay kısmında birden çok birim olabilir.

### SCSS Sözdizimi

```scss
@use 'sass:math';

@debug 4px * 6px; // 24px*px (okunuş: kare piksel)
@debug math.div(5px, 2s); // 2.5px/s (okunuş: saniye başına piksel)

// 3.125px*deg/s*em (okunuş: piksel-derece / saniye-em)
@debug 5px * math.div(math.div(30deg, 2s), 24em);

$degrees-per-second: math.div(20deg, 1s);
@debug $degrees-per-second; // 20deg/s
@debug math.div(1, $degrees-per-second); // 0.05s/deg
```

⚠️ Dikkat!
CSS, `px*px` (kare piksel) gibi karmaşık birimleri desteklemez. Böyle bir birimi stil değeri olarak kullanmak hata üretir.
Bu aslında faydalı bir özelliktir: yanlış birimle karşılaştığınızda hesaplamalarınızı kontrol etmeniz gerektiğini gösterir.
Bir değişkenin veya ifadenin birimlerini görmek için her zaman `@debug` kullanabilirsiniz.

Sass, uyumlu birimler arasında otomatik dönüşüm yapar. Sonucun hangi birimde döneceği Sass’ın implementasyonuna bağlıdır.
Eğer uyumsuz birimleri (ör. `1in + 1em`) birleştirmeye çalışırsanız, Sass hata üretir.

### SCSS Sözdizimi

```scss
// CSS bir inç’i 96 piksel olarak tanımlar.
@debug 1in + 6px; // 102px veya 1.0625in

@debug 1in + 1s;
// Error: Incompatible units s and in.
```

Gerçek dünyadaki hesaplamalarda olduğu gibi, pay ve paydada birbirine uyumlu birimler varsa bunlar sadeleşir.
Örneğin `math.div(96px, 1in)` → birim sadeleşir.
Bu sayede birimler arası dönüşüm için oranlar tanımlamak kolaylaşır.

### SCSS Sözdizimi

```scss
@use 'sass:math';

$transition-speed: math.div(1s, 50px);

@mixin move($left-start, $left-stop) {
  position: absolute;
  left: $left-start;
  transition: left ($left-stop - $left-start) * $transition-speed;

  &:hover {
    left: $left-stop;
  }
}

.slider {
  @include move(10px, 120px);
}
```

⚠️ Dikkat!
Yanlış birim sonucu alıyorsanız, muhtemelen birimi olması gereken bir değeri birimsiz bıraktınız.
**Interpolation** (`#{$number}px`) kullanmaktan kaçının. Bu gerçek bir sayı değil, tırnaksız bir dizge üretir.
Bunun yerine `$number * 1px` yazın veya `$number` zaten `px` birimine sahipse direkt kullanın.

⚠️ Dikkat!
Yüzdeler (%) Sass’ta diğer tüm birimler gibi çalışır. Ondalık sayılarla aynı şey değildir:

* `50%` → `%` birimli bir sayı
* `0.5` → birimsiz sayı

Dönüştürmek için birim aritmetiği kullanabilirsiniz:

* `math.div($percentage, 100%)` → ondalık sayı
* `$decimal * 100%` → yüzde değeri
  Ayrıca `math.percentage($decimal)` fonksiyonu da `%` değerine dönüştürmek için kullanılabilir.

---

## 🎯 Hassasiyet (Precision)

Sass sayıları dahili olarak 64-bit kayan nokta değerlerdir. CSS çıktısına yazılırken ve eşitlik karşılaştırmalarında **ondalık sonrası 10 basamağa kadar** hassasiyeti destekler.

* Oluşturulan CSS’te yalnızca ondalık sonrası 10 basamak yazılır.
* `==` ve `>=` işlemleri, ondalık sonrası 10. basamağa kadar eşitse iki sayıyı aynı kabul eder.
* Bir sayı, bir tam sayıya `0.0000000001`’den daha yakınsa, `list.nth()` gibi tam sayı bekleyen fonksiyonlar için tam sayı kabul edilir.

### SCSS Sözdizimi

```scss
@debug 0.012345678912345; // 0.0123456789
@debug 0.01234567891 == 0.01234567899; // true
@debug 1.00000000009; // 1
@debug 0.99999999991; // 1
```

💡 İlginç bilgi:
Sayılar, yalnızca gerekli olduğunda **tembel (lazy)** olarak 10 basamağa yuvarlanır.
Bu sayede matematiksel işlemler dahili olarak tam değerle yapılır ve fazladan yuvarlama hataları birikmez.
