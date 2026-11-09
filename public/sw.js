const CACHE_NAME = 'monev-teknisi-v1';
const urlsToCache = [
    '/',
    '/login',
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png'
];

// Install Service Worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(urlsToCache);
            })
    );
});

// Ambil file dari cache jika offline
self.addEventListener('fetch', event => {
    // Jangan cache request POST (seperti simpan absen atau simpan tugas)
    if (event.request.method !== 'GET') return;

    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Jika ada di cache, pakai itu. Jika tidak, ambil dari internet.
                return response || fetch(event.request);
            })
    );
});

// Hapus cache lama jika ada update
self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});