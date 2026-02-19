/// <reference lib="webworker" />

import { clientsClaim } from 'workbox-core';
import { CacheableResponsePlugin } from 'workbox-cacheable-response';
import { ExpirationPlugin } from 'workbox-expiration';
import { cleanupOutdatedCaches, matchPrecache, precacheAndRoute } from 'workbox-precaching';
import { registerRoute, setCatchHandler } from 'workbox-routing';
import { NetworkFirst, StaleWhileRevalidate } from 'workbox-strategies';

const LOG_PREFIX = '[PWA][SW]';
const DEBUG_HOSTS = new Set(['localhost', '127.0.0.1']);
let debugEnabled = DEBUG_HOSTS.has(self.location.hostname);
const AUTH_BYPASS_PATHS = ['/auth/google', '/auth/google/callback'];

function isAuthNavigation(url) {
    return AUTH_BYPASS_PATHS.some((path) => url.pathname.startsWith(path));
}

function log(event, details) {
    if (!debugEnabled) return;
    if (typeof details === 'undefined') {
        console.log(`${LOG_PREFIX} ${event}`);
        return;
    }
    console.log(`${LOG_PREFIX} ${event}:`, details);
}

function warn(event, details) {
    if (!debugEnabled) return;
    if (typeof details === 'undefined') {
        console.warn(`${LOG_PREFIX} ${event}`);
        return;
    }
    console.warn(`${LOG_PREFIX} ${event}:`, details);
}

const debugPlugin = {
    handlerWillStart: async ({ request }) => {
        log('fetch start', `${request.method} ${request.url}`);
    },
    cachedResponseWillBeUsed: async ({ cacheName, request, cachedResponse }) => {
        log(cachedResponse ? 'cache hit' : 'cache miss', `${cacheName} -> ${request.url}`);
        return cachedResponse;
    },
    cacheDidUpdate: async ({ cacheName, request }) => {
        log('cache updated', `${cacheName} -> ${request.url}`);
    },
    fetchDidFail: async ({ request, error }) => {
        warn('network failed', `${request.url} (${error ? error.message : 'unknown error'})`);
    },
};

self.addEventListener('install', () => {
    log('install');
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    log('activate');
    event.waitUntil(self.clients.claim());
});

self.addEventListener('message', (event) => {
    const data = event.data || {};

    if (data.type === 'PWA_DEBUG') {
        debugEnabled = Boolean(data.enabled);
        log('debug mode changed', debugEnabled);
        return;
    }

    if (data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

clientsClaim();
precacheAndRoute(self.__WB_MANIFEST);
cleanupOutdatedCaches();

registerRoute(
    ({ request, url }) => request.mode === 'navigate' && !isAuthNavigation(url),
    new NetworkFirst({
        cacheName: 'prs-pages-v1',
        networkTimeoutSeconds: 6,
        plugins: [
            debugPlugin,
            new CacheableResponsePlugin({
                statuses: [0, 200],
            }),
            new ExpirationPlugin({
                maxEntries: 60,
                maxAgeSeconds: 60 * 60 * 24 * 7,
            }),
        ],
    }),
);

registerRoute(
    ({ request, url }) => {
        if (request.method !== 'GET') return false;
        if (url.origin !== self.location.origin) return false;
        if (request.mode === 'navigate') return false;
        if (['script', 'style', 'image', 'font'].includes(request.destination)) return false;
        return !url.pathname.startsWith('/logout');
    },
    new NetworkFirst({
        cacheName: 'prs-data-v1',
        networkTimeoutSeconds: 6,
        plugins: [
            debugPlugin,
            new CacheableResponsePlugin({
                statuses: [0, 200],
            }),
            new ExpirationPlugin({
                maxEntries: 120,
                maxAgeSeconds: 60 * 60 * 24 * 3,
            }),
        ],
    }),
);

registerRoute(
    ({ request }) => ['script', 'style', 'image', 'font'].includes(request.destination),
    new StaleWhileRevalidate({
        cacheName: 'prs-static-v1',
        plugins: [
            debugPlugin,
            new ExpirationPlugin({
                maxEntries: 180,
                maxAgeSeconds: 60 * 60 * 24 * 14,
            }),
        ],
    }),
);

setCatchHandler(async ({ request }) => {
    if (request.destination === 'document') {
        const fallback = await matchPrecache('/offline.html');
        if (fallback) {
            warn('offline fallback served', request.url);
            return fallback;
        }
    }

    warn('request failed with no fallback', request.url);
    return Response.error();
});
