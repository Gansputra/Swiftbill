const CACHE_NAME = 'swiftbill-pos-cache-v1';
const OFFLINE_URL = '/offline.html';

const urlsToCache = [
    '/',
    OFFLINE_URL,
    '/icon-192x192.png',
    '/icon-512x512.png'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('activate', (event) => {
    const cacheAllowlist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheAllowlist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

self.addEventListener('fetch', (event) => {
    // Only intercept navigation requests (when the user browses to a new page)
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => {
                // Return offline page if the network request fails
                return caches.match(OFFLINE_URL);
            })
        );
    } else {
        // For other requests (like API calls or assets), try the network, then cache
        event.respondWith(
            fetch(event.request).catch(() => caches.match(event.request))
        );
    }
});
