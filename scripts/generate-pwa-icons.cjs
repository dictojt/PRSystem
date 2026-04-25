/**
 * Generates minimal placeholder PWA icons.
 * Replace pwa-192x192.png and pwa-512x512.png with proper icons for production.
 * Use PWA Asset Generator: https://vite-pwa-org.netlify.app/assets-generator/
 */
const fs = require('fs');
const path = require('path');

// Minimal valid 1x1 transparent PNG - browsers will scale as needed
const PNG_1x1 = Buffer.from(
  'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
  'base64'
);

const publicDir = path.join(__dirname, '..', 'public');
const icon192 = path.join(publicDir, 'pwa-logo-192x192.png');
const icon512 = path.join(publicDir, 'pwa-logo-512x512.png');

if (!fs.existsSync(publicDir)) {
  fs.mkdirSync(publicDir, { recursive: true });
}

fs.writeFileSync(icon192, PNG_1x1);
fs.writeFileSync(icon512, PNG_1x1);

console.log('Created placeholder PWA icons: pwa-192x192.png, pwa-512x512.png');
console.log('Replace with proper 192x192 and 512x512 icons for production.');
