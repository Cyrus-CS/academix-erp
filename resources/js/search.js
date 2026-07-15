export function initSearch() {
    const modal      = document.getElementById('search-modal');
    const backdrop   = document.getElementById('search-backdrop');
    const input      = document.getElementById('search-input');
    const openBtns   = document.querySelectorAll('[data-open-search]');
    const resultsBox = document.getElementById('search-results');
    const quickLinks = document.getElementById('search-quick');
    const emptyState = document.getElementById('search-empty');
    const loader     = document.getElementById('search-loader');

    if (!modal) return;

    let debounceTimer = null;

    // Ouvrir
    const openModal = () => {
        modal.classList.remove('hidden');
        requestAnimationFrame(() => input?.focus());
    };

    // Fermer
    const closeModal = () => {
        modal.classList.add('hidden');
        if (input) input.value = '';
        _showQuick();
    };

    // Boutons d'ouverture
    openBtns.forEach(btn => btn.addEventListener('click', openModal));

    // Raccourci Ctrl+K / Cmd+K
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            openModal();
        }
        if (e.key === 'Escape') closeModal();
    });

    // Backdrop
    backdrop?.addEventListener('click', closeModal);

    // Recherche avec debounce
    input?.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const q = input.value.trim();

        if (q.length < 2) {
            _showQuick();
            return;
        }

        debounceTimer = setTimeout(() => _doSearch(q), 300);
    });

    function _doSearch(q) {
        _showLoader();

        fetch(`/api/search?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                const results = data.results ?? [];
                _renderResults(results, q);
            })
            .catch(() => _showEmpty());
    }

    function _renderResults(results, q) {
        if (!resultsBox) return;

        if (results.length === 0) {
            _showEmpty(q);
            return;
        }

        resultsBox.innerHTML = results.map(r => `
            <a href="${r.url}"
               class="flex items-center gap-3 px-4 py-2.5
                      hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40
                            flex items-center justify-center shrink-0">
                    <i class="bi ${r.icon ?? 'bi-search'}
                              text-blue-600 dark:text-blue-400 text-sm"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate">
                        ${r.label}
                    </p>
                    <p class="text-xs text-slate-400">${r.sublabel ?? ''}</p>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full
                             bg-slate-100 dark:bg-slate-700
                             text-slate-500 dark:text-slate-400">
                    ${r.type ?? ''}
                </span>
            </a>
        `).join('');

        quickLinks?.classList.add('hidden');
        emptyState?.classList.add('hidden');
        loader?.classList.add('hidden');
        resultsBox.classList.remove('hidden');
    }

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
            emptyState.querySelector('span')
                && (emptyState.querySelector('span').textContent = `« ${q} »`);
            emptyState.classList.remove('hidden');
        }
    }
}