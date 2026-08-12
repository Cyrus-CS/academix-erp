@extends('layouts.base')

@section('page_title', 'Parents')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Parents</span>
@endsection

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

    {{-- ════════════════════ HEADER ════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-5 bg-linear-to-r from-blue-50 to-emerald-50
                    dark:from-blue-950/30 dark:to-emerald-950/30
                    border-b border-slate-200 dark:border-slate-700
                    flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-linear-to-br from-blue-600 to-emerald-500
                            flex items-center justify-center shrink-0 shadow-md">
                    <i class="bi bi-people-fill text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                        Gestion des parents
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        {{ number_format($stats['total'], 0, ',', ' ') }} parent{{ $stats['total'] > 1 ? 's' : '' }}
                        enregistré{{ $stats['total'] > 1 ? 's' : '' }}
                    </p>
                </div>
            </div>

            <a href="{{ route('parents.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                      text-sm font-medium text-white bg-blue-600 hover:bg-blue-700
                      shadow-sm shadow-blue-600/20 transition-colors shrink-0">
                <i class="bi bi-plus-lg"></i>
                Ajouter un parent
            </a>
        </div>
    </div>

    {{-- ════════════════════ STATS ════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $statCards = [
        ['label' => 'Total parents', 'value' => $stats['total'], 'icon' => 'bi-people-fill', 'color' => 'blue'],
        ['label' => 'Avec élèves', 'value' => $stats['with_students'], 'icon' => 'bi-person-check-fill', 'color' =>
        'emerald'],
        ['label' => 'Sans élèves associés', 'value' => $stats['without_students'], 'icon' => 'bi-person-dash-fill',
        'color' => 'amber'],
        ['label' => 'Associations totales', 'value' => $stats['total_associations'],'icon' => 'bi-link-45deg', 'color'
        => 'cyan'],
        ];
        @endphp

        @foreach($statCards as $card)
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                    border-slate-200 dark:border-slate-700 p-5">
            <div class="w-10 h-10 rounded-xl bg-{{ $card['color'] }}-50 dark:bg-{{ $card['color'] }}-900/30
                        flex items-center justify-center mb-3">
                <i class="bi {{ $card['icon'] }} text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"></i>
            </div>
            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                {{ number_format($card['value'], 0, ',', ' ') }}
            </p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ════════════════════ FILTRES ════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                border-slate-200 dark:border-slate-700 px-5 py-4">
        <form action="{{ route('parents.index') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">

            <div class="flex-1 w-full">
                <label class="flex items-center gap-1.5 text-xs font-medium
                              text-slate-600 dark:text-slate-400 mb-1.5">
                    <i class="bi bi-search text-slate-400"></i>
                    Rechercher
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                        <i class="bi bi-search text-slate-400 text-sm"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nom, email, téléphone..." class="w-full pl-9 pr-4 py-2.5 rounded-lg border text-sm
                                  text-slate-800 dark:text-slate-100
                                  bg-white dark:bg-slate-900
                                  border-slate-300 dark:border-slate-600
                                  placeholder:text-slate-400
                                  focus:outline-none focus:ring-2 focus:ring-blue-600/40
                                  focus:border-blue-600 transition" />
                </div>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2
                           px-4 py-2.5 rounded-lg text-sm font-medium
                           text-white bg-slate-700 hover:bg-slate-800
                           dark:bg-slate-600 dark:hover:bg-slate-500 transition-colors">
                    <i class="bi bi-funnel-fill"></i>
                    Filtrer
                </button>
                @if(request('search'))
                <a href="{{ route('parents.index') }}" class="inline-flex items-center justify-center px-3.5 py-2.5 rounded-lg text-sm
                          text-slate-500 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Réinitialiser">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ════════════════════ TABLE ════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                border-slate-200 dark:border-slate-700 overflow-hidden">

        {{-- En-tête table --}}
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700
                    flex items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <i class="bi bi-table text-blue-600 dark:text-blue-400 text-sm"></i>
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                    Liste des parents
                </h2>
                @if(request('search'))
                <span class="text-xs px-2 py-0.5 rounded-full
                             bg-blue-100 dark:bg-blue-900/40
                             text-blue-600 dark:text-blue-400">
                    Résultats filtrés
                </span>
                @endif
            </div>
            <span class="text-xs text-slate-400 dark:text-slate-500">
                {{ $parents->total() }} résultat{{ $parents->total() > 1 ? 's' : '' }}
            </span>
        </div>

        {{-- Table desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50
                               border-b border-slate-200 dark:border-slate-700">
                        <th class="text-left px-6 py-3 text-xs font-semibold
                                   text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Parent
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold
                                   text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Contact
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold
                                   text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Élèves associés
                        </th>
                        <th class="text-center px-4 py-3 text-xs font-semibold
                                   text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="text-right px-6 py-3 text-xs font-semibold
                                   text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @forelse($parents as $parent)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors group">

                        {{-- Avatar + Nom --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($parent->avatar)
                                <img src="{{ asset('storage/' . $parent->avatar) }}" alt="{{ $parent->name }}" class="w-9 h-9 rounded-xl object-cover shrink-0
                                            ring-2 ring-blue-500/10" />
                                @else
                                <div class="w-9 h-9 rounded-xl bg-linear-to-br from-blue-500 to-emerald-500
                                            flex items-center justify-center text-white text-sm font-bold
                                            shrink-0 ring-2 ring-blue-500/10">
                                    {{ strtoupper(substr($parent->name, 0, 1)) }}
                                </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">
                                        {{ $parent->name }}
                                    </p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 truncate">
                                        Inscrit le {{ $parent->created_at->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Contact --}}
                        <td class="px-4 py-4">
                            <div class="space-y-1">
                                <p class="text-xs text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
                                    <i class="bi bi-envelope text-slate-400 text-[11px]"></i>
                                    <span class="truncate max-w-45">{{ $parent->email }}</span>
                                </p>
                                @if($parent->phone)
                                <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                    <i class="bi bi-telephone text-slate-400 text-[11px]"></i>
                                    {{ $parent->phone }}
                                </p>
                                @endif
                            </div>
                        </td>

                        {{-- Élèves --}}
                        <td class="px-4 py-4">
                            @if($parent->students_count > 0)
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($parent->students->take(3) as $student)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs
                                             bg-blue-50 dark:bg-blue-900/30
                                             text-blue-700 dark:text-blue-300
                                             border border-blue-100 dark:border-blue-800">
                                    <i class="bi bi-mortarboard text-[10px]"></i>
                                    {{ $student->user?->name ?? '—' }}
                                </span>
                                @endforeach
                                @if($parent->students_count > 3)
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs
                                             bg-slate-100 dark:bg-slate-700
                                             text-slate-500 dark:text-slate-400">
                                    +{{ $parent->students_count - 3 }}
                                </span>
                                @endif
                            </div>
                            @else
                            <span class="text-xs text-slate-400 dark:text-slate-500 italic">
                                Aucun élève associé
                            </span>
                            @endif
                        </td>

                        {{-- Statut --}}
                        <td class="px-4 py-4 text-center">
                            @if($parent->students_count > 0)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                         bg-emerald-100 dark:bg-emerald-900/30
                                         text-emerald-700 dark:text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Actif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                         bg-amber-100 dark:bg-amber-900/30
                                         text-amber-700 dark:text-amber-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Non lié
                            </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1.5
                                        opacity-60 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('parents.show', $parent) }}" class="p-2 rounded-lg text-slate-500 dark:text-slate-400
                                          hover:bg-blue-50 dark:hover:bg-blue-900/30
                                          hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                    title="Voir le détail">
                                    <i class="bi bi-eye text-sm"></i>
                                </a>
                                <a href="{{ route('parents.edit', $parent) }}" class="p-2 rounded-lg text-slate-500 dark:text-slate-400
                                          hover:bg-emerald-50 dark:hover:bg-emerald-900/30
                                          hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors"
                                    title="Modifier">
                                    <i class="bi bi-pencil text-sm"></i>
                                </a>
                                <button type="button" data-open-modal="delete-modal" data-parent-id="{{ $parent->id }}"
                                    data-parent-name="{{ $parent->name }}" class="p-2 rounded-lg text-slate-500 dark:text-slate-400
                                               hover:bg-red-50 dark:hover:bg-red-900/30
                                               hover:text-red-600 dark:hover:text-red-400 transition-colors"
                                    title="Supprimer">
                                    <i class="bi bi-trash3 text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-slate-400 dark:text-slate-500">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700/50
                                            flex items-center justify-center">
                                    <i class="bi bi-people text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Aucun parent trouvé</p>
                                    <p class="text-xs mt-0.5">
                                        @if(request('search'))
                                        Aucun résultat pour "{{ request('search') }}"
                                        @else
                                        Commencez par ajouter un parent
                                        @endif
                                    </p>
                                </div>
                                @unless(request('search'))
                                <a href="{{ route('parents.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm
                                          font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors mt-1">
                                    <i class="bi bi-plus-lg"></i>
                                    Ajouter un parent
                                </a>
                                @endunless
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Cards mobile --}}
        <div class="md:hidden divide-y divide-slate-100 dark:divide-slate-700">
            @forelse($parents as $parent)
            <div class="p-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        @if($parent->avatar)
                        <img src="{{ asset('storage/' . $parent->avatar) }}" alt="{{ $parent->name }}"
                            class="w-10 h-10 rounded-xl object-cover shrink-0" />
                        @else
                        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-500 to-emerald-500
                                    flex items-center justify-center text-white font-bold shrink-0">
                            {{ strtoupper(substr($parent->name, 0, 1)) }}
                        </div>
                        @endif
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">
                                {{ $parent->name }}
                            </p>
                            <p class="text-xs text-slate-400 truncate">{{ $parent->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <a href="{{ route('parents.edit', $parent) }}" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors
                                  text-slate-500 dark:text-slate-400">
                            <i class="bi bi-pencil text-sm"></i>
                        </a>
                        <button type="button" data-open-modal="delete-modal" data-parent-id="{{ $parent->id }}"
                            data-parent-name="{{ $parent->name }}" class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors
                                       text-slate-500 hover:text-red-600 dark:hover:text-red-400">
                            <i class="bi bi-trash3 text-sm"></i>
                        </button>
                    </div>
                </div>

                @if($parent->students_count > 0)
                <div class="flex flex-wrap gap-1.5">
                    @foreach($parent->students->take(2) as $student)
                    <span class="text-xs px-2 py-1 rounded-lg
                                 bg-blue-50 dark:bg-blue-900/30
                                 text-blue-700 dark:text-blue-300">
                        {{ $student->user?->name }}
                    </span>
                    @endforeach
                    @if($parent->students_count > 2)
                    <span class="text-xs px-2 py-1 rounded-lg
                                 bg-slate-100 dark:bg-slate-700
                                 text-slate-500 dark:text-slate-400">
                        +{{ $parent->students_count - 2 }}
                    </span>
                    @endif
                </div>
                @else
                <p class="text-xs text-amber-600 dark:text-amber-400 italic">
                    <i class="bi bi-exclamation-circle mr-1"></i>Aucun élève associé
                </p>
                @endif
            </div>
            @empty
            <div class="py-12 text-center text-slate-400 dark:text-slate-500">
                <i class="bi bi-people text-3xl"></i>
                <p class="text-sm mt-2">Aucun parent trouvé</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($parents->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
            {{ $parents->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ════════════════════ MODAL SUPPRESSION ════════════════════ --}}
<div id="delete-modal" class="hidden fixed inset-0 z-100 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" data-close-modal="delete-modal"></div>

    <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border
                border-slate-200 dark:border-slate-700 w-full max-w-md overflow-hidden">
        <div class="p-6">
            <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-900/30
                        flex items-center justify-center mb-4">
                <i class="bi bi-exclamation-triangle-fill text-red-600 dark:text-red-400 text-xl"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-1.5">
                Supprimer ce parent ?
            </h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Le compte de <strong id="modal-parent-name" class="text-slate-700 dark:text-slate-200"></strong>
                sera définitivement supprimé, ainsi que toutes ses associations avec des élèves.
            </p>
        </div>
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t
                    border-slate-200 dark:border-slate-700 flex justify-end gap-3">
            <button type="button" data-close-modal="delete-modal" class="px-4 py-2.5 rounded-xl text-sm font-medium
                           text-slate-600 dark:text-slate-300
                           bg-white dark:bg-slate-700
                           border border-slate-200 dark:border-slate-600
                           hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                Annuler
            </button>
            <form id="delete-form" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2.5 rounded-xl text-sm font-medium text-white
                               bg-red-600 hover:bg-red-700
                               shadow-sm shadow-red-600/20 transition-colors">
                    <i class="bi bi-trash3-fill mr-1.5"></i>
                    Supprimer
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Modal suppression ─────────────────────────────────────────
    const modal = document.getElementById('delete-modal');
    const form = document.getElementById('delete-form');
    const nameEl = document.getElementById('modal-parent-name');

    document.querySelectorAll('[data-open-modal="delete-modal"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const parentId = btn.dataset.parentId;
            const parentName = btn.dataset.parentName;

            if (nameEl) nameEl.textContent = parentName;
            if (form) form.action = '/parents/' + parentId;

            modal?.classList.remove('hidden');
        });
    });

    document.querySelectorAll('[data-close-modal="delete-modal"]').forEach(function(el) {
        el.addEventListener('click', function() {
            modal?.classList.add('hidden');
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') modal?.classList.add('hidden');
    });
});
</script>
@endpush