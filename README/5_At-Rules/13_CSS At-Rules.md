## 📜 CSS At-Rules (CSS At-Kuralları)

### 💡 Genel Açıklama

Sass, CSS’in bir parçası olan tüm `at-rule` kurallarını destekler. Gelecekteki CSS sürümleriyle esnek ve ileriye dönük uyumluluk sağlamak için Sass, varsayılan olarak neredeyse tüm `at-rule` kurallarını kapsayan genel bir desteğe sahiptir.

Bir CSS `at-rule`, şu şekillerde yazılır:

* `@<name> <value>`
* `@<name> { ... }`
* `@<name> <value> { ... }`

Burada `name` bir tanımlayıcıdır, `value` (varsa) hemen hemen her şey olabilir. Hem `name` hem de `value` interpolasyon içerebilir.

### SCSS Sözdizimi

```scss
@namespace svg url(http://www.w3.org/2000/svg);

@font-face {
  font-family: "Open Sans";
  src: url("/fonts/OpenSans-Regular-webfont.woff2") format("woff2");
}

@counter-style thumbs {
  system: cyclic;
  symbols: "\1F44D";
}
```

👉 Burada `@namespace`, `@font-face` ve `@counter-style` kuralları örneklenmiştir.

---

### 📂 İç İçe Yazım (Nesting)

Eğer bir CSS `at-rule`, bir stil kuralı içinde yer alıyorsa, ikisi otomatik olarak yer değiştirir; yani `at-rule` CSS çıktısında en üst seviyeye alınır ve stil kuralı onun içine yerleştirilir. Bu sayede, stil kuralının seçicisini yeniden yazmadan koşullu stiller eklemek kolaylaşır.

### SCSS Sözdizimi

```scss
.print-only {
  display: none;

  @media print { display: block; }
}
```

👉 `.print-only` sınıfı yalnızca yazdırma sırasında görünür hale gelir.

---

## 📺 @media

`@media` kuralı tüm yukarıdakileri yapmanın yanı sıra daha fazlasını da sunar. İnterpolasyonun yanı sıra SassScript ifadelerinin doğrudan özellik sorgularında kullanılmasına izin verir.

### SCSS Sözdizimi

```scss
$layout-breakpoint-small: 960px;

@media (min-width: $layout-breakpoint-small) {
  .hide-extra-small {
    display: none;
  }
}
```

👉 Burada medya sorgusu değişkenle (`$layout-breakpoint-small`) tanımlanmıştır.

Ayrıca Sass, iç içe geçmiş medya sorgularını birleştirerek, tarayıcıların yerel olarak iç içe `@media` kurallarını desteklemediği durumlarda uyumluluğu artırır.

### SCSS Sözdizimi

```scss
@media (hover: hover) {
  .button:hover {
    border: 2px solid black;

    @media (color) {
      border-color: #036;
    }
  }
}
```

👉 Sass bu iki medya sorgusunu birleştirerek çıktı alır.

---

## ✅ @supports

`@supports` kuralı da bildirim sorgularında SassScript ifadelerinin kullanılmasına izin verir.

### SCSS Sözdizimi

```scss
@mixin sticky-position {
  position: fixed;
  @supports (position: sticky) {
    position: sticky;
  }
}

.banner {
  @include sticky-position;
}
```

👉 Bu örnekte tarayıcı `position: sticky` destekliyorsa, ilgili stil uygulanır.

---

## 🎞️ @keyframes

`@keyframes` kuralı da genel bir `at-rule` gibi çalışır, ancak alt kuralları normal seçiciler yerine yalnızca geçerli keyframe kuralları (`<number>%`, `from`, `to`) olmalıdır.

### SCSS Sözdizimi

```scss
@keyframes slide-in {
  from {
    margin-left: 100%;
    width: 300%;
  }

  70% {
    margin-left: 90%;
    width: 150%;
  }

  to {
    margin-left: 0%;
    width: 100%;
  }
}
```

👉 Bu örnekte `slide-in` animasyonu soldan kayarak giriş hareketi tanımlar.
