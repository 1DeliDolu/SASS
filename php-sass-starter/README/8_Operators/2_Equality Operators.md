## ⚖️ Eşitlik Operatörleri (equality operators)

### 🧩 Uyumluluk (unitless equality)

* Dart Sass: ✓
* LibSass: ✗
* Ruby Sass: 4.0.0 sürümünden itibaren (yayınlanmamış)

**LibSass** ve eski **Ruby Sass** sürümleri, birimleri olmayan sayıları (ör. `1`) herhangi bir birimle (`1px`, `1em`) aynı kabul ediyordu. Bu davranış artık kullanım dışı bırakıldı ve en son sürümlerde kaldırıldı çünkü **geçişlilik kuralını (transitivity)** ihlal ediyordu.

---

Eşitlik operatörleri, iki değerin aynı olup olmadığını döndürür. Yazılış biçimi:

* `<ifade> == <ifade>` → iki ifadenin eşit olup olmadığını döndürür.
* `<ifade> != <ifade>` → iki ifadenin eşit olmadığını döndürür.

İki değer, **aynı türde** ve **aynı değerde** olduklarında eşit kabul edilir. Bu, türlere göre farklılık gösterir:

* **Sayılar (numbers):** Aynı değere ve aynı birime sahiplerse, ya da birimler birbirine dönüştürülebiliyorsa eşittirler.
* **Dizeler (strings):** Tırnaklı ve tırnaksız dizeler aynı içeriğe sahipse eşittir.
* **Renkler (colors):** Aynı renk uzayında aynı kanal değerlerine sahiplerse eşittirler. Eski renk uzayında iseler RGBA kanal değerleri karşılaştırılır.
* **Listeler (lists):** İçerikleri eşitse eşittirler. Virgülle ayrılmış listeler, boşlukla ayrılmış listelere eşit değildir. Köşeli parantezli listeler de köşesizlere eşit değildir.
* **Haritalar (maps):** Hem anahtarlar hem de değerler eşitse eşittirler.
* **Hesaplamalar (calculations):** İsimleri ve argümanları eşitse eşittir. İşlem argümanları metinsel olarak karşılaştırılır.
* **true, false ve null:** Sadece kendilerine eşittir.
* **Fonksiyonlar (functions):** Aynı fonksiyona eşittir. Fonksiyonlar referansa göre karşılaştırılır; aynı ada ve tanıma sahip olsalar bile farklı yerde tanımlandılarsa eşit sayılmazlar.

---

### SCSS Sözdizimi

```scss
@debug 1px == 1px; // true
@debug 1px != 1em; // true
@debug 1 != 1px; // true
@debug 96px == 1in; // true

@debug "Helvetica" == Helvetica; // true
@debug "Helvetica" != "Arial"; // true

@debug hsl(34, 35%, 92.1%) == #f2ece4; // true
@debug rgba(179, 115, 153, 0.5) != rgba(179, 115, 153, 0.8); // true

@debug (5px 7px 10px) == (5px 7px 10px); // true
@debug (5px 7px 10px) != (10px 14px 20px); // true
@debug (5px 7px 10px) != (5px, 7px, 10px); // true
@debug (5px 7px 10px) != [5px 7px 10px]; // true

$theme: ("venus": #998099, "nebula": #d2e1dd);
@debug $theme == ("venus": #998099, "nebula": #d2e1dd); // true
@debug $theme != ("venus": #998099, "iron": #dadbdf); // true

@debug true == true; // true
@debug true != false; // true
@debug null != false; // true

@debug get-function("rgba") == get-function("rgba"); // true
@debug get-function("rgba") != get-function("hsla"); // true
```

👉 Bu örnekler, Sass’te farklı veri türleri için `==` ve `!=` operatörlerinin nasıl çalıştığını göstermektedir.
