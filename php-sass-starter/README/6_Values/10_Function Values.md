## 🧩 Fonksiyon Değerleri (Function Values)

Fonksiyonlar da değer olabilir!
Bir fonksiyonu doğrudan değer olarak yazamazsınız, ancak `meta.get-function()` fonksiyonu ile bir fonksiyonun adını alarak onu değer haline getirebilirsiniz.
Fonksiyon değeri elde ettikten sonra, onu `meta.call()` ile çağırabilirsiniz.

Bu yaklaşım, başka fonksiyonları çağıran **üst düzey fonksiyonlar (higher-order functions)** yazmak için kullanışlıdır.

---

### SCSS Sözdizimi

```scss
@use "sass:list";
@use "sass:meta";
@use "sass:string";

/// $condition `true` döndürdüğü tüm elemanları çıkararak $list'in bir kopyasını döndürür.
@function remove-where($list, $condition) {
  $new-list: ();
  $separator: list.separator($list);
  @each $element in $list {
    @if not meta.call($condition, $element) {
      $new-list: list.append($new-list, $element, $separator: $separator);
    }
  }
  @return $new-list;
}

$fonts: Tahoma, Geneva, "Helvetica Neue", Helvetica, Arial, sans-serif;

.content {
  @function contains-helvetica($string) {
    @return string.index($string, "Helvetica");
  }
  font-family: remove-where($fonts, meta.get-function("contains-helvetica"));
}
```

👉 Bu örnekte:

* `contains-helvetica()` fonksiyonu, dizgede `"Helvetica"` geçip geçmediğini kontrol eder.
* `remove-where()` fonksiyonu, bu koşulu sağlayan yazı tiplerini listeden çıkarır.
* Sonuçta `"Helvetica"` içeren yazı tipleri CSS çıktısına dahil edilmez.
