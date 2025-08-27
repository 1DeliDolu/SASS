## 🐞 @debug

Bazen stil sayfanızı (stylesheet) geliştirirken bir değişkenin (variable) veya ifadenin (expression) değerini görmek faydalı olabilir. İşte bunun için `@debug` kuralı (rule) vardır: Yazımı `@debug <ifade>` şeklindedir ve ifadenin değerini, dosya adı (filename) ve satır numarasıyla birlikte yazdırır.

SCSSSass
SassPlayground
SCSS Syntax

```scss
@mixin inset-divider-offset($offset, $padding) {
  $divider-offset: (2 * $padding) + $offset;
  @debug "divider offset: #{$divider-offset}";

  margin-left: $divider-offset;
  width: calc(100% - #{$divider-offset});
}
```

👉 Bu örnekte, `@debug` kullanılarak hesaplanan kenar boşluğu değeri çıktıya yazdırılır.

Uyarı mesajının (debug message) tam biçimi uygulamadan (implementation) uygulamaya değişir. Dart Sass’ta şu şekilde görünür:

```
test.scss:3 Debug: divider offset: 132px
```

💡 İlginç bilgi:
`@debug` kuralına yalnızca string değil, herhangi bir değer gönderebilirsiniz! Bu değer, `meta.inspect()` fonksiyonunun yazdırdığıyla aynı biçimde gösterilir.

