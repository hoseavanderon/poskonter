(function () {
  var html = document.documentElement;
  var MIN_MS = 900;
  var started = Date.now();
  var revealed = false;
  var alpineDone = false;

  try {
    if (localStorage.getItem('darkMode') === 'true') html.classList.add('dark');
  } catch (e) {}

  html.classList.add('is-boot');

  function overlayMarkup() {
    return (
      '<div class="app-boot" id="appBoot" aria-hidden="true">' +
      '<span class="app-boot-orb app-boot-orb-a"></span>' +
      '<span class="app-boot-orb app-boot-orb-b"></span>' +
      '<div class="app-boot-stage">' +
      '<span class="app-boot-ring"></span>' +
      '<span class="app-boot-ring app-boot-ring-2"></span>' +
      '<span class="app-boot-ring app-boot-ring-3"></span>' +
      '<div class="app-boot-hero">POS</div>' +
      '</div>' +
      '<span class="app-boot-label">POS Konter</span>' +
      '</div>'
    );
  }

  function ensureOverlay() {
    var boot = document.getElementById('appBoot');
    if (boot) {
      boot.classList.remove('is-leaving');
      boot.removeAttribute('style');
      return boot;
    }
    if (!document.body) return null;
    document.body.insertAdjacentHTML('afterbegin', overlayMarkup());
    return document.getElementById('appBoot');
  }

  function reveal() {
    if (revealed) return;
    revealed = true;
    html.classList.add('alpine-ready');
    html.classList.remove('is-boot');
    try {
      window.dispatchEvent(new CustomEvent('app-boot:reveal'));
    } catch (e) {}
    var boot = document.getElementById('appBoot');
    if (!boot) return;
    boot.classList.add('is-leaving');
    boot.addEventListener('animationend', function () {
      if (boot.parentNode) boot.remove();
    });
    setTimeout(function () {
      if (boot.parentNode) boot.remove();
    }, 900);
  }

  function revealWhenReady() {
    var wait = Math.max(0, MIN_MS - (Date.now() - started));
    setTimeout(reveal, wait);
  }

  document.addEventListener('alpine:initialized', function () {
    alpineDone = true;
    requestAnimationFrame(function () {
      requestAnimationFrame(revealWhenReady);
    });
  });

  setTimeout(function () {
    if (!revealed) revealWhenReady();
  }, 2400);

  window.playAppBootOverlay = function () {
    revealed = false;
    started = Date.now();
    html.classList.add('is-boot');
    html.classList.remove('alpine-ready');
    ensureOverlay();
    setTimeout(reveal, MIN_MS);
  };

  function isInternalLink(anchor) {
    if (!anchor || !anchor.getAttribute) return false;
    var href = anchor.getAttribute('href');
    if (!href || href.charAt(0) === '#' || href.indexOf('javascript:') === 0) return false;
    if (anchor.hasAttribute('download')) return false;
    if (anchor.target && anchor.target !== '_self') return false;
    var url;
    try {
      url = new URL(anchor.href, location.href);
    } catch (err) {
      return false;
    }
    if (url.origin !== location.origin) return false;
    if (url.pathname === location.pathname && url.search === location.search) return false;
    return url.href;
  }

  document.addEventListener(
    'click',
    function (e) {
      if (e.defaultPrevented || e.button !== 0) return;
      if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
      var anchor = e.target.closest ? e.target.closest('a[href]') : null;
      var next = isInternalLink(anchor);
      if (!next) return;

      e.preventDefault();
      revealed = false;
      alpineDone = false;
      started = Date.now();
      html.classList.add('is-boot');
      html.classList.remove('alpine-ready');
      ensureOverlay();
      setTimeout(function () {
        location.href = next;
      }, 160);
    },
    true
  );
})();
