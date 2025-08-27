## 🔄 İlişkisel Operatörler (relational operators)

İlişkisel operatörler, sayıların birbirinden büyük veya küçük olup olmadığını belirler. Uyumlu birimler arasında otomatik dönüşüm yaparlar.

* `<ifade> < <ifade>` → ilk ifadenin değerinin ikinciden küçük olup olmadığını döndürür.
* `<ifade> <= <ifade>` → ilk ifadenin değerinin ikinciden küçük veya eşit olup olmadığını döndürür.
* `<ifade> > <ifade>` → ilk ifadenin değerinin ikinciden büyük olup olmadığını döndürür.
* `<ifade> >= <ifade>` → ilk ifadenin değerinin ikinciden büyük veya eşit olup olmadığını döndürür.

---

### SCSS Sözdizimi

```scss
@debug 100 > 50; // true
@debug 10px < 17px; // true
@debug 96px >= 1in; // true
@debug 1000ms <= 1s; // true
```

👉 Burada birim uyumlu olduğunda Sass otomatik olarak dönüşüm yapar.

---

Birim içermeyen sayılar (`unitless numbers`), herhangi bir sayı ile karşılaştırılabilir. Bu durumda otomatik olarak o sayının birimine dönüştürülür.

### SCSS Sözdizimi

```scss
@debug 100 > 50px; // true
@debug 10px < 17; // true
```

👉 Birimsiz değerler, karşılaştırılan değerin birimine dönüştürülür.

---

Birbirine uyumsuz birimlere sahip sayılar karşılaştırılamaz.

### SCSS Sözdizimi

```scss
@debug 100px > 10s;
//     ^^^^^^^^^^^
// Error: Incompatible units px and s.
```

👉 Burada `px` ile `s` uyumsuz birimler olduğu için hata alınır.
