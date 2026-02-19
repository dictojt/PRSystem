# PWA Setup Guide (Clean Rebuild)

This project uses a fresh PWA implementation with:

- `vite-plugin-pwa` for manifest + service worker build integration
- a custom service worker source (`resources/js/sw.js`) for clear debug logs
- a shared browser registration module (`resources/js/pwa-register.js`)

## What This Setup Delivers

- Installable app (Chrome/Edge supported browsers)
- Offline fallback page (`public/offline.html`)
- Runtime caching for:
  - full page navigation requests
  - same-origin GET data requests
  - static assets (js/css/images/fonts)

## Core Files

- `vite.config.js` - PWA plugin config + manifest metadata
- `resources/js/sw.js` - service worker logic and runtime cache strategy
- `resources/js/pwa-register.js` - registration, install event handling, debug helpers
- `public/offline.html` - fallback page when navigation fails offline

## Build Outputs

After running `npm run build`, expected PWA artifacts:

- `public/sw.js`
- `public/manifest.webmanifest`
- `public/workbox-*.js`

## Browser Integration

PWA registration is loaded from:

- `resources/js/app.js`
- `resources/js/user-panel.js`
- `resources/js/super-admin.js`
- `resources/js/approver.js`

The following Blade templates include manifest/theme tags and a JS entry that can register SW:

- `resources/views/home.blade.php`
- `resources/views/auth/forgot-password.blade.php`
- `resources/views/layouts/prs.blade.php`
- `resources/views/layouts/user-panel.blade.php`
- `resources/views/layouts/superadmin.blade.php`
- `resources/views/dashboards/approver.blade.php`

## Debug Logs and Helpers

All debug logs use consistent prefixes:

- Client logs: `[PWA] ...`
- Service Worker logs: `[PWA][SW] ...`

Console helper commands:

- `__PWA_DEBUG__()` - show current PWA status report
- `__PWA_DEBUG_ENABLE__()` - enable persistent debug logging
- `__PWA_DEBUG_DISABLE__()` - disable debug logging
- `__PWA_INSTALL__()` - open deferred install prompt (if available)

## Quick Test Flow

1. Open the app while online.
2. Check console for `[PWA] service worker registered`.
3. Reload once so the page is controlled by SW.
4. Visit main sections at least once while online.
5. Switch DevTools Network to Offline and reload a visited page.
6. If a page is not cached yet, `offline.html` is served for navigation fallback.

## Common Troubleshooting

- No install option:
  - confirm `manifest.webmanifest` is reachable
  - confirm no manifest/icon 404 errors
- Offline not working:
  - run `__PWA_DEBUG__()` and verify `hasController: true`
  - ensure you reloaded after first SW registration
  - ensure the route was visited online at least once
