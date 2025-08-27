@extend
There are often cases when designing a page when one class should have all the styles of another class, as well as its own specific styles. For example, the BEM methodology encourages modifier classes that go on the same elements as block or element classes. But this can create cluttered HTML, it’s prone to errors from forgetting to include both classes, and it can bring non-semantic style concerns into your markup.

<div class="error error--serious">
  Oh no! You've been hacked!
</div>
.error {
  border: 1px #f00;
  background-color: #fdd;
}

.error--serious {
  border-width: 3px;
}
Sass’s @extend rule solves this. It’s written @extend <selector>, and it tells Sass that one selector should inherit the styles of another.

SCSSSassCSS
SassPlayground
SCSS Syntax
.error {
  border: 1px #f00;
  background-color: #fdd;

  &--serious {
    @extend .error;
    border-width: 3px;
  }
}
When one class extends another, Sass styles all elements that match the extender as though they also match the class being extended. When one class selector extends another, it works exactly as though you added the extended class to every element in your HTML that already had the extending class. You can just write class="error--serious", and Sass will make sure it’s styled as though it had class="error" as well.

Of course, selectors aren’t just used on their own in style rules. Sass knows to extend everywhere the selector is used. This ensures that your elements are styled exactly as if they matched the extended selector.

SCSSSassCSS
SassPlayground
SCSS Syntax
.error:hover {
  background-color: #fee;
}

.error--serious {
  @extend .error;
  border-width: 3px;
}
⚠️ Heads up!
Extends are resolved after the rest of your stylesheet is compiled. In particular, it happens after parent selectors are resolved. This means that if you @extend .error, it won’t affect the inner selector in .error { &__icon { ... } }. It also means that parent selectors in SassScript can’t see the results of extend.

How It WorksHow It Works permalink
Unlike mixins, which copy styles into the current style rule, @extend updates style rules that contain the extended selector so that they contain the extending selector as well. When extending selectors, Sass does intelligent unification:

It never generates selectors like #main#footer that can’t possibly match any elements.

It ensures that complex selectors are interleaved so that they work no matter which order the HTML elements are nested.

It trims redundant selectors as much as possible, while still ensuring that the specificity is greater than or equal to that of the extender.

It knows when one selector matches everything another does, and can combine them together.

It intelligently handles combinators, universal selectors, and pseudo-classes that contain selectors.

SCSSSassCSS
SassPlayground
SCSS Syntax
.content nav.sidebar {
  @extend .info;
}

// This won't be extended, because `p` is incompatible with `nav`.
p.info {
  background-color: #dee9fc;
}

// There's no way to know whether `<div class="guide">` will be inside or
// outside `<div class="content">`, so Sass generates both to be safe.
.guide .info {
  border: 1px solid rgba(#000, 0.8);
  border-radius: 2px;
}

// Sass knows that every element matching "main.content" also matches ".content"
// and avoids generating unnecessary interleaved selectors.
main.content .info {
  font-size: 0.8em;
}
💡 Fun fact:
You can directly access Sass’s intelligent unification using selector functions! The selector.unify() function returns a selector that matches the intersection of two selectors, while the selector.extend() function works just like @extend, but on a single selector.

⚠️ Heads up!
Because @extend updates style rules that contain the extended selector, their styles have precedence in the cascade based on where the extended selector’s style rules appear, not based on where the @extend appears. This can be confusing, but just remember: this is the same precedence those rules would have if you added the extended class to your HTML!

Placeholder SelectorsPlaceholder Selectors permalink
Sometimes you want to write a style rule that’s only intended to be extended. In that case, you can use placeholder selectors, which look like class selectors that start with % instead of .. Any selectors that include placeholders aren’t included in the CSS output, but selectors that extend them are.

SCSSSassCSS
SassPlayground
SCSS Syntax
.alert:hover, %strong-alert {
  font-weight: bold;
}

%strong-alert:hover {
  color: red;
}
Private PlaceholdersPrivate Placeholders permalink
Like module members, a placeholder selector can be marked private by starting its name with either - or _. A private placeholder selector can only be extended within the stylesheet that defines it. To any other stylesheets, it will look as though that selector doesn’t exist.

Extension ScopeExtension Scope permalink
When one stylesheet extends a selector, that extension will only affect style rules written in upstream modules—that is, modules that are loaded by that stylesheet using the @use rule or the @forward rule, modules loaded by those modules, and so on. This helps make your @extend rules more predictable, ensuring that they affect only the styles you were aware of when you wrote them.

⚠️ Heads up!
Extensions aren’t scoped at all if you’re using the @import rule. Not only will they affect every stylesheet you import, they’ll affect every stylesheet that imports your stylesheet, everything else those stylesheets import, and so on. Without @use, extensions are global.

Mandatory and Optional ExtendsMandatory and Optional Extends permalink
Normally, if an @extend doesn’t match any selectors in the stylesheet, Sass will produce an error. This helps protect from typos or from renaming a selector without renaming the selectors that inherit from it. Extends that require that the extended selector exists are mandatory.

This may not always be what you want, though. If you want the @extend to do nothing if the extended selector doesn’t exist, just add !optional to the end.

Extends or Mixins?Extends or Mixins? permalink
Extends and mixins are both ways of encapsulating and re-using styles in Sass, which naturally raises the question of when to use which one. Mixins are obviously necessary when you need to configure the styles using arguments, but what if they’re just a chunk of styles?

As a rule of thumb, extends are the best option when you’re expressing a relationship between semantic classes (or other semantic selectors). Because an element with class .error--serious is an error, it makes sense for it to extend .error. But for non-semantic collections of styles, writing a mixin can avoid cascade headaches and make it easier to configure down the line.

💡 Fun fact:
Most web servers compress the CSS they serve using an algorithm that’s very good at handling repeated chunks of identical text. This means that, although mixins may produce more CSS than extends, they probably won’t substantially increase the amount your users need to download. So choose the feature that makes the most sense for your use-case, not the one that generates the least CSS!

LimitationsLimitations permalink
Disallowed SelectorsDisallowed Selectors permalink
Compatibility (No Compound Extensions):
Dart Sass
✓
LibSass
✗
Ruby Sass
✗
➤
Only simple selectors—individual selectors like .info or a—can be extended. If .message.info could be extended, the definition of @extend says that elements matching the extender would be styled as though they matched .message.info. That’s just the same as matching both .message and .info, so there wouldn’t be any benefit in writing that instead of @extend .message, .info.

Similarly, if .main .info could be extended, it would do (almost) the same thing as extending .info on its own. The subtle differences aren’t worth the confusion of looking like it’s doing something substantially different, so this isn’t allowed either.

SCSSSass
SassPlayground
SCSS Syntax
.alert {
  @extend .message.info;
  //      ^^^^^^^^^^^^^
  // Error: Write @extend .message, .info instead.

  @extend .main .info;
  //      ^^^^^^^^^^^
  // Error: write @extend .info instead.
}
HTML HeuristicsHTML Heuristics permalink
When @extend interleaves complex selectors, it doesn’t generate all possible combinations of ancestor selectors. Many of the selectors it could generate are unlikely to actually match real HTML, and generating them all would make stylesheets way too big for very little real value. Instead, it uses a heuristic: it assumes that each selector’s ancestors will be self-contained, without being interleaved with any other selector’s ancestors.

SCSSSassCSS
SassPlayground
SCSS Syntax
header .warning li {
  font-weight: bold;
}

aside .notice dd {
  // Sass doesn't generate CSS to match the <dd> in
  //
  // <header>
  //   <aside>
  //     <div class="warning">
  //       <div class="notice">
  //         <dd>...</dd>
  //       </div>
  //     </div>
  //   </aside>
  // </header>
  //
  // because matching all elements like that would require us to generate nine
  // new selectors instead of just two.
  @extend li;
}
Extend in @mediaExtend in @media permalink
While @extend is allowed within @media and other CSS at-rules, it’s not allowed to extend selectors that appear outside its at-rule. This is because the extending selector only applies within the given media context, and there’s no way to make sure that restriction is preserved in the generated selector without duplicating the entire style rule.

SCSSSass
SassPlayground
SCSS Syntax
@media screen and (max-width: 600px) {
  .error--serious {
    @extend .error;
    //      ^^^^^^
    // Error: ".error" was extended in @media, but used outside it.
  }
}

.error {
  border: 1px #f00;
  background-color: #fdd;
}