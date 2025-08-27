## 🔀 Mantıksal Operatörler (boolean operators)

JavaScript gibi dillerin aksine, Sass mantıksal operatörler için semboller yerine **kelimeler** kullanır:

* `not <ifade>` → ifadenin tersini döndürür: `true` → `false`, `false` → `true`.
* `<ifade> and <ifade>` → her iki ifade de `true` ise `true`, herhangi biri `false` ise `false` döndürür.
* `<ifade> or <ifade>` → ifadelerden en az biri `true` ise `true`, her ikisi de `false` ise `false` döndürür.

---

### SCSS Sözdizimi

```scss
@debug not true; // false
@debug not false; // true

@debug true and true; // true
@debug true and false; // false

@debug true or false; // true
@debug false or false; // false
```

👉 Sass’te `not`, `and` ve `or` mantıksal operatörleri bu şekilde çalışır.

---

## ✅ Doğruluk ve Yanlışlık (truthiness and falsiness)

`true` veya `false` beklenen her yerde, başka değerler de kullanılabilir.

* `false` ve `null` → **yanlış (falsey)** kabul edilir. Yani koşulları başarısız kılar.
* Diğer tüm değerler → **doğru (truthy)** kabul edilir. Yani koşulları başarılı kılar.

Örneğin, bir dizenin boşluk içerip içermediğini kontrol etmek için:

```scss
string.index($string, " ");
```

Bu fonksiyon, dize bulunmazsa `null` döndürür; bulunduğunda ise bir sayı döndürür.

---

⚠️ Dikkat!
Bazı dillerde boş dizeler, boş listeler veya `0` da **falsey** kabul edilir. Ancak Sass’te böyle değildir!

* Boş dizeler
* Boş listeler
* `0` sayısı

👉 Bunların hepsi **truthy** kabul edilir.
