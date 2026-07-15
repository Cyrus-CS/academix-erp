export function initDropdowns() {
    // Gestion générique de tous les dropdowns
    document.querySelectorAll('[data-dropdown]').forEach(trigger => {
        const targetId = trigger.dataset.dropdown;
        const target   = document.getElementById(targetId);
        if (!target) return;

        // Fermer par défaut
        target.classList.add('hidden');

        // Ouvrir/fermer au clic
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = !target.classList.contains('hidden');

            // Fermer tous les autres dropdowns ouverts
            _closeAll();

            if (!isOpen) {
                target.classList.remove('hidden');
                // Animation d'entrée
                requestAnimationFrame(() => {
                    target.classList.add('opacity-100', 'scale-100', 'translate-y-0');
                    target.classList.remove('opacity-0', 'scale-95', 'translate-y-1');
                });
            }
        });
    });

    // Fermer en cliquant ailleurs
    document.addEventListener('click', _closeAll);

    // Fermer avec Échap
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') _closeAll();
    });
}

function _closeAll() {
    document.querySelectorAll('[data-dropdown-menu]').forEach(menu => {
        menu.classList.add('hidden', 'opacity-0', 'scale-95', 'translate-y-1');
        menu.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
    });
}

// Marquer toutes les notifications comme lues
export function initMarkAllRead(url, csrfToken) {
    const btn = document.getElementById('mark-all-read');
    if (!btn) return;

    btn.addEventListener('click', () => {
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            }
        }).then(() => {
            // Mettre le badge à 0
            const badge = document.getElementById('notif-badge');
            if (badge) badge.classList.add('hidden');

            // Vider visuellement la liste
            document.querySelectorAll('.notif-item').forEach(el => {
                el.classList.remove('bg-blue-50', 'dark:bg-blue-950/20');
            });
        });
    });
}