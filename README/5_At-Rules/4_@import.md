## 📤 @forward

`@forward` kuralı, bir Sass stil sayfasını yükler ve onun mixinlerini, fonksiyonlarını ve değişkenlerini, stil sayfanız `@use` ile yüklendiğinde kullanılabilir hale getirir. Bu, Sass kütüphanelerini birçok dosya arasında düzenlemeyi mümkün kılar, fakat kullanıcıların yalnızca tek bir giriş noktası dosyasını yüklemesini sağlar.

Kural şu şekilde yazılır: `@forward "<url>"`. Bu, verilen URL’deki modülü tıpkı `@use` gibi yükler, fakat yüklenen modülün **genel üyelerini** sizin modülünüzün doğrudan tanımlamış gibi kullanıcılarınıza açar. Ancak bu üyeler sizin modülünüzde görünmez — eğer onları kendi modülünüz içinde de kullanmak istiyorsanız, ayrıca `@use` yazmalısınız. Endişelenmeyin, modül yalnızca bir kez yüklenir!

Aynı dosyada hem `@forward` hem de `@use` yazıyorsanız, her zaman önce `@forward` yazmak iyi bir fikirdir. Böylece kullanıcılarınız modülü yapılandırmak isterse, bu yapılandırma `@forward` üzerinden uygulanır, ardından `@use` yapılandırmasız şekilde yüklenebilir.

💡 İlginç bilgi:
`@forward`, modülün CSS çıktısı açısından `@use` ile aynı şekilde davranır. İletilen (forwarded) modülün stilleri derlenmiş CSS çıktısına eklenir ve `@forward` yazan modül de onu genişletebilir, hatta kendisi `@use` etmemiş olsa bile.

```scss
// src/_list.scss
@mixin list-reset {
  margin: 0;
  padding: 0;
  list-style: none;
}
// bootstrap.scss
@forward "src/list";
// styles.scss
@use "bootstrap";

li {
  @include bootstrap.list-reset;
}
```

👉 Burada `_list.scss` içindeki `list-reset` mixini `bootstrap.scss` üzerinden dışarıya açılmıştır.

---

## 🏷️ Önek Ekleme (Adding a Prefix)

Modül üyeleri genelde namespace ile kullanıldığından, kısa ve basit isimler en okunabilir olanlardır. Ancak bu isimler, tanımlandıkları modül dışında anlamsız olabilir. Bunun için `@forward` tüm iletilen üyelere bir önek (prefix) ekleme seçeneği sunar.

Bu şu şekilde yazılır:
`@forward "<url>" as <prefix>-*`

Böylece modülün her mixin, fonksiyon ve değişken isminin başına verilen önek eklenir.

```scss
// src/_list.scss
@mixin reset {
  margin: 0;
  padding: 0;
  list-style: none;
}
// bootstrap.scss
@forward "src/list" as list-*;
// styles.scss
@use "bootstrap";

li {
  @include bootstrap.list-reset;
}
```

👉 Burada `reset` mixini, `list-reset` adıyla dışa açılmıştır.

---

## 👁️ Görünürlüğü Kontrol Etme (Controlling Visibility)

Bazen bir modüldeki tüm üyeleri iletmek istemezsiniz. Bazı üyeleri yalnızca kendi paketinizin kullanmasını isteyebilir veya kullanıcıların belirli üyeleri farklı şekilde yüklemesini zorunlu kılabilirsiniz.

İletilecek üyeleri tam olarak kontrol etmek için:

* `@forward "<url>" hide <members...>` → Belirtilen üyeler hariç hepsini iletir.
* `@forward "<url>" show <members...>` → Yalnızca belirtilen üyeleri iletir.

Her iki biçimde de mixin, fonksiyon veya değişkenlerin (başında `$` olacak şekilde) isimleri yazılır.

```scss
// src/_list.scss
$horizontal-list-gap: 2em;

@mixin list-reset {
  margin: 0;
  padding: 0;
  list-style: none;
}

@mixin list-horizontal {
  @include list-reset;

  li {
    display: inline-block;
    margin: {
      left: -2px;
      right: $horizontal-list-gap;
    }
  }
}
// bootstrap.scss
@forward "src/list" hide list-reset, $horizontal-list-gap;
```

👉 Burada `list-reset` mixini ve `$horizontal-list-gap` değişkeni dışa açılmamıştır.

---

## ⚙️ Modülleri Yapılandırma (Configuring Modules)

Uyumluluk:

* Dart Sass: 1.24.0’dan itibaren
* LibSass: ✗
* Ruby Sass: ✗

`@forward` kuralı da modülleri yapılandırarak yükleyebilir. Bu çoğunlukla `@use` ile aynı şekilde çalışır, ancak bir farkla: `@forward` kuralının yapılandırması `!default` bayrağını kullanabilir. Bu sayede bir modül, yukarıdaki stil sayfasının varsayılanlarını değiştirebilir, ama aşağıdaki stil sayfalarının bunları geçersiz kılmasına da izin verir.

```scss
// _library.scss
$black: #000 !default;
$border-radius: 0.25rem !default;
$box-shadow: 0 0.5rem 1rem rgba($black, 0.15) !default;

code {
  border-radius: $border-radius;
  box-shadow: $box-shadow;
}
// _opinionated.scss
@forward 'library' with (
  $black: #222 !default,
  $border-radius: 0.1rem !default
);
// style.scss
@use 'opinionated' with ($black: #333);
```

👉 Burada `_opinionated.scss`, `library` modülünü yapılandırarak iletmiş, ardından `style.scss` dosyası bunu tekrar yapılandırmıştır.
