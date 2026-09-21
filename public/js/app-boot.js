(function () {
  var html = document.documentElement;
  var revealed = false;
  var skip = false;
  try {
    skip = sessionStorage.getItem('pos-boot-done') === '1';
  } catch (e) {}

  try {
    if (localStorage.getItem('darkMode') === 'true') html.classList.add('dark');
  } catch (e) {}

  function removeOverlay() {
    var boot = document.getElementById('appBoot');
    if (boot && boot.parentNode) boot.parentNode.removeChild(boot);
  }

  function reveal() {
    if (revealed) return;
    revealed = true;
    html.classList.add('alpine-ready');
    html.classList.remove('is-boot');
    try {
      sessionStorage.setItem('pos-boot-done', '1');
    } catch (e) {}
    var boot = document.getElementById('appBoot');
    if (!boot) return;
    boot.classList.add('is-leaving');
    setTimeout(removeOverlay, 280);
  }

  if (skip) {
    html.classList.remove('is-boot');
    html.classList.add('alpine-ready');
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', removeOverlay);
    } else {
      removeOverlay();
    }
    window.playAppBootOverlay = function () {};
    return;
  }

  html.classList.add('is-boot');

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      setTimeout(reveal, 180);
    });
  } else {
    setTimeout(reveal, 180);
  }

  setTimeout(reveal, 1200);

  window.playAppBootOverlay = function () {};
})();
