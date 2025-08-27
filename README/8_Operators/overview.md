## ➕ Operatörler (operators)

Sass, farklı değerlerle çalışmak için faydalı birkaç operatörü destekler. Bunlara standart matematiksel operatörler olan `+` ve `*` dahil olduğu gibi, çeşitli diğer türler için de operatörler vardır:

* `==` ve `!=` iki değerin aynı olup olmadığını kontrol etmek için kullanılır.
* `+`, `-`, `*`, `/` ve `%` sayılar için normal matematiksel anlamına sahiptir; birimlerle çalışırken bilimsel matematikteki birim kullanımına uygun özel davranışlar gösterir.
* `<`, `<=`, `>` ve `>=` iki sayının birbirinden büyük ya da küçük olup olmadığını kontrol eder.
* `and`, `or` ve `not` normal mantıksal (boolean) davranışa sahiptir. Sass’te `false` ve `null` haricindeki tüm değerler "true" kabul edilir.
* `+`, `-` ve `/` dizeleri (string) birleştirmek için kullanılabilir.

⚠️ Dikkat!
Sass’ın geçmişinde, renkler üzerinde matematiksel işlemler için destek eklenmişti. Bu işlemler renklerin her bir RGB kanalında ayrı ayrı çalışıyordu. Örneğin iki rengi toplamak, kırmızı kanalların toplamını kırmızı kanal olarak almak gibi sonuç veriyordu.

Bu davranış çok faydalı değildi, çünkü kanal bazlı RGB aritmetiği, insanların renk algısıyla iyi uyuşmuyordu. Bunun yerine daha kullanışlı renk fonksiyonları eklendi ve renk işlemleri kullanımdan kaldırıldı. Bu işlemler hala **LibSass** ve **Ruby Sass**’ta desteklenmektedir, ancak uyarılar üretir ve kullanıcıların bunlardan kaçınmaları şiddetle önerilir.

---

## 📐 İşlem Önceliği (order of operations)

Sass, sıkıdan gevşeğe doğru oldukça standart bir işlem önceliği sırasına sahiptir:

1. Tekli (unary) operatörler: `not`, `+`, `-` ve `/`
2. `*`, `/` ve `%` operatörleri
3. `+` ve `-` operatörleri
4. `>`, `>=`, `<` ve `<=` operatörleri
5. `==` ve `!=` operatörleri
6. `and` operatörü
7. `or` operatörü
8. `=` operatörü (sadece uygun olduğunda)

### SCSS Sözdizimi

```scss
@debug 1 + 2 * 3 == 1 + (2 * 3); // true
@debug true or false and false == true or (false and false); // true
```

👉 Bu örnek, Sass’te işlem önceliğini gösterir.

---

## 🔢 Parantezler (parentheses)

Parantezler kullanılarak işlem önceliğini açıkça kontrol edebilirsiniz. Parantez içindeki bir işlem, dışındaki tüm işlemlerden önce değerlendirilir. Parantezler iç içe de yazılabilir; bu durumda en içteki parantez önce değerlendirilir.

### SCSS Sözdizimi

```scss
@debug (1 + 2) * 3; // 9
@debug ((1 + 2) * 3 + 4) * 5; // 65
```

👉 Parantezler, işlemlerin sırasını değiştirmek için kullanılır.

---

## ➖ Tek Eşittir (single equals)

Sass, yalnızca fonksiyon argümanlarında izin verilen özel bir `=` operatörünü destekler. Bu, iki operandı `=` ile ayıran tırnaksız bir dize oluşturur. Bu özellik, çok eski IE’ye özel sözdizimi ile geriye dönük uyumluluk için vardır.

### SCSS Sözdizimi

```scss
.transparent-blue {
  filter: chroma(color=#0000ff);
}
```

👉 Burada `=` operatörü, eski tarayıcı desteği için tırnaksız bir dize üretir.
