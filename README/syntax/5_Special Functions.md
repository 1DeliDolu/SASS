## ⚙️ Özel Fonksiyonlar (Special Functions)

CSS birçok fonksiyon tanımlar ve bunların çoğu Sass’ın normal fonksiyon sözdizimi ile sorunsuz çalışır. Bu fonksiyonlar bir fonksiyon çağrısı olarak ayrıştırılır, düz CSS fonksiyonuna çevrilir ve CSS’e olduğu gibi derlenir. Ancak, bazı fonksiyonlar SassScript ifadesi olarak ayrıştırılamayan özel sözdizimine sahiptir. **Tüm özel fonksiyon çağrıları tırnaksız dizeler (unquoted strings) döndürür.**

### 🔗 `url()`

`url()` fonksiyonu CSS’te yaygın olarak kullanılır, ancak sözdizimi diğer fonksiyonlardan farklıdır: tırnaklı veya tırnaksız URL alabilir.

* Tırnaksız URL, geçerli bir SassScript ifadesi olmadığından Sass onu ayrıştırmak için özel mantık kullanır.
* Eğer `url()` argümanı geçerli bir tırnaksız URL ise, Sass onu olduğu gibi ayrıştırır. Interpolasyon (`#{}`) kullanılarak SassScript değerleri de eklenebilir.
* Eğer argüman değişkenler veya fonksiyon çağrıları içeriyorsa, normal bir CSS fonksiyon çağrısı olarak ayrıştırılır.

#### Örnek (SCSS):

```scss
$roboto-font-path: "../fonts/roboto";

@font-face {
    // This is parsed as a normal function call that takes a quoted string.
    src: url("#{$roboto-font-path}/Roboto-Thin.woff2") format("woff2");

    font-family: "Roboto";
    font-weight: 100;
}

@font-face {
    // This is parsed as a normal function call that takes an arithmetic
    // expression.
    src: url($roboto-font-path + "/Roboto-Light.woff2") format("woff2");

    font-family: "Roboto";
    font-weight: 300;
}

@font-face {
    // This is parsed as an interpolated special function.
    src: url(#{$roboto-font-path}/Roboto-Regular.woff2) format("woff2");

    font-family: "Roboto";
    font-weight: 400;
}
```

👉 Bu örnek, `url()` fonksiyonunun hem normal fonksiyon çağrısı hem de özel interpolasyonla nasıl ayrıştırıldığını göstermektedir.

### 🧩 `element()`, `progid:...()`, ve `expression()`

**Uyumluluk (calc()):**

* Dart Sass → `<1.40.0` sürümünden itibaren
* LibSass → ✗ desteklenmez
* Ruby Sass → ✗ desteklenmez

**Uyumluluk (clamp()):**

* Dart Sass → `>=1.31.0 <1.40.0` sürümlerinde

* LibSass → ✗ desteklenmez

* Ruby Sass → ✗ desteklenmez

* `element()` fonksiyonu CSS spesifikasyonunda tanımlıdır. Ancak ID’leri renkler gibi ayrıştırılabileceğinden özel ayrıştırma gerekir.

* `expression()` ve `progid:` ile başlayan fonksiyonlar eski **Internet Explorer** özellikleridir ve standart dışı sözdizimi kullanırlar. Artık modern tarayıcılar tarafından desteklenmiyor olsalar da, Sass geriye dönük uyumluluk için bunları ayrıştırmaya devam eder.

* Bu fonksiyon çağrılarında her türlü metne izin verilir (iç içe parantezler dahil). Hiçbir şey SassScript ifadesi olarak yorumlanmaz, yalnızca interpolasyon (`#{}`) ile dinamik değerler eklenebilir.

#### Örnek (SCSS):

```scss
$logo-element: logo-bg;

.logo {
  background: element(##{$logo-element});
}
```

👉 Bu örnek, `element()` fonksiyonunda interpolasyon ile Sass değişkeninin nasıl kullanılabileceğini göstermektedir.
