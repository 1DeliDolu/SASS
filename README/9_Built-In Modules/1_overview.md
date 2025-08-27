## 📦 Dahili Modüller (Built-In Modules)

Uyumluluk:
Dart Sass
1.23.0’dan beri
LibSass: ✗
Ruby Sass: ✗

➤ Şu anda yalnızca Dart Sass, `@use` ile dahili modülleri yüklemeyi destekler. Diğer uygulamaların kullanıcıları, fonksiyonları bunun yerine küresel (global) adlarıyla çağırmalıdır.

Sass, yararlı fonksiyonlar (ve bazen `mixin`) içeren birçok dahili modül sağlar. Bu modüller, herhangi bir kullanıcı tanımlı stil sayfası gibi `@use` kuralı ile yüklenebilir ve fonksiyonları diğer modül üyeleri gibi çağrılabilir. Tüm dahili modül URL’leri, Sass’in bir parçası olduklarını belirtmek için `sass:` ile başlar.

⚠️ Dikkat!
Sass modül sistemi tanıtılmadan önce, tüm Sass fonksiyonları her zaman küresel olarak erişilebilirdi. Hâlen birçok fonksiyonun küresel takma adları mevcuttur (bunlar belgelerinde listelenmiştir). Sass ekibi bunların kullanımını önermemektedir ve sonunda kaldırılacaktır, ancak şimdilik eski Sass sürümleri ve modül sistemini desteklemeyen LibSass ile uyumluluk için kullanılabilir durumdadır.

Bazı fonksiyonlar, yeni modül sisteminde bile yalnızca küresel olarak kullanılabilir. Bunun nedeni ya özel değerlendirme davranışına sahip olmalarıdır (`if()`), ya da dahili CSS fonksiyonlarına ek işlevsellik eklemeleridir (`rgb()` ve `hsl()`). Bunlar kaldırılmayacaktır ve serbestçe kullanılabilir.

### SCSS Örneği

```scss
@use "sass:color";

.button {
  $primary-color: #6b717f;
  color: $primary-color;
  border: 1px solid color.scale($primary-color, $lightness: 20%);
}
```

👉 Bu örnek, `sass:color` modülünü `@use` ile yükleyerek bir butonun kenarlık rengini `color.scale()` fonksiyonu ile açmaktadır.

Sass şu dahili modülleri sağlar:

* `sass:math` sayılar üzerinde işlem yapan fonksiyonlar sağlar.
* `sass:string` dizeleri birleştirmeyi, aramayı veya ayırmayı kolaylaştırır.
* `sass:color` mevcut renklere dayalı yeni renkler üretir, renk temaları oluşturmayı kolaylaştırır.
* `sass:list` listelerdeki değerleri erişmeyi ve değiştirmeyi sağlar.
* `sass:map` bir `map` içindeki anahtarın değerini bulmayı ve daha fazlasını mümkün kılar.
* `sass:selector` Sass’in güçlü seçici motoruna erişim sağlar.
* `sass:meta` Sass’in iç işleyişine dair detayları açığa çıkarır.

---

## 🌍 Küresel Fonksiyonlar (Global Functions)

💡 İlginç bilgi:
Özel fonksiyonları (`calc()`, `var()`) bir küresel renk oluşturucusuna argüman olarak geçirebilirsiniz. Hatta `var()` birden fazla değeri temsil edebileceği için, birden fazla argümanın yerine de kullanılabilir! Bu şekilde çağrılan bir renk fonksiyonu, çağrıldığı imzayla aynı şekilde tırnaksız bir dize döndürür.

### SCSS Örneği

```scss
@debug rgb(0 51 102 / var(--opacity)); // rgb(0 51 102 / var(--opacity))
@debug color(display-p3 var(--peach)); // color(display-p3 var(--peach))
```

👉 Bu örnekte `rgb()` ve `color()` fonksiyonlarına CSS değişkenleri (`var()`) argüman olarak geçirilmiştir.

---

## 🎨 Renk Fonksiyonları (Color Functions)

### `color($space $channel1 $channel2 $channel3 [/$alpha])`

Belirtilen renk uzayında (`srgb`, `display-p3`, `rec2020`, vb.) kanal değerleriyle bir renk döndürür.

```scss
@debug color(srgb 0.1 0.6 1); // color(srgb 0.1 0.6 1)
@debug color(xyz 30% 0% 90% / 50%); // color(xyz 0.3 0 0.9 / 50%)
```

---

### `hsl()` ve `hsla()`

Belirtilen ton (hue), doygunluk (saturation), açıklık (lightness) ve alfa (alpha) kanalı ile renk döndürür.

```scss
@debug hsl(210deg 100% 20%); // #036
@debug hsl(210deg 100% 20% / 50%); // rgba(0, 51, 102, 0.5)
@debug hsla(34, 35%, 92%, 0.2); // rgba(241.74, 235.552, 227.46, 0.2)
```

---

### `hwb()`

Ton (hue), beyazlık (whiteness) ve siyahlık (blackness) değerleriyle renk döndürür.

```scss
@debug hwb(210deg 0% 60%); // #036
@debug hwb(210 0% 60% / 0.5); // rgba(0, 51, 102, 0.5)
```

---

### `if($condition, $if-true, $if-false)`

Koşul doğruysa `$if-true`, değilse `$if-false` döndürür. Kullanılmayan argüman değerlendirilmez.

```scss
@debug if(true, 10px, 15px); // 10px
@debug if(false, 10px, 15px); // 15px
@debug if(variable-defined($var), $var, null); // null
```

---

### `lab()` ve `lch()`

CIELAB renk uzayına göre açıklık (lightness), `a`/`b` veya kromatiklik (chroma) ve ton (hue) değerleriyle renk döndürür.

```scss
@debug lab(50% -20 30); // lab(50% -20 30)
@debug lch(50% 10 270deg); // lch(50% 10 270deg)
```

---

### `oklab()` ve `oklch()`

Algısal olarak uniform açıklık (lightness), `a`/`b` veya kromatiklik (chroma) ve ton (hue) ile renk döndürür.

```scss
@debug oklab(50% -0.1 0.15); // oklab(50% -0.1 0.15)
@debug oklch(50% 0.3 270deg); // oklch(50% 0.3 270deg)
```

---

### `rgb()` ve `rgba()`

Kırmızı (red), yeşil (green), mavi (blue) ve alfa (alpha) kanalıyla renk döndürür.

```scss
@debug rgb(0 51 102); // #036
@debug rgb(95%, 92.5%, 89.5%); // #f2ece4
@debug rgb(0 51 102 / 50%); // rgba(0, 51, 102, 0.5)
@debug rgba(95%, 92.5%, 89.5%, 0.2); // rgba(242, 236, 228, 0.2)
```

Ayrıca mevcut bir renge yeni alfa kanalı eklemek için de kullanılabilir:

```scss
@debug rgb(#f2ece4, 50%); // rgba(242, 236, 228, 0.5)
@debug rgba(rgba(0, 51, 102, 0.5), 1); // #003366
```
