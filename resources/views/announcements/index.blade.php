@extends('layouts.base')

@section('title', 'Annonces')
@section('page_title', 'Annonces')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600">/</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Annonces</span>
@endsection

@section('content')
<div class="space-y-5">

    {{-- ══════════════════════════════════════════════════════════
         EN-TÊTE
    ══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                Annonces
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                <span class="font-semibold text-blue-600 dark:text-blue-400">
                    {{ $total }}
                </span>
                annonce{{ $total > 1 ? 's' : '' }} au total ·
                <span class="text-emerald-600 dark:text-emerald-400 font-medium">
                    {{ $active }} active{{ $active > 1 ? 's' : '' }}
                </span>
                @if($expired > 0)
                ·
                <span class="text-slate-400 dark:text-slate-500">
                    {{ $expired }} expirée{{ $expired > 1 ? 's' : '' }}
                </span>
                @endif
            </p>
        </div>

        <a href="{{ route('announcements.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                  bg-blue-600 hover:bg-blue-700 text-white self-start sm:self-auto
                  transition-all shadow-sm shadow-blue-500/30">
            <i class="bi bi-megaphone-fill"></i>
            <span class="hidden sm:inline">Nouvelle annonce</span>
            <span class="sm:hidden">Nouveau</span>
        </a>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         STATS RAPIDES
    ══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        @foreach([
        [
        'label' => 'Total annonces',
        'value' => $total,
        'icon' => 'bi-megaphone-fill',
        'color' => 'blue',
        'desc' => 'Toutes périodes',
        ],
        [
        'label' => 'Annonces actives',
        'value' => $active,
        'icon' => 'bi-broadcast',
        'color' => 'emerald',
        'desc' => 'En cours de diffusion',
        ],
        [
        'label' => 'Expirées',
        'value' => $expired,
        'icon' => 'bi-calendar-x-fill',
        'color' => 'slate',
        'desc' => 'Archivées',
        ],
        ] as $card)
        <div class="bg-white dark:bg-slate-800 rounded-2xl px-5 py-4
                    border border-slate-200 dark:border-slate-700 shadow-sm
                    flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex-shrink-0
                        bg-{{ $card['color'] }}-100 dark:bg-{{ $card['color'] }}-900/30
                        flex items-center justify-center">
                <i class="bi {{ $card['icon'] }}
                          text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400
                          text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 leading-tight">
                    {{ $card['value'] }}
                </p>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    {{ $card['label'] }}
                </p>
                <p class="text-xs text-slate-400 dark:text-slate-500">
                    {{ $card['desc'] }}
                </p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════════
         FILTRES
    ══════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl
                border border-slate-200 dark:border-slate-700 shadow-sm">
        <form method="GET" action="{{ route('announcements.index') }}" id="filter-form"
            class="flex flex-col sm:flex-row gap-3 items-end p-4">

            {{-- Recherche --}}
            <div class="flex-1 min-w-0">
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">
                    Rechercher
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="bi bi-search text-slate-400 text-sm"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Titre, contenu…"
                        class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl
                                  border border-slate-200 dark:border-slate-700
                                  bg-slate-50 dark:bg-slate-900/50
                                  text-slate-800 dark:text-slate-100
                                  placeholder:text-slate-400
                                  focus:outline-none focus:ring-2
                                  focus:ring-blue-600/40 focus:border-blue-600
                                  transition" />
                </div>
            </div>

            {{-- Audience --}}
            <div class="w-full sm:w-44">
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">
                    Audience
                </label>
                <div class="relative">
                    <select name="audience" onchange="document.getElementById('filter-form').submit()" class="w-full pl-3.5 pr-9 py-2.5 text-sm rounded-xl appearance-none
                                   border border-slate-200 dark:border-slate-700
                                   bg-slate-50 dark:bg-slate-900/50
                                   text-slate-800 dark:text-slate-100
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-600/40 focus:border-blue-600 transition">
                        <option value="">Toutes audiences</option>
                        @foreach($audiences as $key => $label)
                        <option value="{{ $key }}" {{ request('audience') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                    </span>
                </div>
            </div>

            {{-- Statut --}}
            <div class="w-full sm:w-36">
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">
                    Statut
                </label>
                <div class="relative">
                    <select name="status" onchange="document.getElementById('filter-form').submit()" class="w-full pl-3.5 pr-9 py-2.5 text-sm rounded-xl appearance-none
                                   border border-slate-200 dark:border-slate-700
                                   bg-slate-50 dark:bg-slate-900/50
                                   text-slate-800 dark:text-slate-100
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-600/40 focus:border-blue-600 transition">
                        <option value="">Tous</option>
                        <option value="active" {{ request('status') === 'active'  ? 'selected' : '' }}>
                            Actives
                        </option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>
                            Expirées
                        </option>
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                    </span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                               bg-blue-600 hover:bg-blue-700 text-white
                               transition-all shadow-sm shadow-blue-500/20">
                    <i class="bi bi-funnel-fill"></i>
                    Filtrer
                </button>
                @if(request()->hasAny(['search', 'audience', 'status']))
                <a href="{{ route('announcements.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                          border border-slate-200 dark:border-slate-700
                          text-slate-600 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                    <i class="bi bi-x-circle"></i>
                    Réinitialiser
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         LISTE DES ANNONCES
    ══════════════════════════════════════════════════════════ --}}
    @if($announcements->isEmpty())
    <div class="bg-white dark:bg-slate-800 rounded-2xl
                border border-slate-200 dark:border-slate-700
                shadow-sm flex flex-col items-center justify-center py-20 px-4">
        <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700
                    flex items-center justify-center mb-4">
            <i class="bi bi-megaphone text-3xl text-slate-300 dark:text-slate-500"></i>
        </div>
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
            Aucune annonce
        </h3>
        <p class="text-xs text-slate-400 dark:text-slate-500 text-center mb-5">
            @if(request()->hasAny(['search', 'audience', 'status']))
            Aucun résultat pour ces filtres.
            @else
            Publiez votre première annonce.
            @endif
        </p>
        @if(request()->hasAny(['search', 'audience', 'status']))
        <a href="{{ route('announcements.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium
                  border border-slate-200 dark:border-slate-700
                  text-slate-600 dark:text-slate-400
                  hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
            <i class="bi bi-arrow-counterclockwise"></i>
            Réinitialiser
        </a>
        @else
        <a href="{{ route('announcements.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                  bg-blue-600 hover:bg-blue-700 text-white
                  transition-all shadow-sm shadow-blue-500/30">
            <i class="bi bi-megaphone-fill"></i>
            Créer une annonce
        </a>
        @endif
    </div>

    @else

    {{-- Grille d'annonces (SortableJS) --}}
    <div id="announcements-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($announcements as $announcement)
        @php
        $isExpired = $announcement->expires_at && now()->isAfter($announcement->expires_at);
        $isActive = !$isExpired;

        $audienceConfig = [
        'all' => ['label' => 'Tous', 'color' => 'blue', 'icon' => 'bi-globe'],
        'teachers' => ['label' => 'Enseignants', 'color' => 'emerald','icon' => 'bi-person-badge-fill'],
        'students' => ['label' => 'Élèves', 'color' => 'violet', 'icon' => 'bi-people-fill'],
        'parents' => ['label' => 'Parents', 'color' => 'amber', 'icon' => 'bi-person-heart'],
        ];
        $audienceCfg = $audienceConfig[$announcement->audience] ?? $audienceConfig['all'];

        $priorityConfig = [
        'high' => ['label' => 'Urgent', 'color' => 'red', 'icon' => 'bi-exclamation-triangle-fill'],
        'normal' => ['label' => 'Normal', 'color' => 'blue', 'icon' => 'bi-info-circle-fill'],
        'low' => ['label' => 'Info', 'color' => 'slate', 'icon' => 'bi-bell-fill'],
        ];
        $priorityCfg = $priorityConfig[$announcement->priority ?? 'normal'] ?? $priorityConfig['normal'];
        @endphp

        <div class="announcement-card group bg-white dark:bg-slate-800 rounded-2xl
                    border shadow-sm overflow-hidden
                    hover:shadow-md transition-all duration-200
                    cursor-grab active:cursor-grabbing
                    {{ $isExpired
                        ? 'border-slate-200 dark:border-slate-700 opacity-70'
                        : 'border-slate-200 dark:border-slate-700 hover:border-blue-200 dark:hover:border-blue-800' }}"
            data-id="{{ $announcement->id }}">

            {{-- Bande de couleur priorité --}}
            <div class="h-1 w-full
                        bg-{{ $priorityCfg['color'] }}-500
                        {{ $isExpired ? 'opacity-40' : '' }}">
            </div>

            <div class="p-5">

                {{-- En-tête : badges + menu --}}
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex flex-wrap items-center gap-1.5">

                        {{-- Priorité --}}
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                     text-[10px] font-semibold
                                     bg-{{ $priorityCfg['color'] }}-100 dark:bg-{{ $priorityCfg['color'] }}-900/30
                                     text-{{ $priorityCfg['color'] }}-700 dark:text-{{ $priorityCfg['color'] }}-400">
                            <i class="bi {{ $priorityCfg['icon'] }}"></i>
                            {{ $priorityCfg['label'] }}
                        </span>

                        {{-- Audience --}}
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                     text-[10px] font-semibold
                                     bg-{{ $audienceCfg['color'] }}-100 dark:bg-{{ $audienceCfg['color'] }}-900/30
                                     text-{{ $audienceCfg['color'] }}-700 dark:text-{{ $audienceCfg['color'] }}-400">
                            <i class="bi {{ $audienceCfg['icon'] }}"></i>
                            {{ $audienceCfg['label'] }}
                        </span>

                        {{-- Statut --}}
                        @if($isExpired)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                     text-[10px] font-semibold
                                     bg-slate-100 dark:bg-slate-700
                                     text-slate-500 dark:text-slate-400">
                            <i class="bi bi-clock-history"></i>
                            Expirée
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                     text-[10px] font-semibold
                                     bg-emerald-100 dark:bg-emerald-900/30
                                     text-emerald-700 dark:text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Active
                        </span>
                        @endif
                    </div>

                    {{-- Menu actions --}}
                    <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button data-dropdown="ann-actions-{{ $announcement->id }}" class="w-7 h-7 rounded-lg flex items-center justify-center
                                       bg-slate-100 dark:bg-slate-700
                                       text-slate-500 dark:text-slate-400
                                       hover:bg-slate-200 dark:hover:bg-slate-600
                                       transition-colors focus:outline-none">
                            <i class="bi bi-three-dots-vertical text-sm"></i>
                        </button>
                        <div id="ann-actions-{{ $announcement->id }}" data-dropdown-menu class="hidden absolute right-0 w-44 mt-1
                                    bg-white dark:bg-slate-800
                                    border border-slate-200 dark:border-slate-700
                                    rounded-xl shadow-lg overflow-hidden z-20
                                    opacity-0 scale-95 translate-y-1
                                    transition-all duration-150">
                            <a href="{{ route('announcements.show', $announcement) }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs
                                      text-slate-700 dark:text-slate-300
                                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                                      hover:text-blue-600 transition-colors">
                                <i class="bi bi-eye-fill w-4 text-center"></i>
                                Voir le détail
                            </a>
                            <a href="{{ route('announcements.edit', $announcement) }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs
                                      text-slate-700 dark:text-slate-300
                                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                                      hover:text-blue-600 transition-colors">
                                <i class="bi bi-pencil-fill w-4 text-center"></i>
                                Modifier
                            </a>
                            @if($isExpired)
                            <form method="POST" action="{{ route('announcements.renew', $announcement) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="flex items-center gap-2.5 w-full px-3 py-2.5 text-xs
                                               text-emerald-600 dark:text-emerald-400
                                               hover:bg-emerald-50 dark:hover:bg-emerald-900/20
                                               transition-colors focus:outline-none">
                                    <i class="bi bi-arrow-clockwise w-4 text-center"></i>
                                    Renouveler
                                </button>
                            </form>
                            @endif
                            <div class="border-t border-slate-100 dark:border-slate-700 mt-1 pt-1">
                                <button
                                    onclick="deleteAnnouncement({{ $announcement->id }}, '{{ addslashes($announcement->title) }}')"
                                    class="flex items-center gap-2.5 w-full px-3 py-2.5 text-xs
                                               text-red-600 dark:text-red-400
                                               hover:bg-red-50 dark:hover:bg-red-900/20
                                               transition-colors focus:outline-none">
                                    <i class="bi bi-trash3-fill w-4 text-center"></i>
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Titre --}}
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100
                           leading-snug mb-2 line-clamp-2
                           group-hover:text-blue-600 dark:group-hover:text-blue-400
                           transition-colors">
                    {{ $announcement->title }}
                </h3>

                {{-- Contenu --}}
                <p class="text-xs text-slate-500 dark:text-slate-400
                          leading-relaxed line-clamp-3 mb-4">
                    {{ strip_tags($announcement->content) }}
                </p>

                {{-- Footer carte --}}
                <div class="flex items-center justify-between pt-3
                            border-t border-slate-100 dark:border-slate-700">

                    {{-- Auteur --}}
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-6 h-6 rounded-full bg-gradient-to-br
                                    from-blue-500 to-indigo-600
                                    flex items-center justify-center flex-shrink-0">
                            <span class="text-[9px] font-bold text-white">
                                {{ strtoupper(substr($announcement->user->name ?? 'A', 0, 1)) }}
                            </span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold text-slate-600 dark:text-slate-300 truncate">
                                {{ $announcement->user->name ?? '—' }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                {{ $announcement->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    {{-- Expiration --}}
                    <div class="flex-shrink-0 text-right">
                        @if($announcement->expires_at)
                        <p class="text-[10px] font-medium
                                  {{ $isExpired
                                    ? 'text-slate-400 dark:text-slate-500'
                                    : 'text-amber-600 dark:text-amber-400' }}">
                            <i class="bi bi-clock mr-0.5"></i>
                            @if($isExpired)
                            Exp. {{ $announcement->expires_at->format('d/m/Y') }}
                            @else
                            Expire {{ $announcement->expires_at->diffForHumans() }}
                            @endif
                        </p>
                        @else
                        <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium">
                            <i class="bi bi-infinity mr-0.5"></i>
                            Sans expiration
                        </p>
                        @endif
                    </div>
                </div>

                {{-- Bouton voir --}}
                <a href="{{ route('announcements.show', $announcement) }}" class="mt-3 flex items-center justify-center gap-1.5 w-full
                          py-2 rounded-xl text-xs font-medium
                          bg-slate-50 dark:bg-slate-700/50
                          text-slate-600 dark:text-slate-300
                          hover:bg-blue-50 dark:hover:bg-blue-900/20
                          hover:text-blue-600 dark:hover:text-blue-400
                          border border-slate-200 dark:border-slate-700
                          transition-all">
                    <i class="bi bi-eye-fill"></i>
                    Lire l'annonce
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════════
         PAGINATION
    ══════════════════════════════════════════════════════════ --}}
    @if($announcements->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4
                bg-white dark:bg-slate-800 rounded-2xl
                border border-slate-200 dark:border-slate-700
                shadow-sm px-5 py-3.5">
        <p class="text-xs text-slate-500 dark:text-slate-400 order-2 sm:order-1">
            Affichage de
            <span class="font-semibold text-slate-700 dark:text-slate-300">
                {{ $announcements->firstItem() }}
            </span>
            à
            <span class="font-semibold text-slate-700 dark:text-slate-300">
                {{ $announcements->lastItem() }}
            </span>
            sur
            <span class="font-semibold text-slate-700 dark:text-slate-300">
                {{ $announcements->total() }}
            </span>
            annonces
        </p>
        <div class="flex items-center gap-1 order-1 sm:order-2">
            @if(!$announcements->onFirstPage())
            <a href="{{ $announcements->previousPageUrl() }}" class="px-3 py-1.5 rounded-xl text-xs
                      border border-slate-200 dark:border-slate-700
                      text-slate-600 dark:text-slate-400
                      hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <i class="bi bi-chevron-left"></i>
            </a>
            @endif
            @foreach($announcements->getUrlRange(
            max(1, $announcements->currentPage() - 2),
            min($announcements->lastPage(), $announcements->currentPage() + 2)
            ) as $page => $url)
            @if($page == $announcements->currentPage())
            <span class="px-3 py-1.5 rounded-xl text-xs font-semibold
                             bg-blue-600 text-white shadow-sm">
                {{ $page }}
            </span>
            @else
            <a href="{{ $url }}" class="px-3 py-1.5 rounded-xl text-xs
                          border border-slate-200 dark:border-slate-700
                          text-slate-600 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                {{ $page }}
            </a>
            @endif
            @endforeach
            @if($announcements->hasMorePages())
            <a href="{{ $announcements->nextPageUrl() }}" class="px-3 py-1.5 rounded-xl text-xs
                      border border-slate-200 dark:border-slate-700
                      text-slate-600 dark:text-slate-400
                      hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <i class="bi bi-chevron-right"></i>
            </a>
            @endif
        </div>
    </div>
    @endif

    @endif

</div>

{{-- Formulaire suppression --}}
<form id="delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── SortableJS ─────────────────────────────────────────────
    const grid = document.getElementById('announcements-grid');
    if (grid && typeof Sortable !== 'undefined') {
        Sortable.create(grid, {
            animation: 200,
            ghostClass: 'opacity-40',
            chosenClass: 'ring-2 ring-blue-400 shadow-xl scale-[1.01]',
            dragClass: 'shadow-2xl rotate-1',
            delay: 80,
            delayOnTouchOnly: true,

            onEnd(evt) {
                const order = [...grid.querySelectorAll('[data-id]')]
                    .map(el => el.dataset.id);

                fetch('{{ route("announcements.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .content,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        order
                    }),
                }).catch(() => {});

                window.showToast({
                    type: 'info',
                    title: 'Ordre mis à jour',
                    message: 'Les annonces ont été réorganisées.',
                    delay: 2500,
                });
            }
        });
    }
});

// ── Suppression ────────────────────────────────────────────────
function deleteAnnouncement(id, title) {
    if (!confirm(`Supprimer l'annonce "${title}" ? Cette action est irréversible.`)) return;
    const form = document.getElementById('delete-form');
    form.action = `/announcements/${id}`;
    form.submit();
}
</script>
@endpush