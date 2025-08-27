document.addEventListener('DOMContentLoaded', () => {
  const input = document.querySelector('.search-input');
  if (!input) return;

  const debounce = (fn, wait = 150) => {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); };
  };

  const escapeRegExp = (s) => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

  const clearHighlights = (root) => {
    root.querySelectorAll('mark.hl').forEach(mark => {
      const t = document.createTextNode(mark.textContent);
      mark.replaceWith(t);
    });
  };

  const highlightElement = (el, q) => {
    const re = new RegExp(`(${escapeRegExp(q)})`, 'gi');
    // Sadece metin düğümlerini işleyelim, attribute'ları bozmayalım
    const walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null);
    const nodes = [];
    while (walker.nextNode()) nodes.push(walker.currentNode);
    nodes.forEach(node => {
      const parent = node.parentNode;
      const text = node.nodeValue;
      if (!text || q.length < 2 || !re.test(text)) return;
      const frag = document.createDocumentFragment();
      let lastIndex = 0;
      text.replace(re, (m, p1, offset) => {
        if (offset > lastIndex) frag.appendChild(document.createTextNode(text.slice(lastIndex, offset)));
        const mark = document.createElement('mark');
        mark.className = 'hl';
        mark.textContent = m;
        frag.appendChild(mark);
        lastIndex = offset + m.length;
      });
      if (lastIndex < text.length) frag.appendChild(document.createTextNode(text.slice(lastIndex)));
      parent.replaceChild(frag, node);
    });
  };

  const inPageSearch = (q) => {
    const listItems = document.querySelectorAll('.project-list li');
    if (listItems.length) {
      listItems.forEach(li => {
        const text = (li.textContent || '').toLowerCase();
        const match = q && text.includes(q.toLowerCase());
        li.classList.toggle('is-hidden', q && !match);
        const link = li.querySelector('a');
        if (link) highlightElement(link, q);
      });
    }

    const content = document.querySelector('.main-content');
    if (content) {
      clearHighlights(content);
      if (q && q.length >= 2) {
        content.querySelectorAll('.doc-article h1, .doc-article h2, .doc-article h3, .doc-article p').forEach(node => {
          highlightElement(node, q);
        });
        // Arama sonuçlarında dosya adı linklerini vurgula (href bozulmaz)
        document.querySelectorAll('.search-result-file a').forEach(a => highlightElement(a, q));
      }
    }
  };

  input.addEventListener('input', debounce(() => inPageSearch(input.value)));
});
