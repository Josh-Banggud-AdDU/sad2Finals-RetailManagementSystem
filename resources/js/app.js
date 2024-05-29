import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';
import { Ziggy } from './ziggy.js';

// Import Font Awesome packages
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faBoxOpen, faCartShopping, faMoneyBills, faMotorcycle, faUsers, faUser, faMagnifyingGlass, faCaretDown, faTrashCan, faPlus, faMinus, faPrint} from '@fortawesome/free-solid-svg-icons';

// Add the imported icons to the Font Awesome library
library.add(faBoxOpen, faCartShopping, faMoneyBills, faMotorcycle, faUsers, faUser, faMagnifyingGlass, faCaretDown, faTrashCan, faPlus, faMinus, faPrint);

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .component('font-awesome-icon', FontAwesomeIcon) // Register FontAwesomeIcon as a component
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
