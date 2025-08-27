## 🌳 @at-root

`@at-root` kuralı (rule) genellikle `@at-root <seçici> { ... }` şeklinde yazılır ve içindeki her şeyi normal iç içe geçme (nesting) yerine belgenin köküne (root) yerleştirir. Bu kural çoğunlukla SassScript ebeveyn seçici (&) ve seçici fonksiyonları (selector functions) ile gelişmiş iç içe yazım yaparken kullanılır.

Örneğin, dış seçici ile bir element seçicisini eşleştiren bir seçici yazmak istediğinizi varsayalım. Bunun için, `selector.unify()` fonksiyonunu kullanarak `&` ile kullanıcının seçicisini birleştiren bir karışım (mixin) tanımlayabilirsiniz.

SCSSSassCSS
SassPlayground
SCSS Syntax

```scss
@use "sass:selector";

@mixin unify-parent($child) {
  @at-root #{selector.unify(&, $child)} {
    @content;
  }
}

.wrapper .field {
  @include unify-parent("input") {
    /* ... */
  }
  @include unify-parent("select") {
    /* ... */
  }
}
```

👉 Bu örnekte `@at-root`, dış seçicinin otomatik olarak eklenmesini engelleyerek doğru birleşimi sağlar.

Sass, seçici iç içe yazımı (selector nesting) sırasında hangi interpolasyonun kullanıldığını bilmediği için normalde dış seçiciyi iç seçiciye ekler. `@at-root`, Sass’a açıkça dış seçiciyi dahil etmemesini söyler (ancak `&` ifadesinin kendisinde her zaman bulunur).

💡 İlginç bilgi:
`@at-root` kuralı `@at-root { ... }` şeklinde de yazılabilir. Bu biçim, birden fazla stil kuralını belgenin köküne yerleştirir. Aslında `@at-root <seçici> { ... }` sadece `@at-root { <seçici> { ... } }` ifadesinin kısaltmasıdır!

---

## 📜 Stil Kurallarının Ötesinde (Beyond Style Rules)

Tek başına `@at-root`, yalnızca stil kurallarını kaldırır. `@media` veya `@supports` gibi at-kurallar (at-rules) olduğu gibi bırakılır. Ancak bu davranışı değiştirmek istiyorsanız, hangi kuralların dahil edileceğini veya hariç tutulacağını `@at-root (with: <kurallar...>) { ... }` veya `@at-root (without: <kurallar...>) { ... }` şeklinde kontrol edebilirsiniz.

* `(without: ...)` → Sass’a hangi kuralların hariç tutulacağını söyler.
* `(with: ...)` → Listelenenler dışındaki tüm kuralları hariç tutar.

SCSSSassCSS
SassPlayground
SCSS Syntax

```scss
@media print {
  .page {
    width: 8in;

    @at-root (without: media) {
      color: #111;
    }

    @at-root (with: rule) {
      font-size: 1.2em;
    }
  }
}
```

👉 Bu örnekte `color` özelliği kök seviyeye taşınırken `media` kuralı hariç tutulur.

Ek olarak, sorgularda kullanılabilecek iki özel değer vardır:

* `rule` → stil kurallarına işaret eder. Örneğin `@at-root (with: rule)`, tüm at-kuralları hariç tutar ama stil kurallarını korur.
* `all` → tüm at-kuralları ve stil kurallarını hariç tutar.
