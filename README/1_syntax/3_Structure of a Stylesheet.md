## 📂 Bir Stil Sayfasının Yapısı (Structure of a Stylesheet)

CSS’te olduğu gibi, çoğu Sass stil sayfası özellik bildirimleri (property declarations) içeren stil kurallarından (style rules) oluşur. Ancak Sass stil sayfaları bunun yanında birçok ek özellikle birlikte gelir.

### 📜 Deyimler (Statements)

Bir Sass stil sayfası, sonuçta oluşacak CSS’i oluşturmak için sırayla değerlendirilen bir dizi deyimden oluşur. Bazı deyimlerin `{}` ile tanımlanan blokları olabilir ve bu bloklar başka deyimler içerir. Örneğin, bir stil kuralı bir blok içeren bir deyimdir ve bu blok, özellik bildirimleri gibi başka deyimler içerir.

* SCSS’te deyimler `;` ile ayrılır (blok kullanan deyimlerde `;` opsiyoneldir).
* Girintili sözdiziminde (indented syntax) deyimler satır sonlarıyla ayrılır. İsterseniz satır sonunda `;` kullanabilirsiniz, ancak satır sonu yine de gereklidir.

### 🌍 Evrensel Deyimler (Universal Statements)

Bu tür deyimler, bir Sass stil sayfasında her yerde kullanılabilir:

* Değişken bildirimleri → `$var: value`
* Akış kontrol `@` kuralları → `@if`, `@each`
* `@error`, `@warn`, `@debug` kuralları

### 🎨 CSS Deyimleri (CSS Statements)

Bu deyimler CSS üretir. `@function` içinde kullanılamazlar:

* Stil kuralları → `h1 { /* ... */ }`
* CSS `@` kuralları → `@media`, `@font-face`
* `@include` ile mixin kullanımları
* `@at-root` kuralı

### 🔝 Üst Düzey Deyimler (Top-Level Statements)

Bu deyimler yalnızca bir stil sayfasının en üst düzeyinde veya üst düzeydeki bir CSS deyiminin içinde kullanılabilir:

* Modül yüklemeleri → `@use`
* İçe aktarmalar → `@import`
* Mixin tanımları → `@mixin`
* Fonksiyon tanımları → `@function`

### 📑 Diğer Deyimler (Other Statements)

* Özellik bildirimleri → `width: 100px` yalnızca stil kuralları ve bazı CSS `@` kuralları içinde kullanılabilir.
* `@extend` kuralı yalnızca stil kuralları içinde kullanılabilir.

### 🔢 İfadeler (Expressions)

Bir ifade (expression), bir özellik veya değişken bildiriminin sağ tarafında yer alan her şeydir. Her ifade bir değer üretir.

* Tüm geçerli CSS değerleri Sass ifadeleri olabilir.
* Sass ifadeleri CSS değerlerinden çok daha güçlüdür:

  * Mixin ve fonksiyonlara argüman olarak verilebilir.
  * `@if` ile akış kontrolünde kullanılabilir.
  * Aritmetik işlemlerle değiştirilebilir.

Sass’ın ifade sözdizimine **SassScript** denir.

### 🧩 Sabit Değerler (Literals)

En basit ifadeler sabit değerlerdir:

* Sayılar (birimle veya birimsiz) → `12`, `100px`
* Dizeler (tırnaklı veya tırnaksız) → `"Helvetica Neue"`, `bold`
* Renkler → `#c6538c`, `blue`
* Mantıksal değerler → `true`, `false`
* Tekil değer → `null`
* Liste değerleri (boşluk veya virgülle ayrılmış, köşeli parantezli veya parantezsiz) → `1.5em 1em 0 2em`, `Helvetica, Arial, sans-serif`, `[col1-start]`
* Haritalar (anahtar-değer eşleşmeleri) → `("background": red, "foreground": pink)`

### ➕ İşlemler (Operations)

Sass birçok işlem için sözdizimi tanımlar:

* `==`, `!=` → iki değerin aynı olup olmadığını kontrol eder.
* `+`, `-`, `*`, `/`, `%` → sayılar üzerinde matematiksel işlemler, birimlerle bilimsel matematik mantığına uygun çalışır.
* `<`, `<=`, `>`, `>=` → iki sayıyı karşılaştırır.
* `and`, `or`, `not` → mantıksal işlemler. Sass’ta `false` ve `null` dışında her değer “true” kabul edilir.
* `+`, `-`, `/` → dizeleri birleştirmek için de kullanılabilir.
* `( )` → işlem önceliğini belirlemek için kullanılır.

### 🔍 Diğer İfadeler (Other Expressions)

* Değişkenler → `$var`
* Fonksiyon çağrıları → `nth($list, 1)`, `var(--main-bg-color)`

  * Sass çekirdek kütüphanesi fonksiyonları veya kullanıcı tanımlı fonksiyonlar olabilir.
  * CSS’e doğrudan derlenebilir.
* Özel fonksiyonlar → `calc(1px + 100%)`, `url(http://myapp.com/assets/logo.png)`
* Ebeveyn seçici → `&`
* `!important` → tırnaksız bir dize olarak ayrıştırılır.
