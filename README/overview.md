## 📝 Sözdizimi (Syntax)

Sass iki farklı sözdizimini (syntax) destekler. Her biri diğerini yükleyebilir, bu nedenle hangisini seçeceğiniz size ve ekibinize bağlıdır.

### 📄 SCSS (SCSS)

SCSS sözdizimi `.scss` dosya uzantısını kullanır. Küçük birkaç istisna dışında CSS’in üst kümesidir (superset), yani geçerli tüm CSS aynı zamanda geçerli SCSS’tir. CSS’e çok benzer olduğundan alışması en kolay ve en popüler sözdizimidir.

SCSS şu şekilde görünür:

```
@mixin button-base() {
  @include typography(button);
  @include ripple-surface;
  @include ripple-radius-bounded;

  display: inline-flex;
  position: relative;
  height: $button-height;
  border: none;
  vertical-align: middle;

  &:hover {
    cursor: pointer;
  }

  &:disabled {
    color: $mdc-button-disabled-ink-color;
    cursor: default;
    pointer-events: none;
  }
}
```

👉 Bu örnek, SCSS sözdiziminin süslü parantezler `{}` ve noktalı virgüller `;` ile nasıl çalıştığını gösterir.

### 🔹 Girintili Sözdizimi (The Indented Syntax)

Girintili sözdizimi Sass’ın orijinal sözdizimidir ve `.sass` dosya uzantısını kullanır. Bu nedenle bazen sadece “Sass” olarak adlandırılır. SCSS ile aynı özellikleri destekler, ancak belge biçimini tanımlamak için süslü parantezler `{}` ve noktalı virgüller `;` yerine girinti (indentation) kullanır.

Genel kural olarak:

* CSS veya SCSS’de süslü parantez açtığınız her yerde girintili sözdiziminde bir seviye daha içeri girersiniz.
* Bir satır, bir ifadenin bitebileceği yerde biterse bu, noktalı virgül `;` yerine geçer.

Girintili sözdizimi şu şekilde görünür:

```
@mixin button-base()
  @include typography(button)
  @include ripple-surface
  @include ripple-radius-bounded

  display: inline-flex
  position: relative
  height: $button-height
  border: none
  vertical-align: middle

  &:hover
    cursor: pointer

  &:disabled
    color: $mdc-button-disabled-ink-color
    cursor: default
    pointer-events: none
```

👉 Bu örnek, `.sass` sözdiziminde süslü parantez `{}` ve noktalı virgül `;` yerine girintinin kullanıldığını göstermektedir.

### 📑 Çok Satırlı İfadeler (Multiline statements)

Uyumluluk:

* Dart Sass → 1.84.0’dan itibaren desteklenir
* LibSass → ✗ desteklenmez
* Ruby Sass → ✗ desteklenmez

Girintili sözdiziminde, satır sonu ifadeyi bitiremeyeceği yerlerde olduğu sürece ifadeler birden fazla satıra yayılabilir. Bu durum şu yerlerde geçerlidir:

* Parantezler veya diğer köşeli ayraçlar içinde
* Sass’a özgü `@` kurallarındaki anahtar kelimeler arasında

Örnek:

```
.grid
  display: grid
  grid-template: (
    "header" min-content
    "main" 1fr
  )

@for 
  $i from 
  1 through 3
    ul:nth-child(3n + #{$i})
      margin-left: $i * 10
```

👉 Bu örnek, girintili sözdiziminde çok satırlı ifadelerin nasıl yazılabileceğini göstermektedir.
