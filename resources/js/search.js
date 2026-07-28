/**
 * resources/js/search.js
 * Recherche globale — Modal Ctrl+K / Cmd+K
 * JS natif — zéro Alpine.js
 */
export function initSearch() {

    // ── Éléments DOM ─────────────────────────────────────────────
    const modal       = document.getElementById('search-modal');
    const dialog      = document.getElementById('search-dialog');
    const backdrop    = document.getElementById('search-backdrop');
    const input       = document.getElementById('search-input');
    const closeBtn    = document.getElementById('search-close-btn');
    const loader      = document.getElementById('search-loader');
    const resultsWrap = document.getElementById('search-results');
    const resultsList = document.getElementById('search-results-list');
    const emptyState  = document.getElementById('search-empty');
    const quickLinks  = document.getElementById('search-quick');
    const openBtns    = document.querySelectorAll('[data-open-search]');

    // Guard — modal absent du DOM
    if (!modal) {
        console.warn('[Search] #search-modal introuvable dans le DOM.');
        return;
    }

    // ── État ─────────────────────────────────────────────────────
    let isOpen        = false;
    let debounceTimer = null;
    let focusedIndex  = -1;

    // ────────────────────────────────────────────────────────────
    // Ouvrir / Fermer
    // ────────────────────────────────────────────────────────────

    function openModal() {
        if (isOpen) return;
        isOpen = true;

        // Afficher le wrapper
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Bloquer le scroll du body
        document.body.style.overflow = 'hidden';

        // Animation d'entrée
        requestAnimationFrame(() => {
            dialog?.classList.remove('opacity-0', 'scale-95', 'translate-y-2');
            dialog?.classList.add('opacity-100', 'scale-100', 'translate-y-0');

            // Focus sur l'input
            setTimeout(() => input?.focus(), 50);
        });

        // Réinitialiser l'état
        _showQuick();
    }

    function closeModal() {
        if (!isOpen) return;
        isOpen = false;
        focusedIndex = -1;

        // Animation de sortie
        dialog?.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        dialog?.classList.add('opacity-0', 'scale-95', 'translate-y-2');

        // Masquer après la transition
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';

            // Réinitialiser
            if (input) input.value = '';
            _showQuick();
        }, 200);
    }

    // ────────────────────────────────────────────────────────────
    // Événements
    // ────────────────────────────────────────────────────────────

    // Boutons [data-open-search]
    openBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openModal();
        });
    });

    // Bouton Échap dans le modal
    closeBtn?.addEventListener('click', closeModal);

    // Clic sur le backdrop
    backdrop?.addEventListener('click', closeModal);

    // Empêcher la fermeture en cliquant dans le dialog
    dialog?.addEventListener('click', (e) => e.stopPropagation());

    // Raccourcis clavier globaux
    document.addEventListener('keydown', (e) => {

        // Ctrl+K ou Cmd+K → toggle
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            e.stopPropagation();
            isOpen ? closeModal() : openModal();
            return;
        }

        // Échap → fermer
        if (e.key === 'Escape' && isOpen) {
            closeModal();
            return;
        }

        // Navigation clavier dans les résultats
        if (!isOpen) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            _moveFocus(1);
        }

        if (e.key === 'ArrowUp') {
            e.preventDefault();
            _moveFocus(-1);
        }

        // Entrée → naviguer vers le résultat focalisé
        if (e.key === 'Enter') {
            const focused = resultsList?.querySelector('.result-item.is-focused');
            if (focused) {
                focused.click();
            }
        }
    });

    // Input — recherche avec debounce
    input?.addEventListener('input', () => {
        clearTimeout(debounceTimer);

        const q = input.value.trim();
        focusedIndex = -1;

        if (q.length < 2) {
            _showQuick();
            return;
        }

        debounceTimer = setTimeout(() => _doSearch(q), 300);
    });

    // Fermer les liens d'accès rapide
    document.querySelectorAll('[data-search-quick-link]').forEach(link => {
        link.addEventListener('click', closeModal);
    });

    // ────────────────────────────────────────────────────────────
    // Recherche
    // ────────────────────────────────────────────────────────────

    function _doSearch(q) {
        _showLoader();

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        fetch(`/api/search?q=${encodeURIComponent(q)}`, {
            headers: {
                'Accept'       : 'application/json',
                'X-CSRF-TOKEN' : csrfToken,
            },
        })
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then(data => {
            _renderResults(data.results ?? [], q);
        })
        .catch(err => {
            console.error('[Search] Erreur :', err);
            _showEmpty(q);
        });
    }

    // ────────────────────────────────────────────────────────────
    // Rendu des résultats
    // ────────────────────────────────────────────────────────────

    function _renderResults(results, q) {
        if (!resultsList) return;

        if (results.length === 0) {
            _showEmpty(q);
            return;
        }

        resultsList.innerHTML = results.map((r, index) => `
            <a href="${_esc(r.url ?? '#')}"
               data-index="${index}"
               class="result-item flex items-center gap-3 px-4 py-2.5
                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                      transition-colors outline-none">
                <div class="w-7 h-7 rounded-lg
                            bg-blue-100 dark:bg-blue-900/40
                            flex items-center justify-center shrink-0">
                    <i class="bi ${_esc(r.icon ?? 'bi-search')}
                              text-blue-600 dark:text-blue-400 text-sm"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium
                              text-slate-700 dark:text-slate-200 truncate">
                        ${_esc(r.label ?? '')}
                    </p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 truncate">
                        ${_esc(r.sublabel ?? '')}
                    </p>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full shrink-0
                             bg-slate-100 dark:bg-slate-700
                             text-slate-500 dark:text-slate-400">
                    ${_esc(r.type ?? '')}
                </span>
            </a>
        `).join('');

        // Fermer le modal au clic sur un résultat
        resultsList.querySelectorAll('.result-item').forEach(item => {
            item.addEventListener('click', closeModal);
        });

        _showResults();
    }

    // ────────────────────────────────────────────────────────────
    // Navigation clavier
    // ────────────────────────────────────────────────────────────

    function _moveFocus(direction) {
        const items = resultsList?.querySelectorAll('.result-item') ?? [];
        if (!items.length) return;

        // Retirer le focus actuel
        items.forEach(item => item.classList.remove('is-focused', 'bg-slate-50',
            'dark:bg-slate-700/50'));

        // Calculer le nouvel index
        focusedIndex += direction;
        if (focusedIndex < 0) focusedIndex = items.length - 1;
        if (focusedIndex >= items.length) focusedIndex = 0;

        // Appliquer le nouveau focus
        const target = items[focusedIndex];
        if (target) {
            target.classList.add('is-focused', 'bg-slate-50', 'dark:bg-slate-700/50');
            target.scrollIntoView({ block: 'nearest' });
        }
    }

    // ────────────────────────────────────────────────────────────
    // États d'affichage
    // ────────────────────────────────────────────────────────────

    function _showQuick() {
        loader?.classList.add('hidden');
        loader?.classList.remove('flex');
        resultsWrap?.classList.add('hidden');
        emptyState?.classList.add('hidden');
        emptyState?.classList.remove('flex');
        quickLinks?.classList.remove('hidden');
    }

    function _showLoader() {
        quickLinks?.classList.add('hidden');
        resultsWrap?.classList.add('hidden');
        emptyState?.classList.add('hidden');
        emptyState?.classList.remove('flex');
        loader?.classList.remove('hidden');
        loader?.classList.add('flex');
    }

    function _showResults() {
        loader?.classList.add('hidden');
        loader?.classList.remove('flex');
        emptyState?.classList.add('hidden');
        emptyState?.classList.remove('flex');
        quickLinks?.classList.add('hidden');
        resultsWrap?.classList.remove('hidden');
    }

    function _showEmpty(q = '') {
        loader?.classList.add('hidden');
        loader?.classList.remove('flex');
        resultsWrap?.classList.add('hidden');
        quickLinks?.classList.add('hidden');

        const span = emptyState?.querySelector('[data-empty-query]');
        if (span) span.textContent = `« ${q} »`;

        emptyState?.classList.remove('hidden');
        emptyState?.classList.add('flex');
    }

    // ────────────────────────────────────────────────────────────
    // Utilitaire — échapper le HTML (anti-XSS)
    // ────────────────────────────────────────────────────────────

    function _esc(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(String(str)));
        return d.innerHTML;
    }
}