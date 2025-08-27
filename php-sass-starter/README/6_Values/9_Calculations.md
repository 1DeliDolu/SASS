## 🔢 Hesaplamalar (Calculations)

Hesaplamalar, Sass’ın `calc()` fonksiyonunu ve `clamp()`, `min()`, `max()` gibi benzer fonksiyonları temsil etme biçimidir. Sass, bu fonksiyonları mümkün olduğunca basitleştirir; hatta birbirleriyle birleştirilseler bile.

### SCSS Sözdizimi

```scss
@debug calc(400px + 10%); // calc(400px + 10%)
@debug calc(400px / 2); // 200px
@debug min(100px, calc(1rem + 10%)); // min(100px, 1rem + 10%)
```

💡 İlginç bilgi:
Hesaplamalar, CSS’in `calc()` sözdizimini kullanır ama Sass değişkenleri ve fonksiyonlarını da destekler.
Hesaplamaların içinde `/` her zaman **bölme (division)** anlamına gelir.

Interpolation kullanılabilir, fakat bu durumda tip kontrolü yapılmaz ve CSS çıktısı geçersiz olabilir.
Örn: `calc(10px + #{$var})` yerine `calc(10px + $var)` yazın.

---

## 🔽 Basitleştirme (Simplification)

Sass, uyumlu birimlerdeki işlemleri derleme zamanında basitleştirir:

* `1in + 10px` → dönüştürülüp toplanır.
* `clamp(0px, 30px, 20px)` → `20px` döner.

⚠️ Bir hesaplama her zaman `calc()` döndürmek zorunda değildir, bazen basit bir sayı döner.
Tipi kontrol etmek için `meta.type-of()` kullanılabilir.

```scss
$width: calc(400px + 10%);

.sidebar {
  width: $width;
  padding-left: calc($width / 4);
}
```

---

## ➕ İşlemler (Operations)

Hesaplamalar normal SassScript işlemleriyle (`+`, `*`) kullanılamaz.
Bunun yerine, kendi `calc()` ifadeleri içinde yazılmalıdır.

```scss
$width: calc(100% + 10px);
@debug $width * 2; // Error!
@debug calc($width * 2); // calc((100% + 10px) * 2);
```

---

## 🔣 Sabitler (Constants)

Hesaplamalar, sabitler (constants) içerebilir. Bunlar CSS tanımlayıcılarıdır.
İleriye dönük uyumluluk için tüm tanımlayıcılar izinlidir.

Sass, bazı özel sabitleri sayılara çevirir:

* `pi` → π
* `e` → Euler sayısı
* `infinity`, `-infinity`, `NaN`

### SCSS Sözdizimi

```scss
@use 'sass:math';

@debug calc(pi); // 3.1415926536
@debug calc(e);  // 2.7182818285
@debug calc(infinity) > math.$max-number;  // true
@debug calc(-infinity) < math.$min-number; // true
```

---

## 🧮 Hesaplama Fonksiyonları (Calculation Functions)

Sass aşağıdaki fonksiyonları hesaplama olarak ayrıştırır:

* **Karşılaştırma (Comparison):** `min()`, `max()`, `clamp()`
* **Adımlı Değerler (Stepped Values):** `round()`, `mod()`, `rem()`
* **Trigonometrik (Trigonometric):** `sin()`, `cos()`, `tan()`, `asin()`, `acos()`, `atan()`, `atan2()`
* **Üstel (Exponential):** `pow()`, `sqrt()`, `hypot()`, `log()`, `exp()`
* **İşaret İlişkili (Sign-Related):** `abs()`, `sign()`

💡 İlginç bilgi:
Aynı isimde bir Sass fonksiyonu tanımlarsanız, Sass sizin fonksiyonunuzu çağırır, hesaplama değeri üretmez.

---

## 🕰️ Eski Global Fonksiyonlar (Legacy Global Functions)

Sass, `round()`, `abs()`, `min()`, `max()` fonksiyonlarını `calc()`’tan önce de destekliyordu.
Geriye dönük uyumluluk için:

* Eğer çağrı hesaplama olarak geçerli ise → hesaplama olarak işlenir.
* Eğer SassScript özelliği (ör. `%` mod operatörü) içerirse → Sass fonksiyonu olarak işlenir.

Örn:

* `max($padding, env(safe-area-inset-left))` → hesaplama
* `max($padding % 10, 20px)` → Sass fonksiyonu

---

## 📏 `min()` ve `max()`

```scss
$padding: 12px;

.post {
  padding-left: max($padding, env(safe-area-inset-left));
  padding-right: max($padding, env(safe-area-inset-right));
}

.sidebar {
  padding-left: max($padding % 10, 20px);
  padding-right: max($padding % 10, 20px);
}
```

---

## 🔄 `round()`

`round(<strategy>, number, step)` → strateji (nearest, up, down, to-zero), değer ve adım alır.

```scss
$number: 12.5px;
$step: 15px;

.post-image {
  padding-left: round(nearest, $number, $step);
  padding-right: round($number + 10px);
  padding-bottom: round($number + 10px, $step + 10%);
}
```

---

## ➖ `abs()`

`abs(value)` → sayının mutlak değerini döner.
Negatifse `-value`, pozitifse olduğu gibi.

```scss
.post-image {
  padding-left: abs(10px);
  padding-right: math.abs(-7.5%);
  padding-top: abs(1 + 1px);
}
```
