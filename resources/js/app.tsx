import { createInertiaApp } from '@inertiajs/react';
if (typeof window !== 'undefined') {
    void import('bootstrap');
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    progress: {
        color: '#4B5563',
    },
});
