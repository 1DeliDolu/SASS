## 📜 At-Kuralları (At-Rules)

Sass’ın ek işlevselliğinin büyük bir kısmı, CSS’in üzerine eklediği yeni **at-kuralları (at-rules)** ile gelir:

* `@use` → Diğer Sass stil sayfalarından mixinler, fonksiyonlar ve değişkenler yükler ve birden fazla stil sayfasındaki CSS’i birleştirir.
* `@forward` → Bir Sass stil sayfasını yükler ve onun mixinlerini, fonksiyonlarını ve değişkenlerini, stil sayfanız `@use` ile yüklendiğinde kullanılabilir hale getirir.
* `@import` → CSS’in `@import` kuralını genişleterek diğer stil sayfalarından stiller, mixinler, fonksiyonlar ve değişkenler yükler.
* `@mixin` ve `@include` → Stil parçalarının yeniden kullanılmasını kolaylaştırır.
* `@function` → SassScript ifadelerinde kullanılabilecek özel fonksiyonlar tanımlar.
* `@extend` → Seçicilerin (selectors) birbirinden stilleri miras almasını sağlar.
* `@at-root` → İçerdiği stilleri CSS belgesinin kök seviyesine yerleştirir.
* `@error` → Derlemeyi bir hata mesajı ile durdurur.
* `@warn` → Derlemeyi tamamen durdurmadan bir uyarı mesajı yazdırır.
* `@debug` → Hata ayıklama amacıyla bir mesaj yazdırır.
* Akış kontrol kuralları (`@if`, `@each`, `@for`, `@while`) → Hangi stillerin veya kaç kez üretileceğini kontrol eder.

Sass ayrıca düz CSS at-kuralları (plain CSS at-rules) için de özel davranışlara sahiptir:

* Enterpolasyon (interpolation) içerebilirler.
* Stil kuralları (style rules) içinde iç içe (nested) yazılabilirler.
* Bazıları (örneğin `@media` ve `@supports`) enterpolasyona gerek olmadan doğrudan SassScript kullanılmasına izin verir.
