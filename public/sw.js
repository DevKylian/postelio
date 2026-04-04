const CACHE_NAME = 'postelio-pwa-v3';
const OFFLINE_URL = '/offline';

// Static shell assets — cached on install, served cache-first
const SHELL_ASSETS = [
  '/offline',
  '/manifest.webmanifest',
  '/images/pwa/icon-192.png',
  '/images/pwa/icon-512.png',
  '/favicon.ico',
  '/js/pwa/offline-queue.js',
];

// App pages — cached after first visit, served network-first with cache fallback
const APP_PAGES = [
  '/dashboard',
  '/generate-post',
  '/posts',
  '/calendar',
  '/post-settings',
  '/statistics',
  '/centre-aide',
  '/profile',
];

// ─── Install: pre-cache the shell ─────────────────────────────────────────────
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(SHELL_ASSETS);
    }).then(() => self.skipWaiting())
  );
});

// ─── Activate: clean up old caches ────────────────────────────────────────────
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((names) =>
      Promise.all(
        names
          .filter((n) => n !== CACHE_NAME)
          .map((n) => caches.delete(n))
      )
    ).then(() => self.clients.claim())
  );
});

// ─── Fetch: routing logic ──────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Only handle http(s) — skip chrome-extension://, etc.
  if (!url.protocol.startsWith('http')) return;

  // Skip admin, webhook, Livewire internal, and Vite HMR routes
  const skipPaths = ['/admin', '/stripe/webhook', '/livewire/', '/__vite', '/@vite'];
  if (skipPaths.some((p) => url.pathname.startsWith(p))) return;

  // Skip non-GET requests (POST/PUT/DELETE go to network only)
  if (request.method !== 'GET') return;

  // ── Static assets (js, css, images, fonts): cache-first ──────────────────
  if (isStaticAsset(url)) {
    event.respondWith(cacheFirst(request));
    return;
  }

  // ── Navigation requests: network-first, fallback to cached page or offline ─
  if (request.mode === 'navigate') {
    event.respondWith(networkFirstNavigate(request, url));
    return;
  }

  // ── Other GET requests (JSON, API reads): network-first ──────────────────
  event.respondWith(networkFirst(request));
});

// ─── Helpers ──────────────────────────────────────────────────────────────────

function isStaticAsset(url) {
  return /\.(js|css|png|jpg|jpeg|gif|svg|webp|ico|woff|woff2|ttf|eot|json)$/.test(url.pathname)
    || url.hostname !== self.location.hostname; // fonts, CDN, etc.
}

async function cacheFirst(request) {
  const cached = await caches.match(request);
  if (cached) return cached;
  try {
    const response = await fetch(request);
    if (response && response.status === 200) {
      const cache = await caches.open(CACHE_NAME);
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    return new Response('', { status: 408 });
  }
}

async function networkFirst(request) {
  try {
    const response = await fetch(request);
    if (response && response.status === 200) {
      const cache = await caches.open(CACHE_NAME);
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    const cached = await caches.match(request);
    return cached || new Response('', { status: 408 });
  }
}

async function networkFirstNavigate(request, url) {
  try {
    const response = await fetch(request);
    if (response && response.status === 200) {
      const cache = await caches.open(CACHE_NAME);
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    // Network failed — serve cached version of this exact page if available
    const cached = await caches.match(request);
    if (cached) return cached;

    // For known app pages, try to serve the cached version from a previous visit
    const isAppPage = APP_PAGES.some((p) => url.pathname === p || url.pathname.startsWith(p + '/'));
    if (isAppPage) {
      // Try the page without query string
      const pageMatch = await caches.match(url.pathname);
      if (pageMatch) return pageMatch;
    }

    // Last resort: offline fallback page
    const offlinePage = await caches.match(OFFLINE_URL);
    return offlinePage || new Response('<h1>Hors-ligne</h1>', {
      headers: { 'Content-Type': 'text/html' }
    });
  }
}
