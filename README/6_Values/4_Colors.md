## 🎨 Renkler (Colors)

Sass, renk değerleri için yerleşik destek sunar. CSS renklerinde olduğu gibi, her renk belirli bir renk uzayında (color space) bir noktayı temsil eder (ör. `rgb`, `lab`).

Sass renkleri şu şekilde yazılabilir:

* **Hex kodları:** `#f2ece4`, `#b37399aa`
* **CSS renk adları:** `midnightblue`, `transparent`
* **Fonksiyonlarla:** `rgb()`, `lab()`, `color()`

### SCSS Sözdizimi

```scss
@debug #f2ece4; // #f2ece4
@debug #b37399aa; // rgba(179, 115, 153, 67%)
@debug midnightblue; // #191970
@debug rgb(204 102 153); // #c69
@debug lab(32.4% 38.4 -47.7 / 0.7); // lab(32.4% 38.4 -47.7 / 0.7)
@debug color(display-p3 0.597 0.732 0.576); // color(display-p3 0.597 0.732 0.576)
```

---

## 🌈 Renk Uzayları (Color Spaces)

Sass, CSS’in desteklediği tüm renk uzaylarını destekler.
Bir renk, yazıldığı uzayda çıktılanır. Ancak `color.to-space()` kullanılarak başka bir uzaya dönüştürülebilir.

* **rgb**, **hsl**, **hwb** → eski (legacy) renk uzaylarıdır.
* Yeni uzaylar: `srgb`, `display-p3`, `lab`, `lch`, `oklab`, `oklch`, `xyz` vb.

💡 Sass, gamut dışında kalan değerleri de temsil edebilir. Böylece geniş gamutlu bir renkten dar gamuta gidip geri dönerken bilgi kaybı olmaz.

⚠️ CSS bazı fonksiyonlarda değerleri kırpar (clip).
Örn: `rgb(500 0 0)` → kırmızı kanal `[0,255]` aralığına çekilir → `rgb(255 0 0)`.
Sass ise `color.change()` ile gamut dışı değerleri de saklayabilir.

---

## 🔲 Eksik Kanallar (Missing Channels)

Bir renk kanalının değeri bilinmiyorsa `none` olarak yazılır. Örn: `hsl(none 0% 50%)`
Genellikle `0` gibi değerlendirilir ama bazı özel durumları vardır:

* **color.mix()** → Eksik kanal varsa, diğer renkten alınır.
* **Uzay dönüşümü** → Benzer kanal varsa, sonuçta da `none` olarak kalır.
* **color.channel()** → Eksik kanal için `0` döner.
* **color.is-missing()** → Eksik kanal olup olmadığını kontrol eder.

### SCSS Sözdizimi

```scss
@use 'sass:color';

$grey: hsl(none 0% 50%);

@debug color.mix($grey, blue, $method: hsl); // hsl(240, 50%, 50%)
@debug color.to-space($grey, lch); // lch(53.3889647411% 0 none)
```

---

## ⚪ Güçsüz Kanallar (Powerless Channels)

Bazı kanallar, belirli durumlarda sonucun ekranda nasıl görüneceğini etkilemez.
CSS spesifikasyonu, böyle kanalların başka bir uzaya dönüştürüldüğünde `none` ile değiştirilmesini şart koşar.
Sass da aynı davranışı uygular (eski uzaylara dönüşüm hariç).

Detaylı kontrol için: `color.is-powerless()`.

---

## 🕰️ Eski Renk Uzayları (Legacy Color Spaces)

Geçmişte CSS ve Sass yalnızca standart RGB gamutunu destekliyordu (`rgb`, `hsl`, `hwb`).
O zamanlar tüm renkler aynı gamutta olduğundan, fonksiyonlar uzay fark etmeksizin çalışıyordu.

Bugün bu davranış yalnızca eski uzaylar için korunur. Yine de, renk fonksiyonlarında çalışılacak uzayı (`$space`) açıkça belirtmek en iyi uygulamadır.

Sass, eski uzaylar arasında otomatik dönüşüm yapar ve mümkün olan en uyumlu çıktıyı üretir.

---

## 🛠️ Renk Fonksiyonları (Color Functions)

Sass, mevcut renkler üzerinde işlem yaparak yeni renkler üretmeye yarayan birçok fonksiyon destekler:

* Renkleri **karıştırma** (`color.mix`)
* Kanal değerlerini **ölçekleme** (`color.scale`)
* Renk uzayları arasında dönüşüm (`color.to-space`)

💡 İlginç bilgi:
Renk fonksiyonları renkleri otomatik dönüştürebilir, bu sayede **Oklch** gibi görsel olarak daha tutarlı uzaylarda işlem yapmak kolaylaşır.
Ama dönüşüm açıkça yapılmazsa, her zaman verilen uzayın içinde sonuç döner.

### SCSS Sözdizimi

```scss
@use 'sass:color';

$venus: #998099;

@debug color.scale($venus, $lightness: +15%, $space: oklch);
// rgb(170.1523703626, 144.612080603, 170.1172627174)

@debug color.mix($venus, midnightblue, $method: oklch);
// rgb(95.9363315581, 74.5687109346, 133.2082569526)
```
