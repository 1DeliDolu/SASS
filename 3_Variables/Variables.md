## 📝 Değişkenler (Variables)

Sass değişkenleri (variables) basittir: `$` ile başlayan bir ada bir değer atarsınız ve ardından değerin kendisi yerine bu adı kullanabilirsiniz. Basit olmalarına rağmen, Sass’ın sunduğu en yararlı araçlardan biridir. Değişkenler, tekrarları azaltmayı, karmaşık matematiksel işlemler yapmayı, kütüphaneleri yapılandırmayı ve çok daha fazlasını mümkün kılar.

Bir değişken bildirimi, özellik bildirimine çok benzer: `<variable>: <expression>` şeklinde yazılır. Ancak, bir özellik yalnızca bir stil kuralı (style rule) veya at-rule içinde bildirilebilirken, değişkenler istediğiniz yerde bildirilebilir. Bir değişkeni kullanmak için, onu bir değere dahil etmeniz yeterlidir.

```scss
$base-color: #c6538c;
$border-dark: rgba($base-color, 0.88);

.alert {
  border: 1px solid $border-dark;
}
```

👉 Bu örnek, bir değişkene renk atamayı ve başka bir değişkende bu rengi kullanmayı gösterir.

⚠️ Dikkat!
CSS’in kendi değişkenleri vardır ve bunlar Sass değişkenlerinden tamamen farklıdır. Farkları bilmek önemlidir!

* Sass değişkenleri Sass tarafından tamamen derlenir ve CSS çıktısında yer almaz.
* CSS değişkenleri CSS çıktısına dahil edilir.
* CSS değişkenleri farklı öğeler için farklı değerlere sahip olabilir, ancak Sass değişkenleri aynı anda yalnızca tek bir değere sahiptir.
* Sass değişkenleri emredicidir (imperative): Bir değişkeni kullandıktan sonra değerini değiştirirseniz, önceki kullanım aynı kalır. CSS değişkenleri bildirimseldir (declarative): Değerini değiştirirseniz, bu hem önceki hem de sonraki kullanımları etkiler.

```scss
$variable: value 1;
.rule-1 {
  value: $variable;
}

$variable: value 2;
.rule-2 {
  value: $variable;
}
```

👉 Bu örnekte `$variable` iki farklı yerde farklı değerlerle kullanılmıştır.

💡 İlginç bilgi:
Sass değişkenleri (ve tüm Sass tanımlayıcıları), tire (`-`) ve alt çizgi (`_`) karakterlerini aynı kabul eder. Yani `$font-size` ve `$font_size` aynı değişkeni ifade eder. Bu, Sass’ın yalnızca alt çizgilere izin verdiği erken dönemlerinden kalma bir özelliktir.

---

## ⚙️ Varsayılan Değerler (Default Values)

Normalde bir değişkene bir değer atadığınızda, eğer bu değişkenin zaten bir değeri varsa, eski değer üzerine yazılır. Ancak bir Sass kütüphanesi yazıyorsanız, kullanıcıların kütüphanenizin değişkenlerini CSS oluşturmadan önce yapılandırabilmesini isteyebilirsiniz.

Bunu mümkün kılmak için Sass `!default` bayrağını sağlar. Bu bayrak, yalnızca değişken tanımlı değilse veya değeri `null` ise değişkene değer atar. Aksi halde mevcut değer korunur.

```scss
// _library.scss
$black: #000 !default;
$border-radius: 0.25rem !default;
$box-shadow: 0 0.5rem 1rem rgba($black, 0.15) !default;

code {
  border-radius: $border-radius;
  box-shadow: $box-shadow;
}
// style.scss
@use 'library' with (
  $black: #222,
  $border-radius: 0.1rem
);
```

👉 Bu örnekte kütüphane değişkenleri `!default` ile tanımlanmış ve `@use ... with (...)` kullanılarak özelleştirilmiştir.

---

## 📦 Modül Yapılandırma (Configuring Modules)

`!default` ile tanımlanan değişkenler, bir modül `@use` ile yüklenirken yapılandırılabilir. Sass kütüphaneleri, kullanıcıların CSS’i özelleştirmesine olanak tanımak için genellikle `!default` kullanır.

Yerleşik (built-in) modüller tarafından tanımlanan değişkenler değiştirilemez.

```scss
@use "sass:math" as math;

// This assignment will fail.
math.$pi: 0;
```

👉 Yerleşik modül değişkenleri (`math.$pi`) değiştirilemez.

---

## 🌍 Kapsam (Scope)

Stil sayfasının en üst seviyesinde tanımlanan değişkenler **globaldir** ve bildirildikten sonra modülün her yerinden erişilebilir. Ancak bloklarda (örneğin süslü parantez içinde) tanımlanan değişkenler genellikle **lokaldir** ve yalnızca tanımlandıkları blok içinde kullanılabilir.

```scss
$global-variable: global value;

.content {
  $local-variable: local value;
  global: $global-variable;
  local: $local-variable;
}

.sidebar {
  global: $global-variable;

  // Hata: $local-variable burada tanımlı değil
  // local: $local-variable;
}
```

👉 Bu örnekte `$local-variable` sadece `.content` bloğu içinde geçerlidir.

---

## 🎭 Gölgeleme (Shadowing)

Yerel (local) değişkenler, global bir değişken ile aynı ada sahip olabilir. Böyle bir durumda aslında iki farklı değişken vardır: biri lokal, diğeri global. Bu, yerel değişkenin yanlışlıkla global değişkenin değerini değiştirmesini engeller.

```scss
$variable: global value;

.content {
  $variable: local value;
  value: $variable;
}

.sidebar {
  value: $variable;
}
```

👉 `.content` içinde `$variable` yerel değeri alırken, `.sidebar` içinde global değeri alır.

Global bir değişkenin değerini yerel kapsamdan değiştirmek için `!global` bayrağı kullanılabilir.

```scss
$variable: first global value;

.content {
  $variable: second global value !global;
  value: $variable;
}

.sidebar {
  value: $variable;
}
```

👉 `!global`, değişkeni global kapsamda yeniden atar.

⚠️ Dikkat!
`!global` yalnızca dosyanın en üst seviyesinde önceden tanımlanmış bir değişken için kullanılabilir. Yeni bir değişken oluşturmak için kullanılamaz.

---

## 🔄 Akış Kontrolü Kapsamı (Flow Control Scope)

Akış kontrol kuralları (flow control rules) içinde bildirilen değişkenlerin özel kapsam kuralları vardır: Bu değişkenler aynı seviyedeki diğer değişkenleri gölgelemez, doğrudan onlara değer atar. Bu, koşullu atamalar yapmayı veya döngülerde değer oluşturmayı kolaylaştırır.

```scss
$dark-theme: true !default;
$primary-color: #f8bbd0 !default;
$accent-color: #6a1b9a !default;

@if $dark-theme {
  $primary-color: darken($primary-color, 60%);
  $accent-color: lighten($accent-color, 60%);
}

.button {
  background-color: $primary-color;
  border: 1px solid $accent-color;
  border-radius: 3px;
}
```

👉 Bu örnekte akış kontrolü (`@if`) değişkenlerin değerlerini yeniden atar.

⚠️ Dikkat!
Akış kontrol kapsamındaki değişkenler, dış kapsamdaki mevcut değişkenlere değer atayabilir, ancak burada yeni tanımlanan değişkenler dış kapsamda erişilemez. Bu nedenle, değişkeni önceden `null` bile olsa tanımlamak gerekir.

---

## 🧰 İleri Düzey Değişken Fonksiyonları (Advanced Variable Functions)

Sass çekirdek kütüphanesi, değişkenlerle çalışmak için bazı gelişmiş fonksiyonlar sunar:

* `meta.variable-exists()` → Belirtilen adda bir değişken mevcut mu kontrol eder.
* `meta.global-variable-exists()` → Sadece global kapsamda değişken var mı kontrol eder.

⚠️ Dikkat!
Bazen kullanıcılar, başka bir değişkene dayanarak değişken adı oluşturmak için interpolation kullanmak ister. Sass buna izin vermez, çünkü değişkenlerin nerede tanımlandığını anlamayı zorlaştırır. Bunun yerine, adları değerlerle eşleyen bir **map** oluşturabilir ve `map.get()` ile erişebilirsiniz.

```scss
@use "sass:map";

$theme-colors: (
  "success": #28a745,
  "info": #17a2b8,
  "warning": #ffc107,
);

.alert {
  // Instead of $theme-color-#{warning}
  background-color: map.get($theme-colors, "warning");
}
```

👉 Bu örnekte `map.get()` ile uyarı rengi alınmaktadır.
