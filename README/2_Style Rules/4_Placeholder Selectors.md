## 📌 Yer Tutucu Seçiciler (placeholder selectors)

Sass’in, “yer tutucu” (placeholder) olarak bilinen özel bir seçici türü vardır. Bir sınıf seçicisine (class selector) çok benzer şekilde görünür ve davranır, ancak `%` ile başlar ve CSS çıktısına dahil edilmez. Aslında, içinde bir yer tutucu seçici bulunan herhangi bir karmaşık seçici (virgüller arasındaki seçiciler) CSS’e dahil edilmez; seçicilerinin tamamı yer tutucudan oluşan stil kuralları da aynı şekilde dahil edilmez.

### SCSS Sözdizimi

```scss
.alert:hover, %strong-alert {
  font-weight: bold;
}

%strong-alert:hover {
  color: red;
}
```

👉 Bu örnekte `%strong-alert` doğrudan CSS’e yazılmaz.

Peki çıktıya dahil edilmeyen bir seçicinin ne faydası var? **Genişletilebilir (extend edilebilir)** olması! Sınıf seçicilerinden farklı olarak, yer tutucular genişletilmezse CSS’i kalabalıklaştırmaz ve bir kütüphaneyi kullananların HTML için belirli sınıf isimlerini kullanmasını zorunlu kılmaz.

### SCSS Sözdizimi

```scss
%toolbelt {
  box-sizing: border-box;
  border-top: 1px rgba(#000, .12) solid;
  padding: 16px 0;
  width: 100%;

  &:hover { border: 2px rgba(#000, .5) solid; }
}

.action-buttons {
  @extend %toolbelt;
  color: #4285f4;
}

.reset-buttons {
  @extend %toolbelt;
  color: #cddc39;
}
```

👉 Bu örnekte `%toolbelt`, hem `.action-buttons` hem de `.reset-buttons` tarafından genişletilir.

Yer tutucu seçiciler, her stil kuralının kullanılabileceği ya da kullanılmayabileceği Sass kütüphaneleri yazarken faydalıdır. Genel bir kural olarak, yalnızca kendi uygulamanız için bir stil dosyası yazıyorsanız, mevcutsa bir sınıf seçiciyi genişletmek genellikle daha iyi bir çözümdür.
