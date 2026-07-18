/**
 * resources/js/sidebar.js
 * Sidebar collapse/expand + mobile overlay — corrigé
 */
export function initSidebar() {

    const sidebar        = document.getElementById('sidebar');
    const toggleBtn      = document.getElementById('sidebar-toggle');
    const toggleIcon     = document.getElementById('toggle-icon');
    const mainContent    = document.getElementById('main-content');
    const overlay        = document.getElementById('sidebar-overlay');

    if (!sidebar) {
        console.warn('[Sidebar] #sidebar introuvable.');
        return;
    }

    // ── État initial depuis localStorage ────────────────────────
    const STORAGE_KEY = 'sidebar-collapsed';
    let isCollapsed   = localStorage.getItem(STORAGE_KEY) === 'true';
    let isMobileOpen  = false;
    let resizeTimer   = null;

    // ── Classes CSS ──────────────────────────────────────────────
    const CSS = {
        // Sidebar
        collapsed     : ['w-18', 'sidebar-collapsed'],
        expanded      : ['w-65'],

        // Main content
        mainCollapsed : ['ml-18'],
        mainExpanded  : ['ml-65'],

        // Mobile
        mobileVisible : ['translate-x-0'],
        mobileHidden  : ['-translate-x-full'],

        // Overlay
        overlayShow   : ['block'],
        overlayHide   : ['hidden'],
    };

    // ── Appliquer l'état initial SANS transition ─────────────────
    // (évite le flash / FOUC au chargement)
    _applyState(false);

    // ── Activer les transitions APRÈS le premier rendu ───────────
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            sidebar.classList.add('transition-all', 'duration-300', 'ease-in-out');
            mainContent?.classList.add('transition-all', 'duration-300', 'ease-in-out');
        });
    });

    // ── Toggle bouton ────────────────────────────────────────────
    toggleBtn?.addEventListener('click', () => {
        const isMobile = _isMobileView();

        if (isMobile) {
            _toggleMobile();
        } else {
            isCollapsed = !isCollapsed;
            localStorage.setItem(STORAGE_KEY, isCollapsed);
            _applyState(true);
        }
    });

    // ── Overlay mobile ───────────────────────────────────────────
    overlay?.addEventListener('click', () => {
        if (isMobileOpen) _closeMobile();
    });

    // ── Fermer sidebar depuis un event global ────────────────────
    document.addEventListener('close-sidebar', () => {
        if (isMobileOpen) _closeMobile();
    });

    // ── Resize : recalculer l'état sans casser le layout ─────────
    // CORRECTION CLÉ : debounce du resize pour éviter le bug DevTools
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            _handleResize();
        }, 150); // debounce 150ms
    });

    // ── Swipe mobile (touch) ─────────────────────────────────────
    _initSwipeGesture();

    // ────────────────────────────────────────────────────────────
    // Fonctions internes
    // ────────────────────────────────────────────────────────────

    function _applyState(animated = true) {
        if (!animated) {
            // Désactiver temporairement les transitions
            sidebar.style.transition       = 'none';
            mainContent && (mainContent.style.transition = 'none');
        }

        if (_isMobileView()) {
            // ── MODE MOBILE ──
            // La sidebar est toujours en overlay sur mobile
            _setClasses(sidebar, CSS.expanded, CSS.collapsed);

            if (isMobileOpen) {
                _setClasses(sidebar, CSS.mobileVisible, CSS.mobileHidden);
            } else {
                _setClasses(sidebar, CSS.mobileHidden, CSS.mobileVisible);
            }

            // Pas de margin sur le main en mobile
            if (mainContent) {
                mainContent.classList.remove(...CSS.mainCollapsed, ...CSS.mainExpanded);
            }

        } else {
            // ── MODE DESKTOP ──
            // La sidebar est toujours visible, collapsed ou expanded
            _setClasses(sidebar, CSS.mobileVisible, CSS.mobileHidden);
            overlay?.classList.add('hidden');

            if (isCollapsed) {
                _setClasses(sidebar, CSS.collapsed, CSS.expanded);
                if (mainContent) {
                    _setClasses(mainContent, CSS.mainCollapsed, CSS.mainExpanded);
                }
            } else {
                _setClasses(sidebar, CSS.expanded, CSS.collapsed);
                if (mainContent) {
                    _setClasses(mainContent, CSS.mainExpanded, CSS.mainCollapsed);
                }
            }

            // Icône toggle
            if (toggleIcon) {
                toggleIcon.className = isCollapsed
                    ? 'bi bi-layout-sidebar-inset text-xl'
                    : 'bi bi-layout-sidebar-inset-reverse text-xl';
            }
        }

        if (!animated) {
            // Réactiver les transitions après le prochain frame
            requestAnimationFrame(() => {
                sidebar.style.transition       = '';
                mainContent && (mainContent.style.transition = '');
            });
        }
    }

    function _toggleMobile() {
        isMobileOpen = !isMobileOpen;
        _applyState(true);

        if (isMobileOpen) {
            overlay?.classList.remove('hidden');
            overlay?.classList.add('block');
        } else {
            overlay?.classList.add('hidden');
            overlay?.classList.remove('block');
        }
    }

    function _closeMobile() {
        isMobileOpen = false;
        _applyState(true);
        overlay?.classList.add('hidden');
        overlay?.classList.remove('block');
    }

    function _handleResize() {
        const isMobile = _isMobileView();

        if (!isMobile) {
            // Revenir au desktop → fermer l'overlay mobile
            isMobileOpen = false;
            overlay?.classList.add('hidden');
        }

        // Réappliquer l'état SANS animation pour éviter le flash
        _applyState(false);
    }

    function _isMobileView() {
        return window.innerWidth < 1024; // lg breakpoint Tailwind
    }

    // Helper : ajouter/retirer des classes en une fois
    function _setClasses(el, toAdd, toRemove) {
        if (!el) return;
        el.classList.remove(...toRemove);
        el.classList.add(...toAdd);
    }

    // ── Swipe gesture (mobile) ───────────────────────────────────
    function _initSwipeGesture() {
        let touchStartX = 0;
        let touchStartY = 0;
        const SWIPE_THRESHOLD = 60;

        document.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }, { passive: true });

        document.addEventListener('touchend', (e) => {
            if (!_isMobileView()) return;

            const dx = e.changedTouches[0].clientX - touchStartX;
            const dy = e.changedTouches[0].clientY - touchStartY;

            // S'assurer que c'est un swipe horizontal
            if (Math.abs(dx) < Math.abs(dy)) return;

            // Swipe droite depuis le bord gauche → ouvrir
            if (dx > SWIPE_THRESHOLD && touchStartX < 30 && !isMobileOpen) {
                _toggleMobile();
                return;
            }

            // Swipe gauche → fermer
            if (dx < -SWIPE_THRESHOLD && isMobileOpen) {
                _closeMobile();
            }
        }, { passive: true });
    }
}