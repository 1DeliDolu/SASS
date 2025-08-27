## 🧩 Mixin Değerleri (Mixin Values)

Mixin’ler de değer olabilir!
Bir mixin’i doğrudan değer olarak yazamazsınız, ancak `meta.get-mixin()` fonksiyonu ile bir mixin’in adını alarak onu değer haline getirebilirsiniz.
Elde edilen mixin değeri, `meta.apply()` ile çağrılabilir.

Bu yaklaşım, kütüphaneleri **esnek ve güçlü biçimde genişletilebilir** hale getirmek için kullanılır.

---

### SCSS Sözdizimi

```scss
@use "sass:meta";
@use "sass:string";

/// $list içindeki her elemanı ayrı bir $mixin çağrısına iletir.
@mixin apply-to-all($mixin, $list) {
  @each $element in $list {
    @include meta.apply($mixin, $element);
  }
}

@mixin font-class($size) {
  .font-#{$size} {
    font-size: $size;
  }
}

$sizes: [8px, 12px, 2rem];

@include apply-to-all(meta.get-mixin("font-class"), $sizes);
```

👉 Bu örnekte:

* `font-class` mixin’i, verilen boyuta göre `.font-<size>` sınıfı oluşturur.
* `apply-to-all`, listedeki tüm değerleri mixin’e uygular.
* Sonuçta `.font-8px`, `.font-12px`, `.font-2rem` sınıfları üretilir.
