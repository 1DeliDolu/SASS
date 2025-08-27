## 🔄 Akış Kontrol Kuralları (flow control rules)

Sass, stillerin çıktı alınıp alınmayacağını kontrol etmeyi veya küçük varyasyonlarla birden çok kez çıktı almayı mümkün kılan birkaç `at-rule` sağlar. Bunlar ayrıca Sass yazmayı kolaylaştırmak için küçük algoritmalar yazmak amacıyla `mixin`ler (mixins) ve `fonksiyon`larda (functions) da kullanılabilir. Sass dört adet akış kontrol kuralını (flow control rules) destekler.

* `@if` bir bloğun değerlendirilip değerlendirilmeyeceğini kontrol eder.
* `@each` bir listedeki her eleman veya bir haritadaki (map) her çift için bir bloğu değerlendirir.
* `@for` bir bloğu belirli sayıda kez değerlendirir.
* `@while` belirli bir koşul sağlanana kadar bir bloğu değerlendirir.
