## 🚫 `null`

`null`, kendi türünün tek değeridir. **Değerin yokluğunu** temsil eder ve genellikle bir fonksiyonun sonuç bulamadığını göstermek için döndürülür.

### SCSS Sözdizimi

```scss
@use "sass:map";
@use "sass:string";

@debug string.index("Helvetica Neue", "Roboto"); // null
@debug map.get(("large": 20px), "small"); // null
@debug &; // null
```

---

## 📝 Listelerde `null`

Bir liste `null` içeriyorsa, bu `null` değer oluşturulan CSS çıktısına **dahil edilmez**.

```scss
$fonts: ("serif": "Helvetica Neue", "monospace": "Consolas");

h3 {
  font: 18px bold map-get($fonts, "sans");
}
```

👉 Burada `map-get($fonts, "sans")` → `null`, dolayısıyla `font` özelliğine eklenmez.

---

## 📝 Özellik Değerlerinde `null`

Bir CSS özelliğinin değeri `null` ise, o özellik çıktıya **hiç eklenmez**.

```scss
$fonts: ("serif": "Helvetica Neue", "monospace": "Consolas");

h3 {
  font: {
    size: 18px;
    weight: bold;
    family: map-get($fonts, "sans");
  }
}
```

👉 `family` değeri `null` olduğundan CSS çıktısında yer almaz.

---

## ✅ Booleans ile Kullanımı

`null`, **falsey** kabul edilir, yani mantıksal işlemlerde `false` gibi davranır.
Bu sayede `@if` ve `if()` koşullarında kolayca kullanılabilir.

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

👉 `if(&, ...)` ifadesinde `&` `null` dönerse, alternatif değer (`.app-background`) seçilir.
