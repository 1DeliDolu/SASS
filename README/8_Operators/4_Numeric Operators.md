## ➗ Sayısal Operatörler (numeric operators)

Sass, sayılar için standart matematiksel operatörleri destekler. Uyumlu birimler arasında otomatik dönüşüm yapılır.

* `<ifade> + <ifade>` → ilk ifadenin değerini ikincisine ekler.
* `<ifade> - <ifade>` → ilk ifadenin değerinden ikincisini çıkarır.
* `<ifade> * <ifade>` → ilk ifadenin değerini ikincisiyle çarpar.
* `<ifade> % <ifade>` → ilk ifadenin değerinin ikinciye bölümünden kalanı döndürür (**modulo operatörü**).

### SCSS Sözdizimi

```scss
@debug 10s + 15s; // 25s
@debug 1in - 10px; // 0.8958333333in
@debug 5px * 3px; // 15px*px
@debug 1in % 9px; // 0.0625in
```

👉 Temel toplama, çıkarma, çarpma ve mod alma işlemleri.

Birim içermeyen sayılar (`unitless numbers`), herhangi bir birimle birlikte kullanılabilir.

```scss
@debug 100px + 50; // 150px
@debug 4s * 10; // 40s
```

👉 Birimsiz sayılar, karşılaştırılan veya işleme giren değerin birimine dönüştürülür.

Uyumsuz birimlere sahip sayılar toplama, çıkarma veya mod alma işleminde kullanılamaz.

```scss
@debug 100px + 10s;
//     ^^^^^^^^^^^
// Error: Incompatible units px and s.
```

👉 Burada `px` ve `s` uyumsuz olduğundan hata alınır.

---

## 🔀 Tekli Operatörler (unary operators)

`+` ve `-`, tekli (unary) operatör olarak da kullanılabilir:

* `+<ifade>` → ifadenin değerini değiştirmeden döndürür.
* `-<ifade>` → ifadenin değerinin negatif versiyonunu döndürür.

### SCSS Sözdizimi

```scss
@debug +(5s + 7s); // 12s
@debug -(50px + 30px); // -80px
@debug -(10px - 15px); // 5px
```

👉 `+` ve `-` operatörleri tek başına kullanıldığında değerlerin işaretini değiştirebilir.

⚠️ Dikkat!
`-` hem çıkarma hem de tekli negatifleme için kullanılabildiğinden, liste bağlamında karışıklık olabilir. Güvenli olmak için:

* Çıkarma yaparken `-` işaretinin iki yanına boşluk koyun.
* Negatif sayı veya tekli negatifleme için `-` işaretinden önce boşluk bırakın, sonrasına bırakmayın.
* Eğer `-` bir boşlukla ayrılmış listede kullanılıyorsa, onu paranteze alın.

### SCSS Sözdizimi

```scss
@debug a-1; // a-1
@debug 5px-3px; // 2px
@debug 5-3; // 2
@debug 1 -2 3; // 1 -2 3

$number: 2;
@debug 1 -$number 3; // -1 3
@debug 1 (-$number) 3; // 1 -2 3
```

👉 `-` işaretinin farklı bağlamlardaki anlamlarını gösterir.

---

## ➗ Bölme (division)

### 🧩 Uyumluluk (`math.div()`):

* Dart Sass: 1.33.0’dan itibaren
* LibSass: ✗
* Ruby Sass: ✗

Sass’ta bölme işlemi `math.div()` fonksiyonu ile yapılır. Çünkü `/` işareti CSS’te ayırıcı olarak da kullanılır (örneğin `font: 15px/32px`). Sass, `/` ile bölmeyi destekler ama bu özellik **kullanımdan kaldırılmıştır** ve gelecekte tamamen silinecektir.

---

### 🔀 Eğik Çizgiyle Ayrılmış Değerler (slash-separated values)

Sass, `/`’in hem ayırıcı hem de bölme olabilmesi nedeniyle, bağlama göre ayrım yapar. Eğer iki sayı `/` ile ayrılırsa, sonuç **bölüm yerine eğik çizgiyle ayrılmış değer** olarak yazdırılır, ta ki bu koşullardan biri gerçekleşene kadar:

* İfadelerden biri saf sayı değilse,
* Sonuç bir değişkene atanır veya fonksiyondan döndürülürse,
* İşlem parantez içine alınmışsa (liste dışında),
* Sonuç başka bir işlemde kullanılıyorsa,
* Sonuç bir hesaplama tarafından döndürülüyorsa.

`list.slash()` fonksiyonu kullanılarak `/`’in ayırıcı olarak çalışması zorlanabilir.

### SCSS Sözdizimi

```scss
@use "sass:list";

@debug 15px / 30px; // 15px/30px
@debug (10px + 5px) / 30px; // 0.5
@debug list.slash(10px + 5px, 30px); // 15px/30px

$result: 15px / 30px;
@debug $result; // 0.5

@function fifteen-divided-by-thirty() {
  @return 15px / 30px;
}
@debug fifteen-divided-by-thirty(); // 0.5

@debug (15px/30px); // 0.5
@debug (bold 15px/30px sans-serif); // bold 15px/30px sans-serif
@debug 15px/30px + 1; // 1.5
```

👉 Sass, `/` işaretini bağlama göre ayırıcı veya bölme olarak yorumlar.

---

## 📏 Birimler (units)

Sass, gerçek dünyadaki birim hesaplamalarını taklit eden güçlü bir birim desteğine sahiptir:

* İki sayı çarpıldığında, birimleri de çarpılır.
* Bir sayı diğerine bölündüğünde, pay birimleri ilk sayıdan, payda birimleri ikinci sayıdan gelir.
* Bir sayı, hem payda hem de payda kısmında birden çok birime sahip olabilir.

### SCSS Sözdizimi

```scss
@use 'sass:math';

@debug 4px * 6px; // 24px*px (kare piksel)
@debug math.div(5px, 2s); // 2.5px/s (saniye başına piksel)

// 3.125px*deg/s*em (piksel-derece / saniye-em)
@debug 5px * math.div(math.div(30deg, 2s), 24em);

$degrees-per-second: math.div(20deg, 1s);
@debug $degrees-per-second; // 20deg/s
@debug math.div(1, $degrees-per-second); // 0.05s/deg
```

👉 Çarpma ve bölmede birimlerin nasıl işlendiğini gösterir.

⚠️ Dikkat!
CSS, `px*px` gibi **karmaşık birimleri** desteklemez. Böyle bir değer bir CSS özelliğine atanırsa hata alınır. Bu, genellikle hesaplamalarınızda hata olduğunu gösterir.

Sass, uyumlu birimler arasında otomatik dönüşüm yapar. Ancak uyumsuz birimler birleştirilmeye çalışılırsa hata verir.

```scss
// CSS’e göre 1 inç = 96 piksel
@debug 1in + 6px; // 102px veya 1.0625in

@debug 1in + 1s;
//     ^^^^^^^^
// Error: Incompatible units s and in.
```

👉 Uyumlu birimler otomatik dönüştürülür, uyumsuz birimler hata verir.

Uyumlu birimler birbirini sadeleştirebilir. Örneğin `math.div(96px, 1in)` → `1`, çünkü CSS’te 96px = 1in’dir. Bu, **oranlar (ratios)** tanımlamayı kolaylaştırır.

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

👉 Burada, geçiş süresi birim temizliğiyle doğru şekilde hesaplanmaktadır.

---

⚠️ Ek Notlar:

* Matematiğiniz yanlış birim veriyorsa, bir yerde birim atlamış olabilirsiniz.
* `#{$number}px` gibi interpolasyonlardan kaçının; bu, aslında sayı değil **dize (string)** üretir. Doğru olan `$number * 1px` yazmaktır.
* **Yüzdeler (percentages)** Sass’te diğer birimler gibi çalışır ve ondalıklarla (decimals) aynı değildir.

Örnek: `50%` ≠ `0.5`.

* Yüzdeleri ondalığa çevirmek için: `math.div($percentage, 100%)`.
* Ondalığı yüzdeye çevirmek için: `$decimal * 100%` veya `math.percentage($decimal)`.
