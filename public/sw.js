/**
 * Yoimo Workspace - Smart PWA Service Worker (v2)
 * Powered by LUNOU
 */

const CACHE_NAME = 'yoimo-lunou-pwa-v2';

const STATIC_PRECACHE = [
  '/offline.html',
  '/manifest.json',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png',
  '/icons/maskable-icon-512x512.png',
  '/icons/apple-touch-icon.png',
  '/logopanjang.png',
  '/favicon.ico'
];

// Install: Cache essential shell & offline fallback
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_PRECACHE);
    })
  );
  self.skipWaiting();
});

// Activate: Clean up older caches (e.g. yotayoti-cache-v1)
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((name) => {
          if (name !== CACHE_NAME) {
            return caches.delete(name);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch: Smart routing (Network-First for Navigations, Cache-First for Static Assets)
self.addEventListener('fetch', (event) => {
  const request = event.request;

  // 1. Only intercept GET requests
  if (request.method !== 'GET') {
    return;
  }

  const url = new URL(request.url);

  // 2. Ignore non-http(s) schemas
  if (!url.protocol.startsWith('http')) {
    return;
  }

  // 3. Bypass authentication & session mutation endpoints completely
  const bypassPaths = ['/logout', '/login', '/register', '/password', '/demo-login', '/sanctum'];
  if (bypassPaths.some((p) => url.pathname.startsWith(p))) {
    return;
  }

  // 4. HTML Page Navigations: NETWORK-FIRST (prevents ERR_FAILED & stale session on logout)
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .catch(() => {
          // If completely offline, return friendly branded LUNOU offline page
          return caches.match('/offline.html');
        })
    );
    return;
  }

  // 5. Static Assets (Images, Icons, Vite Build assets, CSS, JS, Fonts): Cache-First / Stale-While-Revalidate
  const isStaticAsset = 
    url.pathname.startsWith('/icons/') ||
    url.pathname.startsWith('/icon/') ||
    url.pathname.startsWith('/images/') ||
    url.pathname.startsWith('/build/') ||
    url.pathname.match(/\.(png|jpg|jpeg|svg|webp|ico|css|js|woff2|woff|ttf)$/i);

  if (isStaticAsset) {
    event.respondWith(
      caches.match(request).then((cachedResponse) => {
        if (cachedResponse) {
          // Optional background revalidate
          fetch(request).then((networkResponse) => {
            if (networkResponse && networkResponse.status === 200) {
              caches.open(CACHE_NAME).then((cache) => cache.put(request, networkResponse));
            }
          }).catch(() => {});
          return cachedResponse;
        }

        return fetch(request).then((networkResponse) => {
          if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic') {
            return networkResponse;
          }
          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(request, responseToCache));
          return networkResponse;
        }).catch(() => {
          // Fallback if needed
        });
      })
    );
  }
});
