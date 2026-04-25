import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/user-panel.css',
                'resources/css/super-admin.css',
                'resources/css/approver.css',
                'resources/css/home.css',
                'resources/css/prs.css',
                'resources/js/app.js',
                'resources/js/user-panel.js',
                'resources/js/super-admin.js',
                'resources/js/approver.js',
                'resources/css/view-requests.css',
            ],
            refresh: true,
        }),
        tailwindcss(),
        VitePWA({
            outDir: 'public',
            manifestFilename: 'manifest.webmanifest',
            filename: 'sw.js',
            injectRegister: false,
            registerType: 'autoUpdate',
            strategies: 'injectManifest',
            srcDir: 'resources/js',
            injectManifest: {
                swSrc: 'resources/js/sw.js',
            },
            includeAssets: [
                'offline.html',
                'pwa-192x192.png',
                'pwa-logo-192x192.png',
                'pwa-logo-512x512.png',
            ],
            manifest: {
                id: '/',
                name: 'Product Request System',
                short_name: 'PRS',
                description: 'Product Request System - DICT',
                start_url: '/',
                scope: '/',
                display: 'standalone',
                background_color: '#ffffff',
                theme_color: '#2563eb',
                icons: [
                    {
                        src: '/pwa-logo-192x192.png',
                        sizes: '192x192',
                        type: 'image/png',
                    },
                    {
                        src: '/pwa-logo-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any maskable',
                    },
                ],
            },
            devOptions: {
                enabled: false,
            },
        }),
    ],
    server: {
        host: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
