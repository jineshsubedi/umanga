const CACHE_NAME = 'umng-hris-cache-v1';
const OFFLINE_URL = '/offline.html';

const ASSETS_TO_CACHE = [
    OFFLINE_URL,
    '/icons/logo.png',
    '/manifest.json'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS_TO_CACHE);
        }).catch((err) => {
            // Don't let a caching failure prevent the SW from installing
            console.warn('SW: caching failed during install, continuing anyway.', err);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    // Only intercept navigational requests
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => {
                return caches.match(OFFLINE_URL);
            })
        );
    } else {
        // For other requests, try network first, then cache
        event.respondWith(
            fetch(event.request).catch(() => {
                return caches.match(event.request);
            })
        );
    }
});

self.addEventListener('push', function (event) {
    let data = {};
    try {
        data = event.data?.json() ?? {};
    } catch (e) {
        data = { title: 'Notification', body: event.data?.text() ?? '' };
    }

    event.waitUntil(
        self.registration.showNotification(data.title || 'UMNG', {
            body: data.body,
            icon: data.icon || '/icons/logo.png',
            badge: data.badge,
            data: data.data || {},
            actions: data.actions || [],
            requireInteraction: true,
            vibrate: data.vibrate || [200, 100, 200, 100, 200, 100, 200],
        })
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const urlToOpen = event.notification.data.url || '/';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then(function (windowClients) {
            let matchingClient = null;

            for (let i = 0; i < windowClients.length; i++) {
                const windowClient = windowClients[i];
                if (windowClient.url.includes(urlToOpen)) {
                    matchingClient = windowClient;
                    break;
                }
            }

            if (matchingClient) {
                return matchingClient.focus();
            } else {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});
