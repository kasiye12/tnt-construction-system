const CACHE_NAME = 'tnt-construction-v1';
const urlsToCache = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/offline'
];

// Install Service Worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

// Cache and return requests
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Return cached version or fetch new
                return response || fetch(event.request)
                    .then(fetchResponse => {
                        // Cache new requests for offline
                        if (event.request.method === 'GET') {
                            const responseClone = fetchResponse.clone();
                            caches.open(CACHE_NAME)
                                .then(cache => cache.put(event.request, responseClone));
                        }
                        return fetchResponse;
                    });
            })
            .catch(() => {
                // Return offline page if no cache
                if (event.request.mode === 'navigate') {
                    return caches.match('/offline');
                }
            })
    );
});

// Update cache
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Background sync for offline reports
self.addEventListener('sync', event => {
    if (event.tag === 'sync-reports') {
        event.waitUntil(syncReports());
    }
    if (event.tag === 'sync-checkins') {
        event.waitUntil(syncCheckins());
    }
});

async function syncReports() {
    const db = await openDB();
    const reports = await db.getAll('pending_reports');
    
    for (const report of reports) {
        try {
            await fetch('/api/sync/reports', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ reports: [report] })
            });
            await db.delete('pending_reports', report.id);
        } catch (error) {
            console.error('Sync failed:', error);
        }
    }
}

async function syncCheckins() {
    const db = await openDB();
    const checkins = await db.getAll('pending_checkins');
    
    for (const checkin of checkins) {
        try {
            await fetch('/api/sync/checkins', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ checkins: [checkin] })
            });
            await db.delete('pending_checkins', checkin.id);
        } catch (error) {
            console.error('Sync failed:', error);
        }
    }
}
