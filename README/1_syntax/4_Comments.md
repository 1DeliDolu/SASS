## 💬 Yorumlar (Comments)

Sass yorumlarının çalışma şekli, SCSS ve girintili sözdiziminde (indented syntax) önemli ölçüde farklıdır. Her iki sözdizimi de iki tür yorumu destekler:

* `/* */` ile tanımlanan yorumlar → (genellikle) CSS’e derlenir.
* `//` ile tanımlanan yorumlar → CSS’e derlenmez.

### 📄 SCSS’te (In SCSS)

SCSS’te yorumlar, JavaScript gibi diğer dillerdeki yorumlara benzer şekilde çalışır:

* **Tek satırlık yorumlar** `//` ile başlar ve satır sonuna kadar devam eder. CSS’e aktarılmazlar → sessiz yorumlar (silent comments) olarak adlandırılır.
* **Çok satırlı yorumlar** `/*` ile başlar ve `*/` ile biter. Eğer deyim yazılabilecek bir yerde bulunuyorsa CSS’e derlenir → bunlara gürültülü yorumlar (loud comments) denir.
* Çok satırlı yorumlar interpolasyon içerebilir (`#{}`), derlenmeden önce değerlendirilir.
* Varsayılan olarak, sıkıştırılmış (compressed) modda çok satırlı yorumlar kaldırılır. Ancak `/*!` ile başlıyorsa CSS çıktısında her zaman yer alır.

#### Örnek (SCSS):

```scss
// This comment won't be included in the CSS.

/* But this comment will, except in compressed mode. */

/* It can also contain interpolation:
* 1 + 1 = #{1 + 1} */

/*! This comment will be included even in compressed mode. */

p /* Multi-line comments can be written anywhere
  * whitespace is allowed. */ .sans {
  font: Helvetica, // So can single-line comments.
        sans-serif;
}
```

👉 Bu örnekte sessiz (`//`) ve gürültülü (`/* */`) yorumların SCSS çıktısındaki davranışları gösterilmektedir.

### 🔹 Sass’ta (In Sass – Girintili Sözdizimi)

Girintili sözdiziminde yorumlar da girintiye bağlıdır:

* `//` ile yazılan sessiz yorumlar CSS’e aktarılmaz. SCSS’ten farklı olarak, `//` altında girintili olan her şey de yorum sayılır.
* `/*` ile başlayan yorumlar girinti ile çalışır ve CSS’e derlenir. Girintiye dayalı oldukları için `*/` kapanışı opsiyoneldir.
* `/*!` ile başlayan yorumlar, sıkıştırılmış modda bile CSS’te yer alır.
* `/* */` yorumları interpolasyon içerebilir.
* Yorumlar ifadeler içinde de kullanılabilir, SCSS’tekiyle aynı sözdizimi geçerlidir.

#### Örnek (Sass – .sass):

```sass
// This comment won't be included in the CSS.
  This is also commented out.

/* But this comment will, except in compressed mode.

/* It can also contain interpolation:
  1 + 1 = #{1 + 1}

/*! This comment will be included even in compressed mode.

p .sans
  font: Helvetica, /* Inline comments must be closed. */ sans-serif
```

👉 Bu örnek, girintili sözdiziminde yorumların nasıl çalıştığını göstermektedir.

### 📚 Dokümantasyon Yorumları (Documentation Comments)

Sass ile stil kütüphaneleri yazarken, sağladığınız mixin’leri, fonksiyonları, değişkenleri ve placeholder seçicileri belgelemede yorumları kullanabilirsiniz. Bu yorumlar **SassDoc** aracı tarafından okunur ve otomatik dokümantasyon oluşturulur.

* Dokümantasyon yorumları sessiz yorumlardır.
* Üç eğik çizgi `///` ile başlar ve belgelenen öğenin hemen üstüne yazılır.
* SassDoc yorumlardaki metni **Markdown** olarak işler.
* Detaylı açıklama için ek açıklamalar (annotations) destekler.

#### Örnek (SCSS – SassDoc):

```scss
/// Computes an exponent.
///
/// @param {number} $base
///   The number to multiply by itself.
/// @param {integer (unitless)} $exponent
///   The number of `$base`s to multiply together.
/// @return {number} `$base` to the power of `$exponent`.
@function pow($base, $exponent) {
  $result: 1;
  @for $_ from 1 through $exponent {
    $result: $result * $base;
  }
  @return $result;
}
```

👉 Bu örnek, SassDoc için dokümantasyon yorumlarının nasıl yazıldığını göstermektedir.
