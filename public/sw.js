const CACHE = 'poskonter-pwa-v1';
const PRECACHE = [
  'offline.html',
  'manifest.webmanifest',
  'icons/icon-192.png',
  'icons/icon-512.png',
  'icons/apple-touch-icon.png',
  'css/sf-pro.css',
  'css/login.css',
  'css/app-boot.css',
  'js/pwa.js',
  'js/app-boot.js',
  'js/login.js'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE).then((cache) => {
      return Promise.all(
        PRECACHE.map((path) => {
          const url = new URL(path, self.registration.scope).href;
          return cache.add(url).catch(function () {});
        })
      );
    }).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((key) => key !== CACHE).map((key) => caches.delete(key)))
    ).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const request = event.request;
  if (request.method !== 'GET') {
    return;
  }

  const url = new URL(request.url);
  if (url.origin !== self.location.origin) {
    return;
  }

  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request).catch(() => caches.match(new URL('offline.html', self.registration.scope).href))
    );
    return;
  }

  if (!/\.(?:css|js|png|jpg|jpeg|gif|webp|svg|woff2?|mp3|webmanifest)$/i.test(url.pathname)) {
    return;
  }

  event.respondWith(
    caches.match(request).then((cached) => {
      const fetched = fetch(request)
        .then((response) => {
          if (response && response.ok) {
            const copy = response.clone();
            caches.open(CACHE).then((cache) => cache.put(request, copy));
          }
          return response;
        })
        .catch(() => cached);
      return cached || fetched;
    })
  );
});
