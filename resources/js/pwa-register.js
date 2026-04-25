const DEBUG_KEY = 'prs:pwa-debug';
const LOG_PREFIX = '[PWA]';

const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
let installPromptEvent = null;
let isDebugEnabled = isLocalhost;

try {
    isDebugEnabled = isDebugEnabled || window.localStorage.getItem(DEBUG_KEY) === '1';
} catch (_) {
    // Ignore localStorage access issues.
}

function log(event, details) {
    if (!isDebugEnabled) return;
    if (typeof details === 'undefined') {
        console.log(`${LOG_PREFIX} ${event}`);
        return;
    }
    console.log(`${LOG_PREFIX} ${event}:`, details);
}

function warn(event, details) {
    if (!isDebugEnabled) return;
    if (typeof details === 'undefined') {
        console.warn(`${LOG_PREFIX} ${event}`);
        return;
    }
    console.warn(`${LOG_PREFIX} ${event}:`, details);
}

function postDebugFlag(enabled) {
    if (!navigator.serviceWorker) return;

    navigator.serviceWorker.ready
        .then((registration) => {
            [registration.installing, registration.waiting, registration.active]
                .filter(Boolean)
                .forEach((worker) => {
                    worker.postMessage({
                        type: 'PWA_DEBUG',
                        enabled,
                    });
                });
        })
        .catch(() => {
            // Service worker might not be ready yet.
        });
}

async function getDebugReport() {
    const report = {
        url: window.location.href,
        isSecureContext: window.isSecureContext,
        online: navigator.onLine,
        serviceWorkerSupported: 'serviceWorker' in navigator,
        hasController: Boolean(navigator.serviceWorker && navigator.serviceWorker.controller),
        debugEnabled: isDebugEnabled,
        installPromptCaptured: Boolean(installPromptEvent),
        cacheNames: [],
        registration: null,
    };

    if ('caches' in window) {
        try {
            report.cacheNames = await caches.keys();
        } catch (error) {
            report.cacheError = String(error);
        }
    }

    if (navigator.serviceWorker) {
        try {
            const registration = await navigator.serviceWorker.getRegistration();
            if (registration) {
                report.registration = {
                    scope: registration.scope,
                    active: registration.active ? registration.active.state : null,
                    waiting: registration.waiting ? registration.waiting.state : null,
                    installing: registration.installing ? registration.installing.state : null,
                };
            }
        } catch (error) {
            report.registrationError = String(error);
        }
    }

    return report;
}

window.__PWA_DEBUG__ = async function __PWA_DEBUG__() {
    const report = await getDebugReport();
    console.log(`${LOG_PREFIX} debug report:`, report);
    return report;
};

window.__PWA_DEBUG_ENABLE__ = function __PWA_DEBUG_ENABLE__() {
    isDebugEnabled = true;
    try {
        window.localStorage.setItem(DEBUG_KEY, '1');
    } catch (_) {
        // Ignore localStorage access issues.
    }
    postDebugFlag(true);
    console.log(`${LOG_PREFIX} debug enabled`);
};

window.__PWA_DEBUG_DISABLE__ = function __PWA_DEBUG_DISABLE__() {
    isDebugEnabled = false;
    try {
        window.localStorage.removeItem(DEBUG_KEY);
    } catch (_) {
        // Ignore localStorage access issues.
    }
    postDebugFlag(false);
    console.log(`${LOG_PREFIX} debug disabled`);
};

window.__PWA_INSTALL__ = async function __PWA_INSTALL__() {
    if (!installPromptEvent) {
        warn('install unavailable', 'No deferred install prompt captured yet.');
        return false;
    }

    installPromptEvent.prompt();
    const result = await installPromptEvent.userChoice;
    log('install prompt result', result && result.outcome ? result.outcome : 'unknown');
    installPromptEvent = null;
    return result;
};

function bindInstallEvents() {
    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        installPromptEvent = event;
        log('install prompt available', 'Call __PWA_INSTALL__() to open prompt.');
    });

    window.addEventListener('appinstalled', () => {
        installPromptEvent = null;
        log('app installed');
    });
}

async function registerServiceWorker() {
    if (!('serviceWorker' in navigator)) {
        warn('service worker unsupported', 'This browser does not support service workers.');
        return;
    }

    try {
        const registration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
        log('service worker registered', registration.scope);

        postDebugFlag(isDebugEnabled);

        if (registration.waiting) {
            log('update waiting', 'New service worker waiting to activate.');
        }

        registration.addEventListener('updatefound', () => {
            const newWorker = registration.installing;
            log('update found');
            if (!newWorker) return;

            newWorker.addEventListener('statechange', () => {
                log('worker state change', newWorker.state);
                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                    log('update ready', 'Reload page to use latest version.');
                }
            });
        });

        navigator.serviceWorker.addEventListener('controllerchange', () => {
            log('controller changed', 'This page is now controlled by the latest service worker.');
        });
    } catch (error) {
        console.error(`${LOG_PREFIX} service worker registration failed:`, error);
    }
}

window.addEventListener('online', () => log('network status', 'online'));
window.addEventListener('offline', () => warn('network status', 'offline'));

bindInstallEvents();
registerServiceWorker();
