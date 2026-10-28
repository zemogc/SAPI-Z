import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Las gráficas solo se descargan en las páginas que las usan.
if (document.getElementById('dashboard-data')) {
    import('./dashboard');
}
