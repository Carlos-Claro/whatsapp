import '../css/app.css';
import 'primeicons/primeicons.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import { definePreset } from '@primeuix/themes';
import { ConfirmationService, ToastService, Tooltip } from 'primevue';

const MyPreset = definePreset(Aura, {
    semantic: {
        primary: {
            50:  '{pink.50}',
            100: '{pink.100}',
            200: '{pink.200}',
            300: '{pink.300}',
            400: '{pink.400}',
            500: '{pink.500}',
            600: '{pink.600}',
            700: '{pink.700}',
            800: '{pink.800}',
            900: '{pink.900}',
            950: '{pink.950}'
        },
        surface: {
            50:  '{viva.50}',
            100: '{viva.100}',
            200: '{viva.200}',
            300: '{viva.300}',
            400: '{viva.400}',
            500: '{viva.500}',
            600: '{viva.600}',
            700: '{viva.700}',
            800: '{viva.800}',
            900: '{viva.900}',
            950: '{viva.950}'
        },
        colorScheme:{
            light:{
                primary: {
                    color: '{primary.500}',
                    contrastColor: '#ffffff',
                    hoverColor: '{primary.600}',
                    activeColor: '{primary.700}'
                },
                root: {
                    background: '{surface.0}',
                    color: '{surface.700}'
                },
            },
            dark:{
                primary: {
                    color: '{primary.400}',
                    contrastColor: '{surface.900}',
                    hoverColor: '{primary.300}',
                    activeColor: '{primary.200}'
                },
                root: {
                    background: '{surface.900}',
                    color: '{surface.0}'
                },
            },
        }
    }
});

const appName = import.meta.env.VITE_APP_NAME || 'Whatsapp Pow';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                ripple:true,
                theme: {
                    preset: MyPreset,
                    options: {
                        darkModeSelector: '.dark',
                    },
                },
            })
            .use(ToastService)
            .directive('tooltip', Tooltip)
            .use(ConfirmationService)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

