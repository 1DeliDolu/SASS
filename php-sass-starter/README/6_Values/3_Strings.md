## 📝 Dizgeler (Strings)

Dizgeler, karakter dizileridir (özellikle Unicode kod noktaları). Sass, iç yapıları aynı ama derlendiklerinde farklı görünen iki tür dizgeyi destekler:

* **Tırnaklı dizgeler (quoted strings):** `"Helvetica Neue"`
* **Tırnaksız dizgeler (unquoted strings / identifiers):** `bold`

Birlikte, CSS’te görülen farklı metin türlerini kapsarlar.

💡 İlginç bilgi:

* Tırnaklı bir dizgeyi tırnaksıza dönüştürmek için `string.unquote()`
* Tırnaksız bir dizgeyi tırnaklıya dönüştürmek için `string.quote()` fonksiyonu kullanılır.

### SCSS Sözdizimi

```scss
@use "sass:string";

@debug string.unquote(".widget:hover"); // .widget:hover
@debug string.quote(bold); // "bold"
```

---

## 🔠 Kaçış Dizileri (Escapes)

Tüm Sass dizgeleri, standart CSS kaçış kodlarını destekler:

* A–F harfleri ve 0–9 sayıları dışındaki herhangi bir karakter (yeni satır bile!) başına `\` eklenerek dizgeye dahil edilebilir.
* Herhangi bir karakter, Unicode kod noktası onaltılık biçimde yazılarak ve önüne `\` eklenerek dahil edilebilir. Kod noktasından sonra boşluk eklenerek sınır belirtilebilir.

### SCSS Sözdizimi

```scss
@debug "\""; // '"'
@debug \.widget; // \.widget
@debug "\a"; // "\a" (sadece yeni satır içeren bir dizge)
@debug "line1\a line2"; // "line1\a line2"
@debug "Nat + Liz \1F46D"; // "Nat + Liz 👭"
```

💡 İlginç bilgi:
Dizgelerde yazılabilen karakterler için Unicode kaçışı kullanmak, karakteri doğrudan yazmakla aynı sonucu üretir.

---

## 🗨️ Tırnaklı Dizgeler (Quoted Strings)

Tırnaklı dizgeler tek veya çift tırnak arasında yazılır: `"Helvetica Neue"`.
İçerik interpolasyon barındırabilir. Kaçış kuralları:

* `\` → `\\`
* Kullanılan tırnak işareti → `\'` veya `\"`
* Yeni satır → `\a `

Tırnaklı dizgeler, orijinal Sass dizgeleriyle aynı içeriğe sahip CSS dizgelerine derlenir.

### SCSS Sözdizimi

```scss
@debug "Helvetica Neue"; // "Helvetica Neue"
@debug "C:\\Program Files"; // "C:\\Program Files"
@debug "\"Don't Fear the Reaper\""; // "\"Don't Fear the Reaper\""
@debug "line1\a line2"; // "line1\a line2"

$roboto-variant: "Mono";
@debug "Roboto #{$roboto-variant}"; // "Roboto Mono"
```

💡 İlginç bilgi:
Bir tırnaklı dizge interpolasyonla başka bir değere enjekte edildiğinde tırnaklar kaldırılır.

---

## 🔤 Tırnaksız Dizgeler (Unquoted Strings)

Tırnaksız dizgeler CSS tanımlayıcıları (identifiers) biçiminde yazılır. Her yerde interpolasyon içerebilir.

### SCSS Sözdizimi

```scss
@debug bold; // bold
@debug -webkit-flex; // -webkit-flex
@debug --123; // --123

$prefix: ms;
@debug -#{$prefix}-flex; // -ms-flex
```

⚠️ Dikkat!
Tüm tanımlayıcılar tırnaksız dizge olarak ayrıştırılmaz:

* CSS renk adları → renk
* `null` → Sass’ın null değeri
* `true` ve `false` → Boole değerleri
* `not`, `and`, `or` → Mantıksal operatörler

Bu yüzden, tırnaksız dizgeler yalnızca CSS özellikleri için özel olarak gerekiyorsa kullanılmalıdır.

---

## 🔑 Tırnaksız Dizgelerde Kaçışlar

Tırnaksız dizgeler ayrıştırıldığında, kaçış karakterleri normal metin gibi işlenir. Ancak CSS ile aynı anlama gelmeleri için normalize edilir:

* Geçerli bir tanımlayıcı karakterse → normal yazılır
* Yazdırılabilir bir karakter (yeni satır veya tab dışında) ise → `\` ile yazılır
* Diğer durumlarda → küçük harfli Unicode kaçışı + boşluk ile yazılır

### SCSS Sözdizimi

```scss
@use "sass:string";

@debug \1F46D; // 👭
@debug \21; // \!
@debug \7Fx; // \7f x
@debug string.length(\7Fx); // 5
```

---

## 📚 Diğer Tırnaksız Dizgeler

CSS sözdiziminin bazı özel bölümleri de tırnaksız dizgeler olarak ayrıştırılır:

* `url()`, `element()` gibi özel fonksiyonların argümanları
* Unicode aralık belirteçleri: `U+0-7F`, `U+4??`
* Hash belirteçleri: `#my-background` (hex renk olmayan)
* `%` değeri
* `!important` ifadesi

### SCSS Sözdizimi

```scss
@debug url(https://example.org); // url(https://example.org)
@debug U+4??; // U+4??
@debug #my-background; // #my-background
@debug %; // %
@debug !important; // !important
```

---

## 🔢 Dizge İndeksleri (String Indexes)

Sass, dizgelerde karakterleri işaret eden sayıları (indeks) kullanan fonksiyonlara sahiptir.

* `1` → dizgenin ilk karakteri (birçok dilde 0’dan başlasa da Sass’ta 1’den başlar).
* `-1` → son karakter, `-2` → sondan ikinci karakter.

### SCSS Sözdizimi

```scss
@use "sass:string";

@debug string.index("Helvetica Neue", "Helvetica"); // 1
@debug string.index("Helvetica Neue", "Neue"); // 11
@debug string.slice("Roboto Mono", -4); // "Mono"
```
