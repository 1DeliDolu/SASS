## 🎨 Stil Kuralları (style rules)

Stil kuralları (style rules), Sass’in temelini oluşturur; CSS’de olduğu gibi. Ve aynı şekilde çalışır: hangi elementlerin stilleneceğini bir seçici (selector) ile belirlersiniz ve bu elementlerin nasıl görüneceğini etkileyen özellikleri tanımlarsınız.

### SCSS Sözdizimi

```scss
.button {
  padding: 3px 10px;
  font-size: 12px;
  border-radius: 3px;
  border: 1px solid #e1e4e8;
}
```

👉 Bu örnek, `.button` sınıfına temel stil kuralları uygular.

---

## 🪆 İç İçe Yazım (nesting)

Sass, hayatınızı kolaylaştırmak ister. Aynı seçicileri tekrar tekrar yazmak yerine bir stil kuralını (style rule) başka bir kuralın içine yazabilirsiniz. Sass, dış kuralın seçicisini otomatik olarak iç kural ile birleştirir.

### SCSS Sözdizimi

```scss
nav {
  ul {
    margin: 0;
    padding: 0;
    list-style: none;
  }

  li { display: inline-block; }

  a {
    display: block;
    padding: 6px 12px;
    text-decoration: none;
  }
}
```

👉 Bu yapı, `nav` içindeki `ul`, `li` ve `a` öğelerini stilize eder.

⚠️ Dikkat!
İç içe kurallar oldukça faydalıdır, ancak üretilen CSS’in ne kadar büyük olduğunu görselleştirmeyi zorlaştırabilir. Ne kadar derine inerseniz, CSS’in sunulması için o kadar fazla bant genişliği gerekir ve tarayıcının işleme yükü artar. Seçicileri yüzeysel tutun!

---

## 📋 Seçici Listeleri (selector lists)

İç içe kurallar, seçici listeleri (virgülle ayrılmış seçiciler) işlerken akıllıdır. Her karmaşık seçici (virgüller arasındaki seçiciler) ayrı ayrı iç içe yerleştirilir ve ardından tekrar bir seçici listesine dönüştürülür.

### SCSS Sözdizimi

```scss
.alert, .warning {
  ul, p {
    margin-right: 0;
    margin-left: 0;
    padding-bottom: 0;
  }
}
```

👉 Bu örnek `.alert` ve `.warning` içindeki `ul` ve `p` öğelerini stiller.

---

## 🔗 Seçici Birleştiriciler (selector combinators)

Birleştiriciler (combinators) kullanan seçicileri de iç içe yazabilirsiniz. Birleştiriciyi dış seçicinin sonuna, iç seçicinin başına veya araya tek başına koyabilirsiniz.

### SCSS Sözdizimi

```scss
ul > {
  li {
    list-style-type: none;
  }
}

h2 {
  + p {
    border-top: 1px solid gray;
  }
}

p {
  ~ {
    span {
      opacity: 0.8;
    }
  }
}
```

👉 Bu yapı, `>`, `+` ve `~` birleştiricileri ile iç içe seçici kullanımını gösterir.

---

## ⚙️ Gelişmiş İç İçe Yazım (advanced nesting)

İç içe yazılmış stil kurallarıyla yalnızca alt seçici (descendant combinator – yani boşluk) kullanmak dışında daha fazlasını yapmak istiyorsanız, Sass size bu imkânı verir. Daha fazla bilgi için üst seçici (parent selector) belgelerine bakınız.

---

## 🔀 Aradeğer Kullanımı (interpolation)

Aradeğer (interpolation), değişkenler ve fonksiyon çağrıları gibi ifadelerden değerleri seçicilere enjekte etmenizi sağlar. Bu, özellikle mixin yazarken kullanışlıdır çünkü kullanıcıların parametre olarak ilettiği değerlerden seçiciler oluşturmanıza olanak tanır.

### SCSS Sözdizimi

```scss
@mixin define-emoji($name, $glyph) {
  span.emoji-#{$name} {
    font-family: IconFont;
    font-variant: normal;
    font-weight: normal;
    content: $glyph;
  }
}

@include define-emoji("women-holding-hands", "👭");
```

👉 Bu örnek, aradeğer kullanarak `emoji` sınıfları oluşturur.

💡 İlginç bilgi:
Sass, seçicileri yalnızca aradeğer çözüldükten sonra işler. Bu, seçicinin herhangi bir bölümünü güvenle aradeğer ile üretmenizi sağlar.

Aradeğer, üst seçici `&`, `@at-root` kuralı ve seçici fonksiyonlarıyla birleştirilerek dinamik olarak güçlü seçiciler üretmek için kullanılabilir. Daha fazla bilgi için üst seçici belgelerine bakınız.
