## 📝 @if ve @else

`@if` kuralı `@if <expression> { ... }` şeklinde yazılır ve bloğun değerlendirilip değerlendirilmeyeceğini (CSS olarak stillerin üretilmesi dahil) kontrol eder. İfade (expression) genellikle `true` veya `false` döner—eğer ifade `true` dönerse blok değerlendirilir, `false` dönerse değerlendirilmez.

### SCSS Sözdizimi

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

👉 Bu örnekte `@if` kullanılarak `$circle` parametresi `true` ise köşeler yuvarlatılır.

---

## 🔄 @else

Bir `@if` kuralı isteğe bağlı olarak `@else` kuralı ile takip edilebilir, `@else { ... }` şeklinde yazılır. Eğer `@if` ifadesi `false` dönerse bu bloğun içi değerlendirilir.

### SCSS Sözdizimi

```scss
$light-background: #f2ece4;
$light-text: #036;
$dark-background: #6b717f;
$dark-text: #d2e1dd;

@mixin theme-colors($light-theme: true) {
  @if $light-theme {
    background-color: $light-background;
    color: $light-text;
  } @else {
    background-color: $dark-background;
    color: $dark-text;
  }
}

.banner {
  @include theme-colors($light-theme: true);
  body.dark & {
    @include theme-colors($light-theme: false);
  }
}
```

👉 Burada `@else`, koyu tema (`dark theme`) durumunda çalışır.

---

## 🔀 @else if

Bir `@else` kuralının bloğunun değerlendirilip değerlendirilmeyeceğini `@else if <expression> { ... }` şeklinde belirleyebilirsiniz. Bu durumda blok, yalnızca önceki `@if` ifadesi `false` döner ve `@else if` ifadesi `true` dönerse çalışır.

İstediğiniz kadar çok `@else if` zincirleyebilirsiniz. Zincirdeki ilk `true` dönen blok çalışır, diğerleri çalışmaz. Zincirin sonunda düz bir `@else` varsa, hiçbir blok başarılı olmazsa bu blok çalışır.

### SCSS Sözdizimi

```scss
@use "sass:math";

@mixin triangle($size, $color, $direction) {
  height: 0;
  width: 0;

  border-color: transparent;
  border-style: solid;
  border-width: math.div($size, 2);

  @if $direction == up {
    border-bottom-color: $color;
  } @else if $direction == right {
    border-left-color: $color;
  } @else if $direction == down {
    border-top-color: $color;
  } @else if $direction == left {
    border-right-color: $color;
  } @else {
    @error "Unknown direction #{$direction}.";
  }
}

.next {
  @include triangle(5px, black, right);
}
```

👉 Bu örnekte yön (`$direction`) değerine göre üçgenin kenarına renk atanır.

---

## ✅ Doğruluk (Truthiness) ve Yanlışlık (Falsiness)

`true` veya `false` kullanılabilen her yerde başka değerler de kullanılabilir. `false` ve `null` değerleri **falsey** kabul edilir, yani Sass bunları yanlış (false) olarak değerlendirir ve koşullar başarısız olur. Diğer tüm değerler **truthy** kabul edilir, yani Sass bunları doğru (true) olarak değerlendirir ve koşullar başarılı olur.

Örneğin, bir dizgenin (string) boşluk içerip içermediğini kontrol etmek için sadece `string.index($string, " ")` yazabilirsiniz. `string.index()` fonksiyonu, dizge bulunmazsa `null`, aksi halde bir sayı döner.

⚠️ Dikkat!
Bazı dillerde `false` ve `null` dışında da falsey değerler vardır. Sass bu dillerden biri değildir! Boş dizgeler, boş listeler ve sayı `0` Sass içinde **truthy** kabul edilir.
