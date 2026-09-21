(function () {
  if (!('serviceWorker' in navigator)) {
    return;
  }

  var script = document.currentScript || document.querySelector('script[data-sw]');
  var swUrl = script && script.getAttribute('data-sw');
  if (!swUrl) {
    return;
  }

  window.addEventListener('load', function () {
    navigator.serviceWorker.register(swUrl).catch(function () {});
  });
})();
