import './bootstrap';

import { initTheme }     from './theme';
import { initSidebar }   from './sidebar';
import { Chart } from 'chart.js';
import { initNotifications } from './notifications';
import flatpickr from "flatpickr";
import { initToasts } from './Toasts';
import { initDropdowns, initMarkAllRead } from './dropdown';
import { initSearch }    from './search';

// ── Config Sortable par défaut ────────────────────────────────────
const SORTABLE_DEFAULTS = {
    animation        : 200,
    ghostClass       : 'sortable-ghost',
    dragClass        : 'sortable-drag',
    forceFallback    : false,
    preventOnFilter  : true,
    filter           : 'input, textarea, select, button, a',
    // Désactiver la sélection texte pendant le drag
    onStart() {
        document.body.style.userSelect       = 'none';
        document.body.style.webkitUserSelect = 'none';
    },
    onEnd() {
        document.body.style.userSelect       = '';
        document.body.style.webkitUserSelect = '';
    },
};

// Rendre Sortable accessible globalement avec les defaults
window.Sortable = Sortable;
window.createSortable = (el, options = {}) => {
    // Fusionner les callbacks onStart/onEnd
    const merged = {
        ...SORTABLE_DEFAULTS,
        ...options,
        onStart(evt) {
            SORTABLE_DEFAULTS.onStart.call(this, evt);
            options.onStart?.call(this, evt);
        },
        onEnd(evt) {
            SORTABLE_DEFAULTS.onEnd.call(this, evt);
            options.onEnd?.call(this, evt);
        },
    };
    return Sortable.create(el, merged);
};

// ── Initialisation au chargement du DOM ──────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initSidebar();
    initDropdowns();
    initSearch();
    initToasts();
    initNotifications();

    // Notifications : marquer comme lues
    const meta = document.querySelector('meta[name="csrf-token"]');
    initMarkAllRead('/notifications/mark-all-read', meta?.content ?? '');
});

