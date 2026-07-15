/**
 * Gestion des notifications en temps réel (polling AJAX)
 * - Badge cloche mis à jour toutes les 30s
 * - Dropdown toggle
 * - Mark all read
 */
export function initNotifications() {
    const btn         = document.getElementById('notif-btn');
    const dropdown    = document.getElementById('notif-dropdown');
    const badge       = document.getElementById('notif-badge');
    const badgeHeader = document.getElementById('notif-badge-header');
    const markAllBtn  = document.getElementById('mark-all-read');
    const notifList   = document.getElementById('notif-list');
    const csrfToken   = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    if (!btn || !dropdown) return;

    let isOpen        = false;
    let currentCount  = parseInt(badge?.textContent ?? '0') || 0;
    let pollingTimer  = null;

    // ── Toggle dropdown ───────────────────────────────────────────
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        isOpen ? closeDropdown() : openDropdown();
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && e.target !== btn) {
            closeDropdown();
        }
    });

    function openDropdown() {
        isOpen = true;
        dropdown.classList.remove('hidden');

        // Déclencher l'animation
        requestAnimationFrame(() => {
            dropdown.classList.remove('opacity-0', 'scale-95', 'translate-y-1');
            dropdown.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        });

        // Charger les notifications fraîches
        fetchNotifications();
    }

    function closeDropdown() {
        isOpen = false;
        dropdown.classList.add('opacity-0', 'scale-95', 'translate-y-1');
        dropdown.classList.remove('opacity-100', 'scale-100', 'translate-y-0');

        setTimeout(() => dropdown.classList.add('hidden'), 200);
    }

    // ── Polling : vérifier le nombre de notifs toutes les 30s ─────
    function startPolling() {
        fetchCount(); // immédiat
        pollingTimer = setInterval(fetchCount, 30000);
    }

    async function fetchCount() {
        try {
            const res  = await fetch('/notifications/unread-count', {
                headers: { 'Accept': 'application/json' },
            });

            if (!res.ok) return;

            const data  = await res.json();
            const count = data.count ?? 0;

            updateBadge(count);

        } catch {
            // Silencieux : pas de connexion ou erreur serveur
        }
    }

    // ── Charger les notifications dans le dropdown ─────────────────
    async function fetchNotifications() {
        try {
            const res = await fetch('/notifications/latest', {
                headers: { 'Accept': 'application/json' },
            });

            if (!res.ok) return;

            const data          = await res.json();
            const notifications = data.notifications ?? [];

            renderNotifications(notifications);
            updateBadge(data.unread_count ?? 0);

        } catch {
            // Silencieux
        }
    }

    // ── Rendu HTML des notifications ───────────────────────────────
    function renderNotifications(notifications) {
        if (!notifList) return;

        if (notifications.length === 0) {
            notifList.innerHTML = `
                <div class="flex flex-col items-center justify-center py-8
                            text-slate-400 dark:text-slate-500">
                    <i class="bi bi-bell-slash text-3xl mb-2"></i>
                    <p class="text-xs">Aucune nouvelle notification</p>
                </div>`;
            return;
        }

        const typeIcons = {
            payment     : { icon: 'bi-credit-card-fill',  color: 'text-emerald-500', bg: 'bg-emerald-100 dark:bg-emerald-900/30' },
            attendance  : { icon: 'bi-person-check-fill',  color: 'text-blue-500',    bg: 'bg-blue-100 dark:bg-blue-900/30' },
            grade       : { icon: 'bi-pencil-square',      color: 'text-amber-500',   bg: 'bg-amber-100 dark:bg-amber-900/30' },
            announcement: { icon: 'bi-megaphone-fill',     color: 'text-cyan-500',    bg: 'bg-cyan-100 dark:bg-cyan-900/30' },
            info        : { icon: 'bi-info-circle-fill',   color: 'text-blue-500',    bg: 'bg-blue-100 dark:bg-blue-900/30' },
        };

        notifList.innerHTML = notifications.map(n => {
            const cfg  = typeIcons[n.data?.type ?? 'info'] ?? typeIcons.info;
            const url  = n.data?.url ?? '#';
            const time = n.created_at_human ?? '';

            return `
            <div class="flex gap-3 px-4 py-3
                        hover:bg-slate-50 dark:hover:bg-slate-700/50
                        transition-colors cursor-pointer"
                 onclick="window.location='${url}'">
                <div class="w-8 h-8 rounded-full ${cfg.bg}
                            flex items-center justify-center shrink-0">
                    <i class="bi ${cfg.icon} ${cfg.color} text-sm"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">
                        ${n.data?.title ?? 'Notification'}
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5
                              line-clamp-2 leading-relaxed">
                        ${n.data?.message ?? ''}
                    </p>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">
                        ${time}
                    </p>
                </div>
                ${!n.read_at ? '<div class="w-2 h-2 rounded-full bg-blue-500 shrink-0 mt-1.5"></div>' : ''}
            </div>`;
        }).join('');
    }

    // ── Mettre à jour le badge ─────────────────────────────────────
    function updateBadge(count) {
        const prev = currentCount;
        currentCount = count;

        if (!badge) return;

        if (count <= 0) {
            badge.classList.add('hidden');
            badgeHeader?.classList.add('hidden');
            markAllBtn?.classList.add('hidden');
        } else {
            const label = count > 9 ? '9+' : count;
            badge.textContent = label;
            badge.classList.remove('hidden');

            if (badgeHeader) {
                badgeHeader.textContent = label;
                badgeHeader.classList.remove('hidden');
            }

            markAllBtn?.classList.remove('hidden');

            // Animation si nouveau(x) message(s)
            if (count > prev) {
                badge.classList.add('animate-bounce');
                setTimeout(() => badge.classList.remove('animate-bounce'), 1500);

                // Toast discret si dropdown fermé
                if (!isOpen && typeof window.showToast === 'function') {
                    window.showToast({
                        type   : 'info',
                        title  : 'Nouvelle notification',
                        message: `Vous avez ${count} notification${count > 1 ? 's' : ''} non lue${count > 1 ? 's' : ''}.`,
                        delay  : 4000,
                    });
                }
            }
        }
    }

    // ── Marquer tout comme lu ──────────────────────────────────────
    markAllBtn?.addEventListener('click', async (e) => {
        e.stopPropagation();

        try {
            const res = await fetch('/notifications/mark-all-read', {
                method  : 'POST',
                headers : {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept'      : 'application/json',
                },
            });

            if (res.ok) {
                updateBadge(0);

                // Supprimer les points bleus
                notifList?.querySelectorAll('.bg-blue-500.rounded-full')
                    .forEach(dot => dot.remove());

                window.showToast?.({
                    type   : 'success',
                    title  : 'Notifications',
                    message: 'Toutes les notifications ont été marquées comme lues.',
                });
            }
        } catch {
            // Silencieux
        }
    });

    // ── Démarrer le polling ────────────────────────────────────────
    startPolling();

    // Nettoyer si la page est déchargée
    window.addEventListener('beforeunload', () => {
        clearInterval(pollingTimer);
    });
}