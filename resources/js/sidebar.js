export function initSidebar() {
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.getElementById('sidebar-overlay');
    const toggleBtn = document.getElementById('sidebar-toggle');

    if (!sidebar) return;

    let isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

    // État initial
    _applyCollapsed(sidebar, isCollapsed);

    // Toggle desktop/mobile
    toggleBtn?.addEventListener('click', () => {
        if (window.innerWidth < 1024) {
            _toggleMobile(sidebar, overlay);
        } else {
            isCollapsed = !isCollapsed;
            localStorage.setItem('sidebarCollapsed', isCollapsed);
            _applyCollapsed(sidebar, isCollapsed);
        }
    });

    // Fermer via overlay (mobile)
    overlay?.addEventListener('click', () => {
        _closeMobile(sidebar, overlay);
    });

    // Fermer au resize vers desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            _closeMobile(sidebar, overlay);
        }
    });
}

function _applyCollapsed(sidebar, isCollapsed) {
    const mainContent   = document.getElementById('main-content');
    const iconToggle    = document.getElementById('toggle-icon');

    sidebar.classList.toggle('w-[260px]', !isCollapsed);
    sidebar.classList.toggle('w-[72px]', isCollapsed);
    sidebar.classList.toggle('sidebar-collapsed', isCollapsed);

    if (mainContent) {
        mainContent.classList.toggle('ml-[260px]', !isCollapsed);
        mainContent.classList.toggle('ml-[72px]', isCollapsed);
    }

    if (iconToggle) {
        iconToggle.className = isCollapsed
            ? 'bi bi-layout-sidebar-inset text-xl'
            : 'bi bi-layout-sidebar-inset-reverse text-xl';
    }
}

function _toggleMobile(sidebar, overlay) {
    const isOpen = !sidebar.classList.contains('-translate-x-full');
    if (isOpen) {
        _closeMobile(sidebar, overlay);
    } else {
        sidebar.classList.remove('-translate-x-full');
        overlay?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
}

function _closeMobile(sidebar, overlay) {
    sidebar.classList.add('-translate-x-full');
    overlay?.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}