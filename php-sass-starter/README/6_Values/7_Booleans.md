## ✅ Booleans (Mantıksal Değerler)

Booleans, mantıksal değerler olan `true` ve `false`’tur.
Bu değerler:

* Doğrudan yazılabilir.
* Eşitlik ve karşılaştırma operatörleriyle dönebilir.
* `math.comparable()`, `map.has-key()` gibi yerleşik fonksiyonlar tarafından dönebilir.

### SCSS Sözdizimi

```scss
@use "sass:math";

@debug 1px == 2px; // false
@debug 1px == 1px; // true
@debug 10px < 3px; // false
@debug math.comparable(100px, 3in); // true
```

---

## 🔀 Mantıksal Operatörler (Boolean Operators)

* `and` → iki taraf da `true` ise `true` döner.
* `or` → iki taraftan biri `true` ise `true` döner.
* `not` → tek bir boolean değerin tersini döner.

### SCSS Sözdizimi

```scss
@debug true and true; // true
@debug true and false; // false

@debug true or false; // true
@debug false or false; // false

@debug not true; // false
@debug not false; // true
```

---

## 🛠️ Booleans Kullanımı (Using Booleans)

Booleans, Sass’ta çeşitli işlemleri yapıp yapmamayı seçmek için kullanılabilir.

### `@if` kuralı

Argümanı `true` ise bloğu çalıştırır.

```scss
@use "sass:math";

@mixin avatar($size, $circle: false) {
  width: $size;
  height: $size;

  @if $circle {
    border-radius: math.div($size, 2);
  }
}

.square-av {
  @include avatar(100px, $circle: false);
}
.circle-av {
  @include avatar(100px, $circle: true);
}
```

### `if()` fonksiyonu

Bir argüman `true` ise bir değer, `false` ise başka bir değer döner.

```scss
@debug if(true, 10px, 30px); // 10px
@debug if(false, 10px, 30px); // 30px
```

---

## 🔎 Doğruluk (Truthiness) ve Yanlışlık (Falsiness)

`true` veya `false` gereken yerlerde başka değerler de kullanılabilir.

* `false` ve `null` → **falsey** (yanlış kabul edilir).
* Diğer tüm değerler → **truthy** (doğru kabul edilir).

Örneğin, bir dizgenin boşluk içerip içermediğini kontrol etmek için:

```scss
string.index($string, " ")
```

* Bulunmazsa → `null` döner.
* Bulunursa → sayı döner.

⚠️ Dikkat!
Bazı diller `false` ve `null` dışında daha fazla değeri falsey kabul eder. Sass öyle değildir!

* Boş dizgeler → truthy
* Boş listeler → truthy
* `0` → truthy
