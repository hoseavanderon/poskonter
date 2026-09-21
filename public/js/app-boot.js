(function () {
  var html = document.documentElement;
  try {
    if (localStorage.getItem('darkMode') === 'true') html.classList.add('dark');
  } catch (e) {}

  html.classList.remove('is-boot');
  html.classList.add('alpine-ready');

  function removeOverlay() {
    var boot = document.getElementById('appBoot');
    if (boot && boot.parentNode) boot.parentNode.removeChild(boot);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', removeOverlay);
  } else {
    removeOverlay();
  }

  window.playAppBootOverlay = function () {};
})();
