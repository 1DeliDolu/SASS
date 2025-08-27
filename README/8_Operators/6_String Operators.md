## 🧵 Dize Operatörleri (string operators)

Sass, dizeler (strings) üretmek için birkaç operatör destekler:

* `<ifade> + <ifade>` → her iki ifadenin değerini içeren bir dize döndürür. Eğer ifadelerden biri tırnaklı ise sonuç da tırnaklı olur; aksi halde tırnaksız olur.
* `<ifade> - <ifade>` → iki ifadenin değerini `-` ile ayırarak tırnaksız bir dize döndürür. Bu eski bir operatördür; bunun yerine genellikle **interpolasyon** (`#{}`) kullanılmalıdır.

---

### SCSS Sözdizimi

```scss
@debug "Helvetica" + " Neue"; // "Helvetica Neue"
@debug sans- + serif; // sans-serif
@debug sans - serif; // sans-serif
```

👉 `+` ve `-` operatörleri ile dizeler birleştirilir.

---

Bu operatörler sadece dizeler için değil, CSS’e yazılabilen her değerle çalışır (bazı istisnalar hariç):

* **Sayılar (numbers):** Sol tarafta kullanılamazlar (çünkü kendi operatörleri vardır).
* **Renkler (colors):** Sol tarafta kullanılamazlar (çünkü geçmişte kendi operatörleri vardı).

### SCSS Sözdizimi

```scss
@debug "Elapsed time: " + 10s; // "Elapsed time: 10s";
@debug true + " is a boolean value"; // "true is a boolean value";
```

👉 Burada sayılar ve boolean değerler, dizeye eklenmiştir.

⚠️ Dikkat!
Dize oluştururken bu operatörleri kullanmak yerine **interpolasyon** (`#{}`) kullanmak genellikle daha temiz ve anlaşılırdır.

---

## ➖ Tekli Operatörler (unary operators)

Tarihsel nedenlerle, Sass ayrıca `/` ve `-` işaretlerini tekli (unary) operatör olarak da destekler. Bunlar yalnızca tek bir değer alır:

* `/ <ifade>` → `/` ile başlayan ve ifadenin değeriyle devam eden tırnaksız bir dize döndürür.
* `- <ifade>` → `-` ile başlayan ve ifadenin değeriyle devam eden tırnaksız bir dize döndürür.

### SCSS Sözdizimi

```scss
@debug / 15px; // /15px
@debug - moz; // -moz
```

👉 `-moz` veya `/15px` gibi tarayıcıya özgü değerler oluşturmak için kullanılabilir.
