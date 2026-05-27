import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    const host = env.VITE_DEV_SERVER_HOST || '0.0.0.0';
    const port = env.VITE_DEV_SERVER_PORT || 5173;

    return {
        plugins: [
            tailwindcss(),
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
        ],
        server: {
            host: '0.0.0.0',
            https: false,
            origin: `http://${host}:${port}`,
            hmr: {
                host: host,
            },
        },
    };
});
