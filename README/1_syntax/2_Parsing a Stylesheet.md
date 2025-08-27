v## 📜 Bir Stil Sayfasının Ayrıştırılması (Parsing a Stylesheet)

Bir Sass stil sayfası, Unicode kod noktaları (Unicode code points) dizisinden ayrıştırılır. Ayrıştırma doğrudan yapılır, önce bir token akışına dönüştürülmez.

### 🔡 Girdi Kodlaması (Input Encoding)

Uyumluluk:

* Dart Sass → ✗ desteklenmez
* LibSass → ✓ desteklenir
* Ruby Sass → ✓ desteklenir

Genellikle bir belge yalnızca bayt (bytes) dizisi olarak bulunur ve Unicode’a dönüştürülmesi gerekir. Sass bu kod çözmeyi şu şekilde gerçekleştirir:

* Eğer bayt dizisi, U+FEFF BYTE ORDER MARK’ın UTF-8 veya UTF-16 kodlamasıyla başlıyorsa, ilgili kodlama kullanılır.
* Eğer bayt dizisi düz ASCII dizesi `@charset` ile başlıyorsa, Sass kodlamayı belirlemek için CSS algoritmasının yedek kodlamayı belirleyen 2. adımını kullanır.
* Aksi durumda UTF-8 kullanılır.

### ⚠️ Ayrıştırma Hataları (Parse Errors)

Sass, bir stil sayfasında geçersiz sözdizimi ile karşılaştığında ayrıştırma başarısız olur ve kullanıcıya geçersiz sözdiziminin konumu ile geçersiz olma nedeni hakkında bilgi veren bir hata mesajı gösterilir.

Bu durum CSS’ten farklıdır; çünkü CSS çoğu hatadan kurtulma yöntemini belirtirken Sass hemen hata verir. Bu, SCSS’in CSS’in katı bir üst kümesi (superset) olmamasının nadir örneklerinden biridir. Ancak, Sass kullanıcıları için hataların hemen görülmesi, bu hataların CSS çıktısına yansıtılmasından çok daha kullanışlıdır.

Ayrıştırma hatalarının konumuna uygulama-özel API’ler aracılığıyla erişilebilir. Örneğin:

* Dart Sass’ta `SassException.span` üzerinden erişilebilir.
* Node Sass ve Dart Sass’ın JS API’sinde dosya, satır ve sütun özelliklerine erişilebilir.
