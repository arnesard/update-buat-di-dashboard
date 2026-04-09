// Service Worker — Penerimaan Produksi PWA
const CACHE_NAME = 'receiving-v1';

// Files to cache for offline shell
const STATIC_ASSETS = [
    '/web_receiving/public/css/bootstrap.min.css',
    '/web_receiving/public/js/bootstrap.bundle.min.js',
    '/web_receiving/public/js/echarts.min.js',
    '/web_receiving/public/js/lucide.min.js',
    '/web_receiving/public/images/icon-512.png',
    '/web_receiving/public/images/logo-gt.png'
];

// Install — pre-cache static assets
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function(cache) {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

// Activate — clean old caches
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(keys) {
            return Promise.all(
                keys.filter(function(key) {
                    return key !== CACHE_NAME;
                }).map(function(key) {
                    return caches.delete(key);
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch — Network First strategy (always try server first, fallback to cache)
// This is ideal for intranet apps where the server is usually available
self.addEventListener('fetch', function(event) {
    // Skip non-GET requests
    if (event.request.method !== 'GET') return;

    event.respondWith(
        fetch(event.request)
            .then(function(response) {
                // Clone and cache successful responses
                if (response.status === 200) {
                    var responseClone = response.clone();
                    caches.open(CACHE_NAME).then(function(cache) {
                        cache.put(event.request, responseClone);
                    });
                }
                return response;
            })
            .catch(function() {
                // Network failed, try cache
                return caches.match(event.request).then(function(cached) {
                    return cached || new Response('Offline — Hubungkan ke jaringan kantor', {
                        status: 503,
                        headers: { 'Content-Type': 'text/plain; charset=utf-8' }
                    });
                });
            })
    );
});
