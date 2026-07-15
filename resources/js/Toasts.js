export function initToasts() {

// ── Toast notifications ─────────────────────────────────────────── 
    const toasts = document.querySelectorAll('[data-toast]');

    toasts.forEach(toast => {
        const delay     = parseInt(toast.dataset.delay ?? 5000);
        const closeBtn  = toast.querySelector('[data-toast-close]');

        // Fermer au clic sur X
        closeBtn?.addEventListener('click', () => dismissToast(toast));

        // Auto-fermeture
        setTimeout(() => dismissToast(toast), delay);
    });
}

function dismissToast(toast) {
    toast.classList.add('toast-leaving');
    toast.addEventListener('animationend', () => {
        toast.remove();

        // Supprimer le conteneur si vide
        const container = document.getElementById('flash-container');
        if (container && !container.querySelector('[data-toast]')) {
            container.remove();
        }
    }, { once: true });
}

// Exposer pour usage externe (ex: AJAX)
window.showToast = function ({ type = 'info', title = '', message = '', delay = 5000 }) {
    const configs = {
        success: { icon: 'bi-check-circle-fill',        iconColor: 'text-emerald-500', border: 'border-emerald-500/20', bg: 'bg-emerald-500' },
        error:   { icon: 'bi-x-circle-fill',            iconColor: 'text-red-500',     border: 'border-red-500/20',     bg: 'bg-red-500' },
        warning: { icon: 'bi-exclamation-triangle-fill', iconColor: 'text-amber-500',   border: 'border-amber-500/20',   bg: 'bg-amber-500' },
        info:    { icon: 'bi-info-circle-fill',          iconColor: 'text-cyan-500',    border: 'border-cyan-500/20',    bg: 'bg-cyan-500' },
    };

    const cfg = configs[type] ?? configs.info;

    // Créer ou récupérer le conteneur
    let container = document.getElementById('flash-container');
    if (!container) {
        container = document.createElement('div');
        container.id        = 'flash-container';
        container.className = 'fixed top-5 right-5 z-[200] flex flex-col gap-3 w-full max-w-sm pointer-events-none';
        document.body.appendChild(container);
    }

    // Créer le toast
    const toast = document.createElement('div');
    toast.dataset.toast = '';
    toast.className = `toast-item pointer-events-auto relative
        flex items-start gap-3.5 px-4 py-3.5 rounded-xl shadow-lg border ${cfg.border}
        bg-slate-800/95 dark:bg-slate-900/95 backdrop-blur-sm`;

    toast.innerHTML = `
        <i class="bi ${cfg.icon} ${cfg.iconColor} text-xl shrink-0 mt-0.5"></i>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-white leading-snug">${title}</p>
            <p class="text-sm text-slate-400 mt-0.5 leading-snug">${message}</p>
        </div>
        <button data-toast-close
                class="shrink-0 text-slate-500 hover:text-slate-300 transition-colors focus:outline-none mt-0.5">
            <i class="bi bi-x-lg text-sm"></i>
        </button>
        <div class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-xl overflow-hidden">
            <div class="h-full ${cfg.bg} w-full origin-left"
                 style="animation: toast-shrink ${delay}ms linear forwards;"></div>
        </div>
    `;

    container.appendChild(toast);

    // Fermer au clic
    toast.querySelector('[data-toast-close]')
         ?.addEventListener('click', () => dismissToast(toast));

    // Auto-dismiss
    setTimeout(() => dismissToast(toast), delay);
};
