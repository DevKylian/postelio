const CACHE_NAME = 'postelio-pwa-v1';
const OFFLINE_URL = '/offline'; // We should probably create this view

const ASSETS_TO_CACHE = [
  '/',
  '/offline',
  '/manifest.webmanifest',
  '/images/pwa/icon-192.png',
  '/images/pwa/icon-512.png',
  '/favicon.ico',
  'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
  // You might want to add build artifacts here too, but they are usually hashed by Vite.
  // We can use a simpler approach for now.
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.filter((name) => name !== CACHE_NAME).map((name) => caches.delete(name))
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  // Only handle GET requests
  if (event.request.method !== 'GET') return;

  // Don't cache admin routes or stripe webhooks
  if (event.request.url.includes('/admin') || event.request.url.includes('/stripe/webhook')) {
    return;
  }

  // Only handle HTTP and HTTPS schemes
  if (!event.request.url.startsWith('http')) return;

  event.respondWith(
    fetch(event.request)
      .then((response) => {
        // If we have a successful network response, clone it to cache
        if (response && response.status === 200) {
          const responseToCache = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
        }
        return response;
      })
      .catch(() => {
        // If network fails, try to get from cache
        return caches.match(event.request).then((cachedResponse) => {
          if (cachedResponse) return cachedResponse;
          
          // If it's a navigation request and both fail, return default offline or landing
          if (event.request.mode === 'navigate') {
            return caches.match('/');
          }
        });
      })
  );
});
