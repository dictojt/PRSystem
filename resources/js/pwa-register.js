const DEBUG_KEY = 'prs:pwa-debug';
const INSTALL_LAST_PROMPT_KEY = 'prs:pwa-install-last-prompt';
const INSTALL_SUPPRESS_UNTIL_KEY = 'prs:pwa-install-suppress-until';
const INSTALL_AUTO_SESSION_KEY = 'prs:pwa-install-auto-attempted';
const LOG_PREFIX = '[PWA]';
const INSTALL_PROMPT_COOLDOWN_MS = 1000 * 60 * 60 * 24 * 3;
const INSTALL_SNOOZE_MS = 1000 * 60 * 60 * 24 * 7;
const AUTO_PROMPT_DELAY_MS = 1500;

const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
let installPromptEvent = null;
let isDebugEnabled = isLocalhost;
let isInstallPromptInProgress = false;
let autoPromptTimerId = null;
let installFallbackElement = null;
let installFallbackVisible = false;
let lastInstallPromptOutcome = null;

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

function getLocalStorageValue(key) {
    try {
        return window.localStorage.getItem(key);
    } catch (_) {
        return null;
    }
}

function setLocalStorageValue(key, value) {
    try {
        window.localStorage.setItem(key, value);
    } catch (_) {
        // Ignore localStorage access issues.
    }
}

function removeLocalStorageValue(key) {
    try {
        window.localStorage.removeItem(key);
    } catch (_) {
        // Ignore localStorage access issues.
    }
}

function getSessionStorageValue(key) {
    try {
        return window.sessionStorage.getItem(key);
    } catch (_) {
        return null;
    }
}

function setSessionStorageValue(key, value) {
    try {
        window.sessionStorage.setItem(key, value);
    } catch (_) {
        // Ignore sessionStorage access issues.
    }
}

function parseTimestamp(value) {
    const parsed = Number(value);
    return Number.isFinite(parsed) ? parsed : 0;
}

function getLastInstallPromptAt() {
    return parseTimestamp(getLocalStorageValue(INSTALL_LAST_PROMPT_KEY));
}

function setLastInstallPromptAt(value) {
    setLocalStorageValue(INSTALL_LAST_PROMPT_KEY, String(value));
}

function getInstallSuppressUntil() {
    return parseTimestamp(getLocalStorageValue(INSTALL_SUPPRESS_UNTIL_KEY));
}

function setInstallSuppressUntil(value) {
    setLocalStorageValue(INSTALL_SUPPRESS_UNTIL_KEY, String(value));
}

function clearInstallSuppressUntil() {
    removeLocalStorageValue(INSTALL_SUPPRESS_UNTIL_KEY);
}

function hasAutoPromptAttemptedThisSession() {
    return getSessionStorageValue(INSTALL_AUTO_SESSION_KEY) === '1';
}

function markAutoPromptAttemptedThisSession() {
    setSessionStorageValue(INSTALL_AUTO_SESSION_KEY, '1');
}

function isInstallSuppressed() {
    return getInstallSuppressUntil() > Date.now();
}

function isInstallCooldownActive() {
    const lastPromptAt = getLastInstallPromptAt();
    if (!lastPromptAt) return false;
    return Date.now() - lastPromptAt < INSTALL_PROMPT_COOLDOWN_MS;
}

function ensureInstallFallbackElement() {
    if (installFallbackElement || !document.body) return;

    const container = document.createElement('div');
    container.id = 'prs-pwa-install-fallback';
    container.setAttribute('role', 'status');
    Object.assign(container.style, {
        position: 'fixed',
        right: '16px',
        bottom: '16px',
        zIndex: '9999',
        maxWidth: '360px',
        width: 'calc(100% - 32px)',
        boxSizing: 'border-box',
        background: '#0f172a',
        color: '#f8fafc',
        borderRadius: '12px',
        padding: '12px 14px',
        boxShadow: '0 10px 24px rgba(15, 23, 42, 0.28)',
        fontFamily: 'Inter, Arial, sans-serif',
        fontSize: '14px',
        lineHeight: '1.4',
    });

    const message = document.createElement('div');
    message.textContent = 'Install PRS for faster access and better offline support.';
    message.style.marginBottom = '10px';

    const actions = document.createElement('div');
    Object.assign(actions.style, {
        display: 'flex',
        gap: '8px',
        justifyContent: 'flex-end',
    });

    const installButton = document.createElement('button');
    installButton.type = 'button';
    installButton.dataset.action = 'install';
    installButton.textContent = 'Install';
    Object.assign(installButton.style, {
        border: '0',
        borderRadius: '8px',
        padding: '8px 12px',
        background: '#2563eb',
        color: '#ffffff',
        cursor: 'pointer',
        fontWeight: '600',
    });

    const notNowButton = document.createElement('button');
    notNowButton.type = 'button';
    notNowButton.dataset.action = 'not-now';
    notNowButton.textContent = 'Not now';
    Object.assign(notNowButton.style, {
        border: '1px solid rgba(148, 163, 184, 0.45)',
        borderRadius: '8px',
        padding: '8px 12px',
        background: 'transparent',
        color: '#e2e8f0',
        cursor: 'pointer',
    });

    installButton.addEventListener('click', async () => {
        await requestInstallPrompt('manual-fallback-button');
    });

    notNowButton.addEventListener('click', () => {
        setInstallSuppressUntil(Date.now() + INSTALL_SNOOZE_MS);
        hideInstallFallback();
        log('install prompt snoozed', 'Fallback prompt hidden for now.');
    });

    actions.append(installButton, notNowButton);
    container.append(message, actions);
    container.hidden = true;
    document.body.appendChild(container);

    installFallbackElement = container;
}

function updateInstallFallbackState() {
    if (!installFallbackElement) return;
    const installButton = installFallbackElement.querySelector('[data-action="install"]');
    if (!installButton) return;

    installButton.disabled = !installPromptEvent || isInstallPromptInProgress;
    installButton.style.opacity = installButton.disabled ? '0.65' : '1';
    installButton.style.cursor = installButton.disabled ? 'not-allowed' : 'pointer';
}

function showInstallFallback(reason) {
    if (isInstallSuppressed()) {
        log('install fallback skipped', 'Suppressed by cooldown.');
        return;
    }

    if (!document.body) {
        window.addEventListener(
            'DOMContentLoaded',
            () => {
                showInstallFallback(reason);
            },
            { once: true },
        );
        return;
    }

    ensureInstallFallbackElement();
    if (!installFallbackElement) return;
    installFallbackElement.hidden = false;
    installFallbackVisible = true;
    updateInstallFallbackState();
    if (reason) log('install fallback shown', reason);
}

function hideInstallFallback() {
    if (!installFallbackElement) return;
    installFallbackElement.hidden = true;
    installFallbackVisible = false;
}

function canAutoPromptInstall() {
    if (!installPromptEvent) return false;
    if (isInstallPromptInProgress) return false;
    if (hasAutoPromptAttemptedThisSession()) return false;
    if (isInstallSuppressed()) return false;
    if (isInstallCooldownActive()) return false;
    if (document.visibilityState && document.visibilityState !== 'visible') return false;
    return true;
}

function scheduleAutoInstallPrompt() {
    if (autoPromptTimerId) {
        window.clearTimeout(autoPromptTimerId);
        autoPromptTimerId = null;
    }

    if (document.visibilityState && document.visibilityState !== 'visible') {
        const onVisible = () => {
            if (document.visibilityState === 'visible') {
                document.removeEventListener('visibilitychange', onVisible);
                scheduleAutoInstallPrompt();
            }
        };
        document.addEventListener('visibilitychange', onVisible);
        return;
    }

    if (!canAutoPromptInstall()) {
        if (installPromptEvent && !isInstallSuppressed()) {
            showInstallFallback('Auto install prompt skipped by guard.');
        }
        return;
    }

    autoPromptTimerId = window.setTimeout(() => {
        autoPromptTimerId = null;
        requestInstallPrompt('auto-beforeinstallprompt');
    }, AUTO_PROMPT_DELAY_MS);
}

async function requestInstallPrompt(source = 'manual') {
    if (!installPromptEvent) {
        warn('install unavailable', 'No deferred install prompt captured yet.');
        showInstallFallback('No deferred prompt available.');
        return false;
    }

    if (isInstallPromptInProgress) {
        return false;
    }

    const deferredPrompt = installPromptEvent;
    isInstallPromptInProgress = true;
    updateInstallFallbackState();

    if (source.startsWith('auto')) {
        markAutoPromptAttemptedThisSession();
    }

    setLastInstallPromptAt(Date.now());

    try {
        await deferredPrompt.prompt();
        const result = await deferredPrompt.userChoice;
        const outcome = result && result.outcome ? result.outcome : 'unknown';
        lastInstallPromptOutcome = outcome;
        log('install prompt result', outcome);

        installPromptEvent = null;
        if (outcome === 'accepted') {
            clearInstallSuppressUntil();
            hideInstallFallback();
        } else {
            setInstallSuppressUntil(Date.now() + INSTALL_SNOOZE_MS);
            showInstallFallback('Install prompt dismissed.');
        }

        return result;
    } catch (error) {
        const message = error instanceof Error ? error.message : String(error);
        warn('install prompt failed', message);
        installPromptEvent = deferredPrompt;
        showInstallFallback('Install prompt failed; using manual fallback.');
        return false;
    } finally {
        isInstallPromptInProgress = false;
        updateInstallFallbackState();
    }
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
        installPromptInProgress: isInstallPromptInProgress,
        installAutoAttemptedThisSession: hasAutoPromptAttemptedThisSession(),
        installCooldownActive: isInstallCooldownActive(),
        installSuppressed: isInstallSuppressed(),
        installSuppressUntil: getInstallSuppressUntil() || null,
        installLastPromptAt: getLastInstallPromptAt() || null,
        installFallbackVisible,
        installLastPromptOutcome: lastInstallPromptOutcome,
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
    return requestInstallPrompt('manual-console');
};

function bindInstallEvents() {
    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        installPromptEvent = event;
        lastInstallPromptOutcome = 'available';
        updateInstallFallbackState();
        log('install prompt available', 'Auto prompt will be attempted with fallback UI.');
        scheduleAutoInstallPrompt();
    });

    window.addEventListener('appinstalled', () => {
        if (autoPromptTimerId) {
            window.clearTimeout(autoPromptTimerId);
            autoPromptTimerId = null;
        }
        installPromptEvent = null;
        lastInstallPromptOutcome = 'accepted';
        markAutoPromptAttemptedThisSession();
        clearInstallSuppressUntil();
        hideInstallFallback();
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
