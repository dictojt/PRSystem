/**
 * Remove existing service worker build outputs so vite-plugin-pwa can write them.
 * Fixes EPERM on Windows when sw.js is locked (e.g. by browser).
 */
import { unlink } from 'fs/promises';
import { join } from 'path';

const files = ['public/sw.js', 'public/sw.mjs'];
for (const f of files) {
    try {
        await unlink(join(process.cwd(), f));
    } catch {
        /* ignore if missing or locked */
    }
}
