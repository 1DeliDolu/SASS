## 💎 Değerler (Values)

Sass, çoğu doğrudan CSS’ten gelen bir dizi değer türünü (value types) destekler. Her ifade (expression) bir değer üretir, değişkenler (variables) değerleri saklar.

### 📐 Sayılar (Numbers)

Birimli veya birimsiz olabilir: `12` veya `100px`.

### 📝 Dizgeler (Strings)

Tırnaklı veya tırnaksız olabilir: `"Helvetica Neue"` veya `bold`.

### 🎨 Renkler (Colors)

Altıgen (hex) gösterimle veya adla belirtilebilir: `#c6538c` veya `blue`.
Fonksiyonlardan da dönebilir: `rgb(107, 113, 127)` veya `hsl(210, 100%, 20%)`.

### 📋 Listeler (Lists)

Değerlerin listesi, boşluk veya virgülle ayrılabilir, köşeli parantez içinde ya da parantezsiz olabilir:

* `1.5em 1em 0 2em`
* `Helvetica, Arial, sans-serif`
* `[col1-start]`

---

### 🔧 Sass’a Özgü Ekstra Türler

* Mantıksal (boolean) değerler: `true` ve `false`.
* Tekil `null` değeri.
* Değerleri anahtarlarla eşleştiren haritalar (maps): `("background": red, "foreground": pink)`.
* `get-function()` ile dönen ve `call()` ile çağrılan fonksiyon referansları.
