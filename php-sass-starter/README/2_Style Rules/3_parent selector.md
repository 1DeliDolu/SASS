## 👤 Üst Seçici (parent selector)

Üst seçici (`&`), Sass tarafından icat edilmiş özel bir seçicidir. İç içe seçicilerde (nested selectors) dış seçiciye referans vermek için kullanılır. Bu sayede, dış seçici yeniden kullanılabilir; örneğin bir pseudo-class eklemek veya üst seçicinin önüne başka bir seçici eklemek mümkündür.

Bir iç seçicide üst seçici (`&`) kullanıldığında, normal iç içe yazım davranışı yerine ilgili dış seçiciyle değiştirilir.

### SCSS Sözdizimi

```scss
.alert {
  // Üst seçici dış seçiciye pseudo-class eklemek için kullanılabilir.
  &:hover {
    font-weight: bold;
  }

  // Belirli bir bağlamda dış seçiciyi stillendirmek için de kullanılabilir,
  // örneğin sağdan sola dil kullanan bir body içinde.
  [dir=rtl] & {
    margin-left: 0;
    margin-right: 10px;
  }

  // Hatta pseudo-class seçicilerine argüman olarak da kullanılabilir.
  :not(&) {
    opacity: 0.8;
  }
}
```

👉 Bu örnekte `&`, `.alert` seçicisini farklı bağlamlarda yeniden kullanır.

⚠️ Dikkat!
Üst seçici bir tip seçici (örneğin `h1`) ile değiştirilebileceği için, yalnızca bileşik seçicilerin (compound selectors) başında kullanılmasına izin verilir. Örneğin `span&` geçersizdir.

---

## ➕ Sonek Ekleme (adding suffixes)

Üst seçici, dış seçiciye ek sonekler eklemek için de kullanılabilir. Bu yöntem özellikle BEM gibi yapılandırılmış sınıf isimlendirme metodolojilerinde faydalıdır. Dış seçici alfabetik bir isim (sınıf, ID, element seçici) ile bitiyorsa, üst seçiciye ek metin eklenebilir.

### SCSS Sözdizimi

```scss
.accordion {
  max-width: 600px;
  margin: 4rem auto;
  width: 90%;
  font-family: "Raleway", sans-serif;
  background: #f4f4f4;

  &__copy {
    display: none;
    padding: 1rem 1.5rem 2rem 1.5rem;
    color: gray;
    line-height: 1.6;
    font-size: 14px;
    font-weight: 500;

    &--open {
      display: block;
    }
  }
}
```

👉 Bu örnek, `.accordion__copy` ve `.accordion__copy--open` gibi BEM sınıflarını üretir.

---

## 🧩 SassScript İçinde (in SassScript)

Üst seçici (`&`) SassScript içinde de kullanılabilir. Bu, geçerli üst seçiciyi döndüren özel bir ifadedir. Formatı, seçici fonksiyonlarının kullandığı biçimde olur:

* Virgülle ayrılmış bir liste (seçici listesi),
* Boşlukla ayrılmış alt listeler (karmaşık seçiciler),
* Tırnaksız stringler (bileşik seçiciler).

### SCSS Sözdizimi

```scss
.main aside:hover,
.sidebar p {
  parent-selector: &;
  // => ((unquote(".main") unquote("aside:hover")),
  //     (unquote(".sidebar") unquote("p")))
}
```

👉 Bu örnek, `&` ifadesinin üst seçiciyi nasıl temsil ettiğini gösterir.

Eğer `&` ifadesi herhangi bir stil kuralının dışında kullanılırsa, `null` döner. `null` sahte (falsey) olduğu için, bununla bir mixin’in stil kuralı içinde çağrılıp çağrılmadığını kolayca kontrol edebilirsiniz.

### SCSS Sözdizimi

```scss
@mixin app-background($color) {
  #{if(&, '&.app-background', '.app-background')} {
    background-color: $color;
    color: rgba(#fff, 0.75);
  }
}

@include app-background(#036);

.sidebar {
  @include app-background(#c6538c);
}
```

👉 Bu örnekte mixin, bulunduğu bağlama göre farklı seçiciler üretir.

---

## ⚙️ Gelişmiş İç İçe Yazım (advanced nesting)

`&` normal bir SassScript ifadesi olarak kullanılabilir; yani fonksiyonlara aktarılabilir veya interpolation içinde kullanılabilir — hatta başka seçicilerin içinde bile! Bunu seçici fonksiyonları ve `@at-root` kuralı ile birleştirerek oldukça güçlü iç içe seçici yapıları oluşturabilirsiniz.

Örneğin, dış seçici ile bir element seçiciyi eşleştiren bir seçici yazmak isteyebilirsiniz. Bunun için, `selector.unify()` fonksiyonunu kullanarak `&` ile kullanıcının seçicisini birleştiren bir mixin yazabilirsiniz.

### SCSS Sözdizimi

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

👉 Bu örnek, `&` ve `@at-root` kullanarak güçlü birleştirilmiş seçiciler üretir.

⚠️ Dikkat!
Sass iç içe seçicileri işlerken, bunların interpolation ile üretilip üretilmediğini bilmez. Bu nedenle, `&` SassScript ifadesi olarak kullanılsa bile otomatik olarak dış seçiciyi ekler. Bu yüzden Sass’a dış seçiciyi dahil etmemesini açıkça belirtmek için `@at-root` kuralını kullanmanız gerekir.
