## ⚠️ @warn

Karışımlar (mixin) ve fonksiyonlar (function) yazarken, kullanıcıların belirli argümanları (arguments) veya belirli değerleri geçmesini caydırmak isteyebilirsiniz. Kullanıcılar artık kullanımdan kaldırılmış (deprecated) eski argümanları geçiyor olabilir ya da API’nizi (API) çok da optimal olmayan bir şekilde çağırıyor olabilirler.

`@warn` kuralı (rule) bunun için tasarlanmıştır. Yazımı `@warn <ifade>` (expression) şeklindedir ve ifadenin değerini (genellikle bir string) kullanıcıya, mevcut karışımın ya da fonksiyonun nasıl çağrıldığına dair bir yığın izi (stack trace) ile birlikte yazdırır. Ancak `@error` kuralından (rule) farklı olarak, Sass derlemeyi tamamen durdurmaz.

SCSSSassCSS
SassPlayground
SCSS Syntax

```scss
$known-prefixes: webkit, moz, ms, o;

@mixin prefix($property, $value, $prefixes) {
  @each $prefix in $prefixes {
    @if not index($known-prefixes, $prefix) {
      @warn "Unknown prefix #{$prefix}.";
    }

    -#{$prefix}-#{$property}: $value;
  }
  #{$property}: $value;
}

.tilt {
  // Oops, we typo'd "webkit" as "wekbit"!
  @include prefix(transform, rotate(15deg), wekbit ms);
}
```

Uyarı (warning) ve yığın izi (stack trace) için tam biçim, uygulamadan (implementation) uygulamaya değişir. Dart Sass’ta şöyle görünür:

```
Warning: Unknown prefix wekbit.
    example.scss 6:7   prefix()
    example.scss 16:3  root stylesheet
```
