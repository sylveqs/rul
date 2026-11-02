(function () {
  'use strict';

  /* Выезжающие уведомления */
  const box = document.getElementById('toasts');
  function toast(type, text) {
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.textContent = text;
    box.appendChild(t);
    requestAnimationFrame(() => t.classList.add('show'));
    setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 400); }, 3500);
  }
  try { JSON.parse(box.dataset.flash || '[]').forEach(([type, text]) => toast(type, text)); } catch (e) {}

  /* Меню */
  const burger = document.querySelector('[data-burger]');
  if (burger) burger.addEventListener('click', () => {
    const open = document.getElementById('menu').classList.toggle('open');
    burger.setAttribute('aria-expanded', open);
  });

  /* Слайдер: автосмена каждые 3 с, кнопки вперёд/назад */
  document.querySelectorAll('[data-slider]').forEach(root => {
    const track = root.querySelector('.slides');
    const n = track.children.length;
    const dots = root.querySelector('.dots');
    let i = 0, timer;
    for (let k = 0; k < n; k++) {
      const d = document.createElement('button');
      d.setAttribute('aria-label', 'Слайд ' + (k + 1));
      d.addEventListener('click', () => { go(k); restart(); });
      dots.appendChild(d);
    }
    function go(k) {
      i = (k + n) % n;
      track.style.transform = 'translateX(' + (-100 * i) + '%)';
      [...dots.children].forEach((d, j) => d.classList.toggle('active', j === i));
    }
    function restart() { clearInterval(timer); timer = setInterval(() => go(i + 1), 3000); }
    root.querySelector('[data-prev]').addEventListener('click', () => { go(i - 1); restart(); });
    root.querySelector('[data-next]').addEventListener('click', () => { go(i + 1); restart(); });
    go(0); restart();
  });

  /* Клиентская валидация: подсветка ошибки и тряска поля */
  document.querySelectorAll('form[novalidate]').forEach(form => {
    form.addEventListener('submit', ev => {
      let bad = false;
      form.querySelectorAll('.input[required]').forEach(el => {
        const p = el.getAttribute('pattern');
        const invalid = !el.value.trim() || (p && !new RegExp('^' + p + '$').test(el.value)) ||
          (el.minLength > 0 && el.value.length < el.minLength);
        el.classList.toggle('is-invalid', invalid);
        if (invalid) { bad = true; el.classList.remove('shake'); void el.offsetWidth; el.classList.add('shake'); }
      });
      if (bad) { ev.preventDefault(); toast('error', 'Проверьте выделенные поля'); }
    });
    form.addEventListener('input', ev => ev.target.classList.remove('is-invalid'));
  });
})();
