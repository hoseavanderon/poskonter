(function () {
  var html = document.documentElement;
  var started = false;

  function play() {
    if (started) return;
    started = true;
    html.classList.remove('page-enter-waiting');
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        html.classList.add('page-enter-play');
        setTimeout(function () {
          html.classList.remove('page-enter-play');
          html.classList.add('page-enter-done');
        }, 1600);
      });
    });
  }

  html.classList.add('page-enter-waiting');

  function bind() {
    var hasOverlay =
      html.classList.contains('is-boot') || document.getElementById('appBoot');

    if (!hasOverlay) {
      play();
      return;
    }

    window.addEventListener('app-boot:reveal', function onReveal() {
      window.removeEventListener('app-boot:reveal', onReveal);
      setTimeout(play, 100);
    });

    var boot = document.getElementById('appBoot');
    if (boot) {
      var bootObs = new MutationObserver(function () {
        if (boot.classList.contains('is-leaving') || !boot.isConnected) {
          bootObs.disconnect();
          setTimeout(play, 100);
        }
      });
      bootObs.observe(boot, { attributes: true, attributeFilter: ['class'] });
    }

    setTimeout(function () {
      if (!started) play();
    }, 2800);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bind);
  } else {
    bind();
  }
})();
