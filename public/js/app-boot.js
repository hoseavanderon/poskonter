(function () {
  var html = document.documentElement;
  var MIN_MS = 700;
  var started = Date.now();
  var revealed = false;

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
    requestAnimationFrame(function () {
      requestAnimationFrame(revealWhenReady);
    });
  });

  setTimeout(function () {
    if (!revealed) reveal();
  }, 1800);

  window.playAppBootOverlay = function () {
    revealed = false;
    started = Date.now();
    html.classList.add('is-boot');
    html.classList.remove('alpine-ready');
    ensureOverlay();
    setTimeout(reveal, MIN_MS);
  };
})();
