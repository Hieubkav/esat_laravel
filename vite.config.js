import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/auth-modal.css', 'resources/js/app.js', 'resources/js/csrf-helper.js', 'resources/js/auth-state-manager.js', 'resources/js/ajax-handlers.js', 'resources/js/fallback-detection.js', 'resources/js/performance-optimizer.js'],
            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
                'resources/views/**/*.blade.php',
                'app/Http/Controllers/**',
            ],
        }),
    ],
});
