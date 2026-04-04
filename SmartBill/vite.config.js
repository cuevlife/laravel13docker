import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: true, // ฟังทุก interfaces (0.0.0.0)
        port: 5173,
        strictPort: true,
        cors: true,
        hmr: {
            host: '192.168.9.113', // บังคับให้ WebSocket วิ่งไปที่ IP นี้
        },
    },
});
