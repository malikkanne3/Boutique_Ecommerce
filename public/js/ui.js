/* E-Shop SN — petites améliorations d'ergonomie (aucune dépendance) */
(function () {
  'use strict';

  // Copier un lien : <button data-copy="#champ">
  document.addEventListener('click', function (e) {
    var b = e.target.closest('[data-copy]'); if (!b) return;
    var src = document.querySelector(b.getAttribute('data-copy')); if (!src) return;
    var text = src.value || src.textContent, done = function () { var old = b.textContent; b.textContent = 'Copié ✓'; setTimeout(function () { b.textContent = old; }, 1800); };
    if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(text).then(done); }
    else { src.select && src.select(); try { document.execCommand('copy'); done(); } catch (err) { window.prompt('Copiez ce lien :', text); } }
  });

  // Menu latéral mobile
  document.addEventListener('click', function (e) {
    if (e.target.closest('[data-sidebar-toggle]')) { document.body.classList.toggle('sidebar-open'); }
    else if (e.target.closest('.side-backdrop')) { document.body.classList.remove('sidebar-open'); }
  });

  // Confirmation avant action sensible : <form data-confirm="Message">
  document.addEventListener('submit', function (e) {
    var form = e.target;
    var msg = form.getAttribute('data-confirm');
    if (msg && !window.confirm(msg)) { e.preventDefault(); return; }
    // Bouton en chargement + protection contre le double clic
    var btn = form.querySelector('button:not([type=button]):not([data-no-loading])');
    if (btn && !form.hasAttribute('data-no-loading')) {
      setTimeout(function () { btn.classList.add('is-loading'); }, 0);
    }
  });

  // Soumission automatique d'un filtre : <select data-autosubmit>
  document.addEventListener('change', function (e) {
    if (e.target.matches('[data-autosubmit]')) { e.target.form.submit(); }
    // Aperçu d'image : <input type=file data-preview="#id">
    if (e.target.matches('input[type=file][data-preview]')) {
      var img = document.querySelector(e.target.getAttribute('data-preview'));
      var file = e.target.files && e.target.files[0];
      if (img && file) { img.src = URL.createObjectURL(file); img.classList.remove('d-none'); }
    }
  });

  // Sélecteur de quantité : <div data-qty> [data-qty-minus] <input> [data-qty-plus]
  document.addEventListener('click', function (e) {
    var b = e.target.closest('[data-qty-minus],[data-qty-plus]');
    if (!b) return;
    var box = b.closest('[data-qty]');
    var input = box.querySelector('input');
    var max = parseInt(input.getAttribute('max') || '999', 10);
    var v = parseInt(input.value || '1', 10) + (b.hasAttribute('data-qty-plus') ? 1 : -1);
    input.value = Math.min(Math.max(v, 1), max);
    if (box.hasAttribute('data-qty-submit')) { box.closest('form').submit(); }
  });

  // Barre de chargement en haut de page lors d'une navigation ou d'un envoi de formulaire
  var loader = document.createElement('div');
  loader.id = 'topbar-loader';
  document.addEventListener('DOMContentLoaded', function () { document.body.appendChild(loader); });
  function startLoader() { loader.classList.add('run'); }
  window.addEventListener('pageshow', function () { loader.classList.remove('run'); });
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a[href]');
    if (a && !a.target && !e.ctrlKey && !e.metaKey && a.getAttribute('href').charAt(0) !== '#' && a.origin === location.origin && !a.hasAttribute('download')) { startLoader(); }
  });
  document.addEventListener('submit', function (e) { if (!e.defaultPrevented) { startLoader(); } });

  document.addEventListener('DOMContentLoaded', function () {
    // Compteurs animés : <div class="stat-value" data-count="1250" data-suffix=" FCFA">
    document.querySelectorAll('[data-count]').forEach(function (el) {
      var end = parseFloat(el.getAttribute('data-count')) || 0, suffix = el.getAttribute('data-suffix') || '', t0 = null;
      function step(t) { if (!t0) t0 = t; var p = Math.min((t - t0) / 900, 1), v = Math.round(end * (1 - Math.pow(1 - p, 3)));
        el.textContent = v.toLocaleString('fr-FR').replace(/\u202f/g, ' ') + suffix; if (p < 1) requestAnimationFrame(step); }
      requestAnimationFrame(step);
    });
    // Apparition au défilement : <div class="reveal">
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) { entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } }); }, { threshold: .1 });
      document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
    } else { document.querySelectorAll('.reveal').forEach(function (el) { el.classList.add('in'); }); }
  });

  document.addEventListener('DOMContentLoaded', function () {
    // Toasts Bootstrap
    if (window.bootstrap) {
      document.querySelectorAll('.toast').forEach(function (el) { new bootstrap.Toast(el, { delay: 4500 }).show(); });
    }
    // Chat : défile vers le dernier message
    var chat = document.querySelector('.chat-body');
    if (chat) { chat.scrollTop = chat.scrollHeight; }
  });
})();
