## 🛠️ @error

Karışımlar (mixin) ve fonksiyonlar (function) argümanlar (arguments) aldığında, bu argümanların API’nizin (API) beklediği türlere (types) ve biçimlere (formats) sahip olduğundan emin olmak istersiniz. Aksi halde kullanıcı bilgilendirilmeli ve karışımınız/fonksiyonunuzun çalışması durdurulmalıdır.

Sass bunu `@error` kuralı (rule) ile kolaylaştırır; yazımı `@error <ifade>` (expression) şeklindedir. Bu, ifadenin (genellikle bir string) değerini, mevcut karışımın ya da fonksiyonun nasıl çağrıldığına dair bir yığın izi (stack trace) ile birlikte yazdırır. Hata yazdırıldıktan sonra Sass stil sayfasını (stylesheet) derlemeyi (compiling) durdurur ve bunu çalıştıran sisteme bir hata oluştuğunu bildirir.

SCSSSass
SassPlayground
SCSS Syntax

```scss
@mixin reflexive-position($property, $value) {
  @if $property != left and $property != right {
    @error "Property #{$property} must be either left or right.";
  }

  $left-value: if($property == right, initial, $value);
  $right-value: if($property == right, $value, initial);

  left: $left-value;
  right: $right-value;
  [dir=rtl] & {
    left: $right-value;
    right: $left-value;
  }
}

.sidebar {
  @include reflexive-position(top, 12px);
  //       ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
  // Error: Property top must be either left or right.
}
```

Hata ve yığın izi (stack trace) için tam biçim, uygulamadan (implementation) uygulamaya değişir ve derleme sistemi (build system) yapılandırmanıza da bağlı olabilir. Komut satırından (command line) çalıştırıldığında Dart Sass’ta şöyle görünür:

```
Error: "Property top must be either left or right."
  ╷
3 │     @error "Property #{$property} must be either left or right.";
  │     ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
  ╵
  example.scss 3:5   reflexive-position()
  example.scss 19:3  root stylesheet
```
