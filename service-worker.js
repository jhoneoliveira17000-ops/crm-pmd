const CACHE_NAME = 'pmdcrm-v1.4.0';
const STATIC_ASSETS = [
    './',
    './index.php',
    './offline.html',
    './manifest.json',
    './js/theme-loader.js'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', event => {
    // Exclude API calls and webhook requests from the static cache
    if (event.request.url.includes('/api/') || event.request.url.includes('webhook')) {
        return;
    }

    event.respondWith(
        caches.match(event.request).then(cachedResponse => {
            const fetchPromise = fetch(event.request).then(networkResponse => {
                // Cache dynamic resources
                if (networkResponse && networkResponse.status === 200 && event.request.method === 'GET') {
                    const url = event.request.url;
                    // Avoid caching external live Tailwind CDN scripts that self-update
                    if (!url.includes('cdn.tailwindcss.com') && !url.includes('google-analytics')) {
                        caches.open(CACHE_NAME).then(cache => cache.put(event.request, networkResponse.clone()));
                    }
                }
                return networkResponse;
            }).catch(() => {
                // Fallback to offline page for page navigations
                if (event.request.headers.get('accept') && event.request.headers.get('accept').includes('text/html')) {
                    return caches.match('./offline.html');
                }
            });

            return cachedResponse || fetchPromise;
        })
    );
});
