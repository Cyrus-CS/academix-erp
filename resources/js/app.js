import './bootstrap';
import { initTheme }   from './theme';
import { initSidebar }  from './sidebar';
import { initToasts }  from './Toasts';
import { initDropdowns, initMarkAllRead } from './dropdown';
import { initSearch } from './search';
import { initNotifications }  from './notifications';
import { SchoolSortable } from './sortable/sortable-core';
import Sortable from 'sortablejs';
import flatpickr from 'flatpickr';

// ── Rendre disponibles globalement ──────────────────────────────
// → utilisable directement dans les <script> inline des vues Blade
window.Sortable       = Sortable;
window.SchoolSortable = SchoolSortable;

// ── Initialisation globale ───────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initSidebar();
    initDropdowns();
    initSearch();
    initToasts();
    initNotifications();

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    initMarkAllRead('/notifications/mark-all-read', csrfMeta?.content ?? '');

    // ── Auto-init : toutes les grilles Sortable de la page ──────
    // Cherche tous les éléments ayant data-sortable-url
    // → permet d'initialiser sans écrire de JS dans chaque vue
    document.querySelectorAll('[data-sortable-url]').forEach(grid => {
        SchoolSortable.init({
            gridId  : grid.id,
            saveUrl : grid.dataset.sortableUrl,
            handle  : grid.dataset.sortableHandle ?? null,
        });
    });
});