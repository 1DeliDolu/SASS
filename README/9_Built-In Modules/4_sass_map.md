## 📋 sass\:list Modülü

Uyumluluk:
Dart Sass
1.23.0’dan beri
LibSass
✗
Ruby Sass
✗

➤ Şu anda yalnızca Dart Sass, `@use` ile dahili modülleri yüklemeyi destekler. Diğer uygulamaların kullanıcıları, fonksiyonları bunun yerine küresel (global) adlarıyla çağırmalıdır.

💡 İlginç bilgi:
Sass’te her `map`, aslında her anahtar/değer çifti için iki elemanlı bir liste içeren bir liste olarak kabul edilir. Örneğin `(1: 2, 3: 4)`, `(1 2, 3 4)` olarak değerlendirilir. Yani tüm bu fonksiyonlar `map` için de çalışır!

Bireysel değerler de liste olarak kabul edilir. Bu nedenle tüm bu fonksiyonlar `1px`’i içinde `1px` olan tek elemanlı bir liste gibi değerlendirir.

---

### ➕ `list.append($list, $val, $separator: auto)`

Bir listenin sonuna yeni bir değer ekler.

```scss
@use 'sass:list';

@debug list.append(10px 20px, 30px); // 10px 20px 30px
@debug list.append((blue, red), green); // blue, red, green
@debug list.append(10px 20px, 30px 40px); // 10px 20px (30px 40px)
@debug list.append(10px, 20px, $separator: comma); // 10px, 20px
@debug list.append((blue, red), green, $separator: space); // blue red green
```

👉 Listenin sonuna bir değer ekler, eğer eklenen değer listeyse iç içe liste olarak kalır.

---

### 🔢 `list.index($list, $value)`

Verilen değerin listedeki indeksini döndürür. Bulunmazsa `null` döner.

```scss
@use 'sass:list';

@debug list.index(1px solid red, 1px); // 1
@debug list.index(1px solid red, solid); // 2
@debug list.index(1px solid red, dashed); // null
```

---

### 🔲 `list.is-bracketed($list)`

Bir listenin köşeli parantez (bracket) ile yazılıp yazılmadığını döndürür.

```scss
@use 'sass:list';

@debug list.is-bracketed(1px 2px 3px); // false
@debug list.is-bracketed([1px, 2px, 3px]); // true
```

---

### 🔗 `list.join($list1, $list2, $separator: auto, $bracketed: auto)`

İki listeyi birleştirir.

⚠️ Dikkat: Tek bir değer de liste olarak sayıldığından `join()` ekleme için kullanılabilir, ancak bu tavsiye edilmez. Bunun yerine `list.append()` kullanılmalıdır.

```scss
@use 'sass:list';

@debug list.join(10px 20px, 30px 40px); // 10px 20px 30px 40px
@debug list.join((blue, red), (#abc, #def)); // blue, red, #abc, #def
@debug list.join(10px, 20px); // 10px 20px
@debug list.join(10px, 20px, $separator: comma); // 10px, 20px
@debug list.join((blue, red), (#abc, #def), $separator: space); // blue red #abc #def
@debug list.join([10px], 20px); // [10px 20px]
@debug list.join(10px, 20px, $bracketed: true); // [10px 20px]
```

---

### 📏 `list.length($list)`

Bir listenin uzunluğunu döndürür.

```scss
@use 'sass:list';

@debug list.length(10px); // 1
@debug list.length(10px 20px 30px); // 3
@debug list.length((width: 10px, height: 20px)); // 2
```

---

### ↔️ `list.separator($list)`

Bir listenin ayırıcı türünü (`space`, `comma`, `slash`) döndürür.

```scss
@use 'sass:list';

@debug list.separator(1px 2px 3px); // space
@debug list.separator((1px, 2px, 3px)); // comma
@debug list.separator('Helvetica'); // space
@debug list.separator(()); // space
```

---

### 🔎 `list.nth($list, $n)`

Bir listedeki `n.` elemanı döndürür. Negatif değer sondan sayar.

```scss
@use 'sass:list';

@debug list.nth(10px 12px 16px, 2); // 12px
@debug list.nth([line1, line2, line3], -1); // line3
```

---

### ✏️ `list.set-nth($list, $n, $value)`

Belirtilen indeksteki değeri değiştirir.

```scss
@use 'sass:list';

@debug list.set-nth(10px 20px 30px, 1, 2em); // 2em 20px 30px
@debug list.set-nth(10px 20px 30px, -1, 8em); // 10px, 20px, 8em
@debug list.set-nth((Helvetica, Arial, sans-serif), 3, Roboto); // Helvetica, Arial, Roboto
```

---

### ➗ `list.slash($elements...)`

Slash (`/`) ile ayrılmış liste oluşturur.

```scss
@use 'sass:list';

@debug list.slash(1px, 50px, 100px); // 1px / 50px / 100px
```

---

### 📦 `list.zip($lists...)`

Birden fazla listeyi, her indeksteki elemanları eşleştirerek birleştirir.

```scss
@use 'sass:list';

@debug list.zip(10px 50px 100px, short mid long); // 10px short, 50px mid, 100px long
@debug list.zip(10px 50px 100px, short mid); // 10px short, 50px mid
```

---

İstersen devamında `sass:map` modülünü de aynı formatta çevirmemi ister misin?
