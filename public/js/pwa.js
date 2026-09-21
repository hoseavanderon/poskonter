(function () {
  var script = document.currentScript || document.querySelector('script[data-sw]');
  var swUrl = script && script.getAttribute('data-sw');
  var iconUrl = (script && script.getAttribute('data-icon')) || '';
  var DISMISS_KEY = 'pwa-install-dismissed';
  var DISMISS_MS = 14 * 24 * 60 * 60 * 1000;
  var deferredPrompt = null;
  var banner = null;

  function isStandalone() {
    return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
  }

  function isIos() {
    return /iphone|ipad|ipod/i.test(navigator.userAgent);
  }

  function dismissedRecently() {
    try {
      var at = Number(localStorage.getItem(DISMISS_KEY) || 0);
      return at && Date.now() - at < DISMISS_MS;
    } catch (e) {
      return false;
    }
  }

  function rememberDismiss() {
    try {
      localStorage.setItem(DISMISS_KEY, String(Date.now()));
    } catch (e) {}
  }

  function hideBanner() {
    if (banner && banner.parentNode) banner.parentNode.removeChild(banner);
    banner = null;
  }

  function showBanner(mode) {
    if (banner || isStandalone() || dismissedRecently()) return;

    banner = document.createElement('div');
    banner.className = 'pwa-install';
    banner.setAttribute('role', 'dialog');
    banner.setAttribute('aria-label', 'Pasang POS Konter');

    var img = iconUrl ? '<img src="' + iconUrl + '" alt="">' : '';
    var text =
      mode === 'ios'
        ? 'Di Safari, ketuk tombol Bagikan lalu <strong>Add to Home Screen</strong>.'
        : 'Pasang ke perangkat supaya lebih cepat dibuka seperti aplikasi.';

    banner.innerHTML =
      img +
      '<div class="pwa-install-copy"><strong>Pasang POS Konter</strong><span>' +
      text +
      '</span></div>' +
      '<div class="pwa-install-actions">' +
      (mode === 'ios'
        ? '<button type="button" class="pwa-install-later" data-pwa-later>Tutup</button>'
        : '<button type="button" class="pwa-install-later" data-pwa-later>Nanti</button>' +
          '<button type="button" class="pwa-install-btn" data-pwa-install>Pasang</button>') +
      '</div>';

    document.body.appendChild(banner);

    banner.addEventListener('click', function (e) {
      var later = e.target.closest('[data-pwa-later]');
      var install = e.target.closest('[data-pwa-install]');
      if (later) {
        rememberDismiss();
        hideBanner();
      }
      if (install && deferredPrompt) {
        var promptEvent = deferredPrompt;
        deferredPrompt = null;
        promptEvent.prompt();
        promptEvent.userChoice.then(function (choice) {
          if (choice && choice.outcome !== 'accepted') rememberDismiss();
          hideBanner();
        });
      }
    });
  }

  function scheduleBanner(mode) {
    setTimeout(function () {
      if (document.getElementById('appBoot')) {
        setTimeout(function () {
          showBanner(mode);
        }, 900);
        return;
      }
      showBanner(mode);
    }, 1400);
  }

  if ('serviceWorker' in navigator && swUrl) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register(swUrl).catch(function () {});
    });
  }

  window.addEventListener('beforeinstallprompt', function (event) {
    event.preventDefault();
    deferredPrompt = event;
    scheduleBanner('chrome');
  });

  window.addEventListener('appinstalled', function () {
    deferredPrompt = null;
    hideBanner();
    try {
      localStorage.removeItem(DISMISS_KEY);
    } catch (e) {}
  });

  if (isIos() && !isStandalone() && !dismissedRecently()) {
    scheduleBanner('ios');
  }
})();
