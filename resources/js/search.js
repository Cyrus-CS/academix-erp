/**
 * search.js — Recherche globale
 * JS natif, zéro dépendance externe
 * Navigation clavier ↑↓ + Entrée + Échap
 */
export function initSearch() {

    // ── Éléments DOM ──────────────────────────────────────────────
    const modal       = document.getElementById('search-modal');
    const dialog      = document.getElementById('search-dialog');
    const backdrop    = document.getElementById('search-backdrop');
    const input       = document.getElementById('search-input');
    const closeBtn    = document.getElementById('search-close-btn');
    const resultsList = document.getElementById('search-results-list');
    const resultsWrap = document.getElementById('search-results');
    const quickLinks  = document.getElementById('search-quick');
    const emptyState  = document.getElementById('search-empty');
    const emptyQuery  = document.querySelector('[data-empty-query]');
    const loader      = document.getElementById('search-loader');
    const openBtns    = document.querySelectorAll('[data-open-search]');

    if (!modal) return;

    // ── État ──────────────────────────────────────────────────────
    let debounceTimer   = null;
    let activeIndex     = -1;
    let currentResults  = [];
    let isOpen          = false;
    const DEBOUNCE_MS   = 300;
    const MIN_LENGTH    = 2;

    // ── Couleurs par type ─────────────────────────────────────────
    const COLOR_MAP = {
        blue:    { bg: 'bg-blue-50 dark:bg-blue-900/20',    text: 'text-blue-600 dark:text-blue-400',    badge: 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400' },
        emerald: { bg: 'bg-emerald-50 dark:bg-emerald-900/20', text: 'text-emerald-600 dark:text-emerald-400', badge: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400' },
        indigo:  { bg: 'bg-indigo-50 dark:bg-indigo-900/20',  text: 'text-indigo-600 dark:text-indigo-400',  badge: 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400' },
        cyan:    { bg: 'bg-cyan-50 dark:bg-cyan-900/20',    text: 'text-cyan-600 dark:text-cyan-400',    badge: 'bg-cyan-100 dark:bg-cyan-900/40 text-cyan-600 dark:text-cyan-400' },
        amber:   { bg: 'bg-amber-50 dark:bg-amber-900/20',  text: 'text-amber-600 dark:text-amber-400',  badge: 'bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400' },
        red:     { bg: 'bg-red-50 dark:bg-red-900/20',      text: 'text-red-600 dark:text-red-400',      badge: 'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400' },
    };

    /*
    |--------------------------------------------------------------------------
    | OPEN / CLOSE
    |--------------------------------------------------------------------------
    */
    function openModal() {
        if (isOpen) return;
        isOpen = true;

        modal.classList.remove('hidden');

        // Animation d'ouverture
        requestAnimationFrame(() => {
            dialog.classList.remove('opacity-0', 'scale-95', 'translate-y-2');
            dialog.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        });

        setTimeout(() => input?.focus(), 50);
        _showQuick();
    }

    function closeModal() {
        if (!isOpen) return;
        isOpen = false;

        // Animation de fermeture
        dialog.classList.add('opacity-0', 'scale-95', 'translate-y-2');
        dialog.classList.remove('opacity-100', 'scale-100', 'translate-y-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            if (input) input.value = '';
            activeIndex = -1;
            currentResults = [];
            _showQuick();
        }, 200);
    }

    /*
    |--------------------------------------------------------------------------
    | ÉVÉNEMENTS D'OUVERTURE / FERMETURE
    |--------------------------------------------------------------------------
    */
    openBtns.forEach(btn => btn.addEventListener('click', openModal));
    closeBtn?.addEventListener('click', closeModal);
    backdrop?.addEventListener('click', closeModal);

    document.addEventListener('keydown', (e) => {
        // Ctrl+K / Cmd+K → ouvrir
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            isOpen ? closeModal() : openModal();
            return;
        }

        if (!isOpen) return;

        switch (e.key) {
            case 'Escape':
                e.preventDefault();
                closeModal();
                break;

            case 'ArrowDown':
                e.preventDefault();
                _navigateResults(1);
                break;

            case 'ArrowUp':
                e.preventDefault();
                _navigateResults(-1);
                break;

            case 'Enter':
                e.preventDefault();
                _openActiveResult();
                break;
        }
    });

    /*
    |--------------------------------------------------------------------------
    | INPUT — Recherche avec debounce
    |--------------------------------------------------------------------------
    */
    input?.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        activeIndex = -1;

        const q = input.value.trim();

        if (q.length < MIN_LENGTH) {
            _showQuick();
            return;
        }

        debounceTimer = setTimeout(() => _doSearch(q), DEBOUNCE_MS);
    });

    /*
    |--------------------------------------------------------------------------
    | FETCH
    |--------------------------------------------------------------------------
    */
    function _doSearch(q) {
        _showLoader();

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        fetch(`/api/search?q=${encodeURIComponent(q)}`, {
            method: 'GET',
            headers: {
                'Accept':           'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN':     csrfToken,
            },
        })
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then(data => {
            currentResults = data.results ?? [];
            activeIndex    = -1;

            if (currentResults.length === 0) {
                _showEmpty(q);
            } else {
                _renderResults(currentResults, q);
            }
        })
        .catch(() => _showEmpty(q));
    }

    /*
    |--------------------------------------------------------------------------
    | RENDU DES RÉSULTATS
    |--------------------------------------------------------------------------
    */
    function _renderResults(results, q) {
        if (!resultsList) return;

        // Highlight du terme recherché
        const highlight = (text) => {
            if (!text) return '';
            const escaped = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const regex   = new RegExp(`(${escaped})`, 'gi');
            return String(text).replace(
                regex,
                '<mark class="bg-yellow-100 dark:bg-yellow-800/40 text-yellow-800 dark:text-yellow-200 rounded px-0.5">$1</mark>'
            );
        };

        resultsList.innerHTML = results.map((r, index) => {
            const color = COLOR_MAP[r.color] ?? COLOR_MAP.blue;

            return `
                <a href="${_escapeHtml(r.url)}"
                   data-result-index="${index}"
                   class="result-item flex items-center gap-3 px-4 py-2.5
                          hover:bg-slate-50 dark:hover:bg-slate-700/50
                          transition-colors cursor-pointer outline-none
                          focus:bg-slate-50 dark:focus:bg-slate-700/50"
                   tabindex="-1">

                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 ${color.bg}">
                        <i class="bi ${_escapeHtml(r.icon ?? 'bi-search')} ${color.text} text-sm"></i>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate">
                            ${highlight(r.label)}
                        </p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 truncate mt-0.5">
                            ${highlight(r.sublabel)}
                        </p>
                    </div>

                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium shrink-0 ${color.badge}">
                        ${_escapeHtml(r.type ?? '')}
                    </span>
                </a>
            `;
        }).join('');

        // Événements sur les items
        resultsList.querySelectorAll('.result-item').forEach((item, index) => {
            item.addEventListener('mouseenter', () => _setActive(index));
            item.addEventListener('mouseleave', () => _clearActive());
        });

        // Afficher la section résultats
        loader?.classList.add('hidden');
        emptyState?.classList.add('hidden');
        quickLinks?.classList.add('hidden');
        resultsWrap?.classList.remove('hidden');
    }

    /*
    |--------------------------------------------------------------------------
    | NAVIGATION CLAVIER ↑↓
    |--------------------------------------------------------------------------
    */
    function _navigateResults(direction) {
        if (currentResults.length === 0) return;

        const items = resultsList?.querySelectorAll('.result-item');
        if (!items || items.length === 0) return;

        // Retirer l'état actif précédent
        _clearActive();

        // Calculer le nouvel index
        activeIndex += direction;

        if (activeIndex < 0)                    activeIndex = items.length - 1;
        if (activeIndex >= items.length)        activeIndex = 0;

        _setActive(activeIndex);

        // Scroll automatique pour garder l'élément visible
        items[activeIndex]?.scrollIntoView({ block: 'nearest' });
    }

    function _setActive(index) {
        const items = resultsList?.querySelectorAll('.result-item');
        if (!items) return;

        // Retirer l'état actif de tous
        items.forEach(item => {
            item.classList.remove('bg-slate-50', 'dark:bg-slate-700/50');
            item.removeAttribute('aria-selected');
        });

        // Appliquer sur le sélectionné
        if (items[index]) {
            items[index].classList.add('bg-slate-50', 'dark:bg-slate-700/50');
            items[index].setAttribute('aria-selected', 'true');
            activeIndex = index;
        }
    }

    function _clearActive() {
        const items = resultsList?.querySelectorAll('.result-item');
        items?.forEach(item => {
            item.classList.remove('bg-slate-50', 'dark:bg-slate-700/50');
            item.removeAttribute('aria-selected');
        });
    }

    function _openActiveResult() {
        if (activeIndex < 0 || currentResults.length === 0) return;

        const result = currentResults[activeIndex];
        if (result?.url) {
            window.location.href = result.url;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ÉTATS : Quick / Loader / Empty
    |--------------------------------------------------------------------------
    */
    function _showQuick() {
        loader?.classList.add('hidden');
        emptyState?.classList.add('hidden');
        resultsWrap?.classList.add('hidden');
        quickLinks?.classList.remove('hidden');
    }

    function _showLoader() {
        quickLinks?.classList.add('hidden');
        emptyState?.classList.add('hidden');
        resultsWrap?.classList.add('hidden');
        loader?.classList.remove('hidden');
        loader?.classList.add('flex');
    }

    function _showEmpty(q = '') {
        loader?.classList.remove('flex');
        loader?.classList.add('hidden');
        resultsWrap?.classList.add('hidden');
        quickLinks?.classList.add('hidden');

        if (emptyQuery) emptyQuery.textContent = `« ${q} »`;

        emptyState?.classList.remove('hidden');
        emptyState?.classList.add('flex');
    }

    /*
    |--------------------------------------------------------------------------
    | UTILS
    |--------------------------------------------------------------------------
    */
    function _escapeHtml(str) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(str ?? '').replace(/[&<>"']/g, m => map[m]);
    }
}