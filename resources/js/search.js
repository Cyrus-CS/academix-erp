/**
 * resources/js/search.js
 * Recherche globale — Modal Ctrl+K / Cmd+K
 * JS natif — corrigé et optimisé
 */
export function initSearch() {

    const modal      = document.getElementById('search-modal');
    const backdrop   = document.getElementById('search-backdrop');
    const input      = document.getElementById('search-input');
    const openBtns   = document.querySelectorAll('[data-open-search]');
    const resultsBox = document.getElementById('search-results');
    const quickLinks = document.getElementById('search-quick');
    const emptyState = document.getElementById('search-empty');
    const loader     = document.getElementById('search-loader');

    // ── Guard : modal introuvable → on sort proprement ──────────
    if (!modal) {
        console.warn('[Search] Modal #search-modal introuvable dans le DOM.');
        return;
    }

    let debounceTimer = null;
    let isOpen        = false;

    // ── Ouvrir ───────────────────────────────────────────────────
    const openModal = () => {
        if (isOpen) return;
        isOpen = true;

        modal.classList.remove('hidden');

        // Animation d'entrée
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0', 'scale-95');
            modal.classList.add('opacity-100', 'scale-100');
            input?.focus();
        });
    };

    // ── Fermer ───────────────────────────────────────────────────
    const closeModal = () => {
        if (!isOpen) return;
        isOpen = false;

        modal.classList.remove('opacity-100', 'scale-100');
        modal.classList.add('opacity-0', 'scale-95');

        // Attendre la fin de la transition avant de cacher
        const onTransitionEnd = () => {
            modal.classList.add('hidden');
            modal.removeEventListener('transitionend', onTransitionEnd);
        };
        modal.addEventListener('transitionend', onTransitionEnd);

        // Réinitialiser
        if (input) input.value = '';
        _showQuick();
    };

    // ── Boutons d'ouverture ──────────────────────────────────────
    openBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openModal();
        });
    });

    // ── Raccourcis clavier ───────────────────────────────────────
    // BUG CORRIGÉ : (e.ctrlKey || && e.metaKey) → (e.ctrlKey || e.metaKey)
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            e.stopPropagation();
            isOpen ? closeModal() : openModal();
        }

        if (e.key === 'Escape' && isOpen) {
            closeModal();
        }

        // Navigation clavier dans les résultats
        if (isOpen && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
            e.preventDefault();
            _navigateResults(e.key);
        }

        // Entrée → ouvre le premier résultat
        if (isOpen && e.key === 'Enter') {
            const focused = resultsBox?.querySelector('a:focus, a.result-focused');
            if (focused) focused.click();
        }
    });

    // ── Backdrop ─────────────────────────────────────────────────
    // BUG CORRIGÉ : backdrop ? addEventListener → backdrop?.addEventListener
    backdrop?.addEventListener('click', (e) => {
        if (e.target === backdrop) closeModal();
    });

    // ── Input — Recherche avec debounce ──────────────────────────
    input?.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const q = input.value.trim();

        if (q.length < 2) {
            _showQuick();
            return;
        }

        debounceTimer = setTimeout(() => _doSearch(q), 300);
    });

    // ── Recherche fetch ──────────────────────────────────────────
    function _doSearch(q) {
        _showLoader();

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        fetch(`/api/search?q=${encodeURIComponent(q)}`, {
            headers: {
                'Accept'       : 'application/json',
                'X-CSRF-TOKEN' : csrfToken,
            },
        })
            .then(r => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then(data => {
                const results = data.results ?? [];
                _renderResults(results, q);
            })
            .catch((err) => {
                console.error('[Search] Erreur fetch :', err);
                _showEmpty(q);
            });
    }

    // ── Rendu des résultats ──────────────────────────────────────
    function _renderResults(results, q) {
        if (!resultsBox) return;

        if (results.length === 0) {
            _showEmpty(q);
            return;
        }

        resultsBox.innerHTML = results.map((r, index) => `
            <a href="${_escapeHtml(r.url ?? '#')}"
               data-result-index="${index}"
               class="result-item flex items-center gap-3 px-4 py-2.5
                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                      focus:bg-slate-50 dark:focus:bg-slate-700/50
                      focus:outline-none transition-colors">
                <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40
                            flex items-center justify-center shrink-0">
                    <i class="bi ${_escapeHtml(r.icon ?? 'bi-search')}
                              text-blue-600 dark:text-blue-400 text-sm"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate">
                        ${_escapeHtml(r.label ?? '')}
                    </p>
                    <p class="text-xs text-slate-400 truncate">${_escapeHtml(r.sublabel ?? '')}</p>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full shrink-0
                             bg-slate-100 dark:bg-slate-700
                             text-slate-500 dark:text-slate-400">
                    ${_escapeHtml(r.type ?? '')}
                </span>
            </a>
        `).join('');

        quickLinks?.classList.add('hidden');
        emptyState?.classList.add('hidden');
        loader?.classList.add('hidden');
        resultsBox.classList.remove('hidden');
    }

    // ── Navigation clavier ───────────────────────────────────────
    function _navigateResults(key) {
        const items = resultsBox?.querySelectorAll('.result-item') ?? [];
        if (!items.length) return;

        const current = resultsBox?.querySelector('.result-focused');
        let idx = current
            ? parseInt(current.dataset.resultIndex ?? '0')
            : -1;

        current?.classList.remove('result-focused');

        if (key === 'ArrowDown') {
            idx = idx < items.length - 1 ? idx + 1 : 0;
        } else {
            idx = idx > 0 ? idx - 1 : items.length - 1;
        }

        const next = items[idx];
        if (next) {
            next.classList.add('result-focused');
            next.focus();
        }
    }

    // ── États d'affichage ────────────────────────────────────────
    function _showQuick() {
        resultsBox?.classList.add('hidden');
        emptyState?.classList.add('hidden');
        loader?.classList.add('hidden');
        quickLinks?.classList.remove('hidden');
    }

    function _showLoader() {
        resultsBox?.classList.add('hidden');
        emptyState?.classList.add('hidden');
        quickLinks?.classList.add('hidden');
        loader?.classList.remove('hidden');
    }

    function _showEmpty(q = '') {
        loader?.classList.add('hidden');
        resultsBox?.classList.add('hidden');
        quickLinks?.classList.add('hidden');

        if (emptyState) {
            // BUG CORRIGÉ : backtick fermant manquant + espace intempestif
            // emptyState.querySelector('span'). textContent = `« ${q} »);`
            const span = emptyState.querySelector('[data-empty-query]');
            if (span) span.textContent = `« ${q} »`;
            emptyState.classList.remove('hidden');
        }
    }

    // ── Utilitaire : échapper le HTML (sécurité XSS) ─────────────
    function _escapeHtml(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(String(str)));
        return div.innerHTML;
    }
}