import './bootstrap';

import Alpine from 'alpinejs';
import { asistentePropiedad, chatAsistente } from './asistente';

window.Alpine = Alpine;

Alpine.data('asistentePropiedad', asistentePropiedad);
Alpine.data('chatAsistente', chatAsistente);

Alpine.start();

// Las gráficas solo se descargan en las páginas que las usan.
if (document.getElementById('dashboard-data')) {
    import('./dashboard');
}
