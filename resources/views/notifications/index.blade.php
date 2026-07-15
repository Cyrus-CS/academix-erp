@extends('layouts.base')

@section('page_title', 'Notifications')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm shrink-0">
            <i class="bi bi-bell-fill text-white text-lg"></i>
        </div>
        <div class="min-w-0">
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                Notifications
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                @if($stats['unread'] > 0)
                <span class="text-blue-600 dark:text-blue-400 font-semibold">
                    {{ $stats['unread'] }} non lue{{ $stats['unread'] > 1 ? 's' : '' }}
                </span>
                sur {{ $stats['total'] }} notification{{ $stats['total'] > 1 ? 's' : '' }}
                @else
                Toutes vos notifications sont lues
                @endif
            </p>
        </div>
    </div>

    {{-- Actions en-tête --}}
    <div class="flex items-center gap-2 shrink-0">
        @if($stats['read'] > 0)
        <button id="btn-delete-read" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-medium
                           border border-red-200 dark:border-red-800
                           text-red-600 dark:text-red-400
                           hover:bg-red-50 dark:hover:bg-red-900/20
                           transition-all duration-200">
            <i class="bi bi-trash3"></i>
            <span class="hidden sm:inline">Supprimer les lues</span>
        </button>
        @endif

        @if($stats['unread'] > 0)
        <button id="btn-mark-all" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-medium
                           bg-blue-600 hover:bg-blue-700 text-white
                           shadow-sm transition-all duration-200
                           focus:outline-none focus:ring-2 focus:ring-blue-500/40">
            <i class="bi bi-check-all"></i>
            <span class="hidden sm:inline">Tout marquer lu</span>
        </button>
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    {{-- ── Statistiques ── --}}
    <div class="grid grid-cols-3 gap-4">
        @foreach([
        ['label' => 'Total', 'value' => $stats['total'], 'icon' => 'bi-bell', 'color' => 'blue'],
        ['label' => 'Non lues', 'value' => $stats['unread'], 'icon' => 'bi-bell-fill', 'color' => 'amber'],
        ['label' => 'Lues', 'value' => $stats['read'], 'icon' => 'bi-bell-slash', 'color' => 'emerald'],
        ] as $stat)
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl
                        bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/30
                        flex items-center justify-center shrink-0">
                <i class="bi {{ $stat['icon'] }}
                          text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xl font-bold text-slate-800 dark:text-slate-100">
                    {{ $stat['value'] }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                    {{ $stat['label'] }}
                </p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Filtres ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm">

        <div class="flex items-center gap-2 p-3 overflow-x-auto">
            @foreach([
            ['param' => null, 'label' => 'Toutes', 'icon' => 'bi-grid'],
            ['param' => 'unread', 'label' => 'Non lues', 'icon' => 'bi-circle-fill'],
            ['param' => 'read', 'label' => 'Lues', 'icon' => 'bi-check-circle'],
            ] as $filter)
            @php
            $isActive = request('filter') === $filter['param']
            || ($filter['param'] === null && !request('filter'));
            @endphp
            <a href="{{ request()->fullUrlWithQuery(['filter' => $filter['param']]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                      whitespace-nowrap transition-all duration-200
                      {{ $isActive
                          ? 'bg-blue-600 text-white shadow-sm'
                          : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50' }}">
                <i class="bi {{ $filter['icon'] }} text-xs
                          {{ $isActive ? '' : 'text-slate-400 dark:text-slate-500' }}"></i>
                {{ $filter['label'] }}
                @if($filter['param'] === 'unread' && $stats['unread'] > 0)
                <span
                    class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold
                                 {{ $isActive ? 'bg-white/20 text-white' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' }}">
                    {{ $stats['unread'] }}
                </span>
                @endif
            </a>
            @endforeach
        </div>
    </div>

    {{-- ── Liste des notifications ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm overflow-hidden">

        @forelse($notifications as $notification)
        @php
        $isUnread = is_null($notification->read_at);
        $data = $notification->data ?? [];
        $type = $data['type'] ?? 'info';

        $typeConfig = [
        'payment' => ['icon' => 'bi-credit-card-fill', 'color' => 'emerald', 'bg' => 'emerald'],
        'attendance' => ['icon' => 'bi-person-check-fill', 'color' => 'blue', 'bg' => 'blue'],
        'grade' => ['icon' => 'bi-pencil-square', 'color' => 'amber', 'bg' => 'amber'],
        'announcement'=> ['icon' => 'bi-megaphone-fill', 'color' => 'cyan', 'bg' => 'cyan'],
        'info' => ['icon' => 'bi-info-circle-fill', 'color' => 'blue', 'bg' => 'blue'],
        'warning' => ['icon' => 'bi-exclamation-triangle-fill','color'=>'amber', 'bg' => 'amber'],
        'error' => ['icon' => 'bi-x-circle-fill', 'color' => 'red', 'bg' => 'red'],
        ][$type] ?? ['icon' => 'bi-bell-fill', 'color' => 'blue', 'bg' => 'blue'];
        @endphp

        <div class="notif-item flex items-start gap-4 px-5 py-4
                    border-b border-slate-100 dark:border-slate-700 last:border-0
                    {{ $isUnread
                        ? 'bg-blue-50/50 dark:bg-blue-950/20 hover:bg-blue-50 dark:hover:bg-blue-950/30'
                        : 'hover:bg-slate-50 dark:hover:bg-slate-700/30' }}
                    transition-colors duration-200 group relative" data-id="{{ $notification->id }}">

            {{-- Indicateur non lu --}}
            @if($isUnread)
            <div class="absolute left-0 top-0 bottom-0 w-1 rounded-r-full bg-blue-500"></div>
            @endif

            {{-- Icône --}}
            <div class="w-10 h-10 rounded-xl shrink-0 mt-0.5
                        bg-{{ $typeConfig['bg'] }}-100 dark:bg-{{ $typeConfig['bg'] }}-900/30
                        flex items-center justify-center">
                <i class="bi {{ $typeConfig['icon'] }}
                          text-{{ $typeConfig['color'] }}-600 dark:text-{{ $typeConfig['color'] }}-400
                          text-base"></i>
            </div>

            {{-- Contenu --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100
                                  {{ $isUnread ? '' : 'font-medium' }} leading-snug">
                            {{ $data['title'] ?? 'Notification' }}
                            @if($isUnread)
                            <span class="inline-flex ml-1.5 w-1.5 h-1.5 rounded-full bg-blue-500
                                         align-middle mb-0.5"></span>
                            @endif
                        </p>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5 leading-relaxed">
                            {{ $data['message'] ?? '' }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1 shrink-0
                                opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        @if($isUnread)
                        <button class="btn-mark-one p-1.5 rounded-lg
                                       text-slate-400 hover:text-blue-600 dark:hover:text-blue-400
                                       hover:bg-blue-50 dark:hover:bg-blue-900/20
                                       transition-all duration-200" data-id="{{ $notification->id }}"
                            title="Marquer comme lu">
                            <i class="bi bi-check2 text-sm"></i>
                        </button>
                        @endif

                        <form method="POST" action="{{ route('notifications.index') }}/{{ $notification->id }}"
                            class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg
                                           text-slate-400 hover:text-red-600 dark:hover:text-red-400
                                           hover:bg-red-50 dark:hover:bg-red-900/20
                                           transition-all duration-200" title="Supprimer"
                                onclick="return confirm('Supprimer cette notification ?')">
                                <i class="bi bi-trash3 text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-2">
                    <span class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                        <i class="bi bi-clock text-[10px]"></i>
                        {{ $notification->created_at->diffForHumans() }}
                    </span>

                    @if($isUnread)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px]
                                 font-semibold bg-blue-100 dark:bg-blue-900/30
                                 text-blue-700 dark:text-blue-400">
                        Nouveau
                    </span>
                    @else
                    <span class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                        <i class="bi bi-check2 text-[10px]"></i>
                        Lu {{ $notification->read_at->diffForHumans() }}
                    </span>
                    @endif

                    @if(isset($data['url']))
                    <a href="{{ $data['url'] }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline
                              font-medium flex items-center gap-1">
                        Voir le détail
                        <i class="bi bi-arrow-right text-[10px]"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        @empty
        {{-- État vide --}}
        <div class="flex flex-col items-center justify-center py-16 text-center px-4">
            <div class="w-20 h-20 rounded-2xl bg-slate-100 dark:bg-slate-700/50
                        flex items-center justify-center mb-4">
                <i class="bi bi-bell-slash text-4xl text-slate-300 dark:text-slate-600"></i>
            </div>
            <h3 class="text-base font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Aucune notification
            </h3>
            <p class="text-sm text-slate-400 dark:text-slate-500 max-w-xs">
                @if(request('filter') === 'unread')
                Toutes vos notifications ont été lues. Bravo !
                @elseif(request('filter') === 'read')
                Vous n'avez pas encore de notifications lues.
                @else
                Vous n'avez aucune notification pour le moment.
                @endif
            </p>
        </div>
        @endforelse
    </div>

    {{-- ── Pagination ── --}}
    @if($notifications->hasPages())
    <div class="flex justify-center">
        {{ $notifications->onEachSide(1)->links('vendor.pagination.tailwind') }}
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
(() => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Marquer tout comme lu ──────────────────────────────────────
    document.getElementById('btn-mark-all')?.addEventListener('click', async function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i>';

        try {
            const res = await fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    _token: csrfToken
                }),
            });

            if (res.ok) {
                // Supprimer les indicateurs non-lus
                document.querySelectorAll('.notif-item').forEach(item => {
                    item.classList.remove(
                        'bg-blue-50/50', 'dark:bg-blue-950/20',
                        'hover:bg-blue-50', 'dark:hover:bg-blue-950/30'
                    );
                    item.classList.add(
                        'hover:bg-slate-50', 'dark:hover:bg-slate-700/30'
                    );
                    // Supprimer la barre bleue
                    item.querySelector('.absolute.left-0')?.remove();
                    // Supprimer le point bleu
                    item.querySelector('.bg-blue-500.rounded-full')?.remove();
                    // Supprimer le badge "Nouveau"
                    item.querySelector('.bg-blue-100')?.remove();
                    // Supprimer le bouton "Marquer comme lu"
                    item.querySelector('.btn-mark-one')?.remove();
                });

                // Masquer le bouton
                btn.closest('div')?.remove();

                // Mettre à jour le badge dans la navbar
                const badge = document.getElementById('notif-badge');
                if (badge) badge.remove();

                window.showToast({
                    type: 'success',
                    title: 'Notifications',
                    message: 'Toutes les notifications ont été marquées comme lues.',
                });
            }
        } catch {
            window.showToast({
                type: 'error',
                title: 'Erreur',
                message: 'Une erreur est survenue. Veuillez réessayer.',
            });
        } finally {
            btn.disabled = false;
        }
    });

    // ── Marquer une notification comme lue ────────────────────────
    document.querySelectorAll('.btn-mark-one').forEach(btn => {
        btn.addEventListener('click', async function() {
            const id = this.dataset.id;
            const item = this.closest('.notif-item');

            try {
                const res = await fetch(`/notifications/${id}/mark-read`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                if (res.ok) {
                    // Mettre à jour le style de l'item
                    item?.classList.remove(
                        'bg-blue-50/50', 'dark:bg-blue-950/20',
                        'hover:bg-blue-50', 'dark:hover:bg-blue-950/30'
                    );
                    item?.classList.add('hover:bg-slate-50', 'dark:hover:bg-slate-700/30');
                    item?.querySelector('.absolute.left-0')?.remove();
                    this.remove();

                    // Décrémenter le badge
                    const badge = document.getElementById('notif-badge');
                    if (badge) {
                        const count = parseInt(badge.textContent) - 1;
                        if (count <= 0) {
                            badge.remove();
                        } else {
                            badge.textContent = count > 9 ? '9+' : count;
                        }
                    }
                }
            } catch {
                window.showToast({
                    type: 'error',
                    title: 'Erreur',
                    message: 'Impossible de mettre à jour la notification.',
                });
            }
        });
    });

    // ── Supprimer les notifications lues ─────────────────────────
    document.getElementById('btn-delete-read')?.addEventListener('click', async function() {
        if (!confirm('Supprimer toutes les notifications lues ?')) return;

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

        try {
            const res = await fetch('/notifications/delete-read', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });

            if (res.ok) {
                // Supprimer les items lus du DOM
                document.querySelectorAll('.notif-item').forEach(item => {
                    const hasUnread = item.querySelector('.absolute.left-0');
                    if (!hasUnread) item.remove();
                });

                btn.remove();

                window.showToast({
                    type: 'success',
                    title: 'Nettoyage',
                    message: 'Notifications lues supprimées avec succès.',
                });
            }
        } catch {
            window.showToast({
                type: 'error',
                title: 'Erreur',
                message: 'Une erreur est survenue.',
            });
            btn.disabled = false;
        }
    });

    // ── Animation entrée des items ────────────────────────────────
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, idx) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, idx * 40);
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });

    document.querySelectorAll('.notif-item').forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(8px)';
        item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        observer.observe(item);
    });
})();
</script>
@endpush