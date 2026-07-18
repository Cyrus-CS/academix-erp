/**
 * ╔══════════════════════════════════════════════════════════════╗
 * ║              SORTABLE CORE — School ERP                      ║
 * ║  Moteur centralisé SortableJS — réutilisable sur toutes vues ║
 * ╚══════════════════════════════════════════════════════════════╝
 *
 * USAGE RAPIDE :
 * ─────────────
 * import { SchoolSortable } from './sortable/sortable-core';
 *
 * SchoolSortable.init({
 *     gridId    : 'subjects-grid',   // ID du conteneur
 *     saveUrl   : '/subjects/reorder', // Route POST Laravel
 *     itemSelector: '[data-id]',      // Sélecteur des items
 * });
 */

import Sortable from 'sortablejs';

// ── Constantes ────────────────────────────────────────────────────
const CSRF = () =>
    document.querySelector('meta[name="csrf-token"]')?.content ?? '';

const INDICATOR_ID = 'sortable-save-indicator';

// ─────────────────────────────────────────────────────────────────
// SchoolSortable — API publique
// ─────────────────────────────────────────────────────────────────
export const SchoolSortable = {

    /**
     * Initialise un Sortable sur un conteneur.
     *
     * @param {Object} config
     * @param {string}   config.gridId          ID du conteneur HTML
     * @param {string}   config.saveUrl         URL POST pour persister l'ordre
     * @param {string}   [config.itemSelector]  Sélecteur CSS des items (défaut: '[data-id]')
     * @param {string}   [config.handle]        Sélecteur de la poignée (optionnel)
     * @param {boolean}  [config.disabled]      Désactiver le drag (ex: si filtres actifs)
     * @param {Function} [config.onSaveSuccess] Callback après sauvegarde OK
     * @param {Function} [config.onSaveError]   Callback après erreur
     * @param {Object}   [config.sortableOptions] Options SortableJS supplémentaires
     *
     * @returns {Sortable|null}
     */
    init(config = {}) {
        const {
            gridId,
            saveUrl,
            itemSelector     = '[data-id]',
            handle           = null,
            disabled         = false,
            onSaveSuccess    = null,
            onSaveError      = null,
            sortableOptions  = {},
        } = config;

        // ── Validation ──────────────────────────────────────────
        if (!gridId || !saveUrl) {
            console.error('[SchoolSortable] gridId et saveUrl sont requis.');
            return null;
        }

        const grid = document.getElementById(gridId);

        if (!grid) {
            // Pas une erreur : la vue peut ne pas avoir cette grille
            return null;
        }

        if (disabled) {
            console.info(`[SchoolSortable] #${gridId} — drag désactivé.`);
            return null;
        }

        // ── Styles de base sur le conteneur ─────────────────────
        grid.style.userSelect       = 'none';
        grid.style.webkitUserSelect = 'none';

        // ── Styles sur les items ─────────────────────────────────
        grid.querySelectorAll(itemSelector).forEach(item => {
            item.style.userSelect       = 'none';
            item.style.webkitUserSelect = 'none';
            item.style.touchAction      = 'none';
            if (!handle) item.style.cursor = 'grab';
        });

        // ── État interne ─────────────────────────────────────────
        let isSaving   = false;
        let saveTimer  = null;

        // ── Créer l'instance SortableJS ──────────────────────────
        const instance = Sortable.create(grid, {

            // Options de base
            animation       : 200,
            easing          : 'cubic-bezier(0.25, 0.1, 0.25, 1)',
            ghostClass      : 'sortable-ghost',
            chosenClass     : 'sortable-chosen',
            dragClass       : 'sortable-drag',
            forceFallback   : false,
            preventOnFilter : true,

            // Poignée personnalisée (optionnel)
            ...(handle ? { handle } : {}),

            // Filtrer les éléments interactifs
            filter: 'input, textarea, select, button, a, [data-no-drag]',
            onFilter(evt) {
                evt.item.style.cursor = 'default';
            },

            // Fusionner les options custom
            ...sortableOptions,

            // ── Callbacks ─────────────────────────────────────
            onStart(evt) {
                // Désactiver la sélection texte globalement
                document.body.style.userSelect       = 'none';
                document.body.style.webkitUserSelect = 'none';
                document.body.classList.add('is-dragging');

                evt.item.style.cursor = 'grabbing';

                // Callback custom
                sortableOptions.onStart?.call(this, evt);
            },

            onEnd(evt) {
                // Réactiver la sélection texte
                document.body.style.userSelect       = '';
                document.body.style.webkitUserSelect = '';
                document.body.classList.remove('is-dragging');

                evt.item.style.cursor = handle ? '' : 'grab';

                // Callback custom
                sortableOptions.onEnd?.call(this, evt);

                // Pas de changement → pas de sauvegarde
                if (evt.oldIndex === evt.newIndex) return;

                // Debounce : attendre la fin des micro-réordonnements
                clearTimeout(saveTimer);
                saveTimer = setTimeout(() => {
                    _persistOrder({
                        grid,
                        itemSelector,
                        saveUrl,
                        isSaving,
                        setSaving : (v) => { isSaving = v; },
                        onSaveSuccess,
                        onSaveError,
                    });
                }, 300);
            },
        });

        console.info(`[SchoolSortable] #${gridId} initialisé → ${saveUrl}`);
        return instance;
    },

    /**
     * Initialise plusieurs grilles d'un coup.
     *
     * @param {Array<Object>} configs  Tableau de configs (voir init())
     * @returns {Array<Sortable|null>}
     */
    initMany(configs = []) {
        return configs.map(cfg => this.init(cfg));
    },

    /**
     * Détruit une instance Sortable proprement.
     *
     * @param {Sortable} instance
     */
    destroy(instance) {
        if (instance && typeof instance.destroy === 'function') {
            instance.destroy();
        }
    },
};

// ─────────────────────────────────────────────────────────────────
// _persistOrder — Sauvegarde l'ordre via fetch
// ─────────────────────────────────────────────────────────────────
function _persistOrder({ grid, itemSelector, saveUrl, isSaving, setSaving, onSaveSuccess, onSaveError }) {

    if (isSaving) return;
    setSaving(true);

    // Récupérer l'ordre actuel des IDs
    const order = [...grid.querySelectorAll(itemSelector)]
        .map(item => item.dataset.id)
        .filter(Boolean);

    if (!order.length) {
        setSaving(false);
        return;
    }

    // Afficher l'indicateur global
    _showIndicator(true);

    fetch(saveUrl, {
        method  : 'POST',
        headers : {
            'Content-Type' : 'application/json',
            'Accept'       : 'application/json',
            'X-CSRF-TOKEN' : CSRF(),
        },
        body: JSON.stringify({ order }),
    })
    .then(res => {
        if (!res.ok) throw new Error(`HTTP ${res.status} — ${saveUrl}`);
        return res.json();
    })
    .then(data => {
        _showIndicator(false, true);

        window.showToast?.({
            type    : 'success',
            title   : 'Ordre sauvegardé',
            message : data.message ?? 'La disposition a été mise à jour.',
            delay   : 2500,
        });

        onSaveSuccess?.(data, order);
    })
    .catch(err => {
        console.error('[SchoolSortable] Erreur sauvegarde :', err);
        _showIndicator(false, false);

        window.showToast?.({
            type    : 'error',
            title   : 'Erreur de sauvegarde',
            message : 'Impossible de sauvegarder l\'ordre. Réessayez.',
            delay   : 5000,
        });

        onSaveError?.(err, order);
    })
    .finally(() => {
        setSaving(false);
    });
}

// ─────────────────────────────────────────────────────────────────
// _showIndicator — Indicateur visuel global
// ─────────────────────────────────────────────────────────────────
function _showIndicator(saving, success = null) {
    let indicator = document.getElementById(INDICATOR_ID);

    // Créer l'indicateur s'il n'existe pas
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = INDICATOR_ID;
        indicator.className = [
            'fixed bottom-5 right-5 z-50',
            'flex items-center gap-2.5',
            'px-4 py-2.5 rounded-xl',
            'text-xs font-semibold text-white',
            'shadow-lg backdrop-blur-sm',
            'transition-all duration-300',
            'translate-y-20 opacity-0',
        ].join(' ');
        document.body.appendChild(indicator);
    }

    if (saving) {
        indicator.className = indicator.className
            .replace('translate-y-20 opacity-0', 'translate-y-0 opacity-100');
        indicator.style.background = 'rgba(37, 99, 235, 0.95)'; // blue-600
        indicator.innerHTML = `
            <svg class="w-3.5 h-3.5 animate-spin" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Sauvegarde…
        `;
        return;
    }

    // Succès / Erreur → afficher brièvement puis masquer
    if (success === true) {
        indicator.style.background = 'rgba(16, 185, 129, 0.95)'; // emerald-500
        indicator.innerHTML = '<i class="bi bi-check-circle-fill"></i> Sauvegardé';
    } else if (success === false) {
        indicator.style.background = 'rgba(239, 68, 68, 0.95)'; // red-500
        indicator.innerHTML = '<i class="bi bi-x-circle-fill"></i> Erreur';
    }

    setTimeout(() => {
        indicator.className = indicator.className
            .replace('translate-y-0 opacity-100', 'translate-y-20 opacity-0');
    }, 2000);
}