## 🔂 @while

`@while` kuralı, `@while <expression> { ... }` şeklinde yazılır ve ifadesi `true` dönerse bloğu değerlendirir. Daha sonra ifade hala `true` dönerse blok tekrar çalışır. Bu işlem, ifade `false` dönene kadar devam eder.

### SCSS Sözdizimi

```scss
@use "sass:math";

/// `$value` değerini `$ratio` ile böler, ta ki `$base` değerinin altına düşene kadar.
@function scale-below($value, $base, $ratio: 1.618) {
  @while $value > $base {
    $value: math.div($value, $ratio);
  }
  @return $value;
}

$normal-font-size: 16px;
sup {
  font-size: scale-below(20px, 16px);
}
```

👉 Bu örnekte `@while`, `$value` değeri `$base` değerinin altına inene kadar bölme işlemini sürdürür.

---

⚠️ Dikkat!
`@while`, özellikle çok karmaşık stiller için gerekli olabilir, ancak mümkünse `@each` veya `@for` kullanmak genellikle daha iyidir. Bu kurallar okuyucu için daha anlaşılırdır ve çoğu durumda derleme süresi açısından da daha hızlıdır.

---

## ✅ Doğruluk (Truthiness) ve Yanlışlık (Falsiness)

`true` veya `false` kullanılabilen her yerde başka değerler de kullanılabilir.

* `false` ve `null` değerleri **falsey** kabul edilir, yani Sass bunları yanlış olarak değerlendirir ve koşullar başarısız olur.
* Diğer tüm değerler **truthy** kabul edilir, yani Sass bunları doğru olarak değerlendirir ve koşullar başarılı olur.

Örneğin, bir dizgenin (string) boşluk içerip içermediğini kontrol etmek için `string.index($string, " ")` yazabilirsiniz. `string.index()` fonksiyonu, boşluk bulunmazsa `null`, aksi halde bir sayı döner.

⚠️ Dikkat!
Bazı diller `false` ve `null` dışında daha fazla değeri **falsey** kabul eder. Sass bu dillerden biri değildir!
Boş dizgeler, boş listeler ve sayı `0` Sass içinde **truthy** kabul edilir.
