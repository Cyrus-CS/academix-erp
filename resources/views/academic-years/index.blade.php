@extends('layouts.base')

@section('title', 'Années académiques')
@section('page_title', 'Années académiques')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600">/</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Années académiques</span>
@endsection

@section('content')
<div class="space-y-5">

    {{-- ══════════════════════════════════════════════════════════
         EN-TÊTE
    ══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                Années académiques
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                <span class="font-semibold text-blue-600 dark:text-blue-400">
                    {{ $totalYears }}
                </span>
                année{{ $totalYears > 1 ? 's' : '' }} configurée{{ $totalYears > 1 ? 's' : '' }}
                @if($activeYear)
                · Année active :
                <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                    {{ $activeYear->name }}
                </span>
                @endif
            </p>
        </div>

        <a href="{{ route('academic-years.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                  bg-blue-600 hover:bg-blue-700 text-white self-start sm:self-auto
                  transition-all duration-200 shadow-sm shadow-blue-500/30">
            <i class="bi bi-plus-lg"></i>
            Nouvelle année
        </a>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         STATS RAPIDES
    ══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        @foreach([
        [
        'label' => 'Années configurées',
        'value' => $totalYears,
        'icon' => 'bi-calendar3',
        'color' => 'blue',
        'desc' => 'Au total',
        ],
        [
        'label' => 'Trimestres',
        'value' => $totalTerms,
        'icon' => 'bi-calendar-range',
        'color' => 'violet',
        'desc' => 'Toutes années',
        ],
        [
        'label' => 'Classes actives',
        'value' => $totalClasses,
        'icon' => 'bi-building',
        'color' => 'emerald',
        'desc' => 'Cette année',
        ],
        ] as $card)
        <div class="bg-white dark:bg-slate-800 rounded-2xl px-5 py-4
                    border border-slate-200 dark:border-slate-700 shadow-sm
                    flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl shrink-0
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
         ANNÉE ACTIVE — MISE EN AVANT
    ══════════════════════════════════════════════════════════ --}}
    @if($activeYear)
    <div class="relative bg-gradient-to-br from-blue-600 to-indigo-700
                dark:from-blue-700 dark:to-indigo-800
                rounded-2xl overflow-hidden shadow-lg shadow-blue-500/20">

        {{-- Décorations --}}
        <div class="absolute top-0 right-0 w-64 h-64 opacity-10">
            <i class="bi bi-calendar-check text-[180px] text-white leading-none
                      absolute -top-4 -right-4"></i>
        </div>

        <div class="relative px-6 py-5 flex flex-col sm:flex-row
                    sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm
                            flex items-center justify-center shrink-0">
                    <i class="bi bi-mortarboard-fill text-white text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full
                                     text-xs font-semibold bg-white/20 text-white">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Année en cours
                        </span>
                    </div>
                    <h2 class="text-2xl font-bold text-white">
                        {{ $activeYear->name }}
                    </h2>
                    <p class="text-blue-200 text-sm mt-0.5">
                        Du {{ $activeYear->start_date->format('d/m/Y') }}
                        au {{ $activeYear->end_date->format('d/m/Y') }}
                        ·
                        @php
                        $daysLeft = now()->diffInDays($activeYear->end_date, false);
                        @endphp
                        @if($daysLeft > 0)
                        <span class="text-white font-semibold">
                            {{ $daysLeft }} jours restants
                        </span>
                        @else
                        <span class="text-red-300 font-semibold">Terminée</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Progression --}}
            <div class="sm:w-56 shrink-0">
                @php
                $total = $activeYear->start_date->diffInDays($activeYear->end_date);
                $elapsed = $activeYear->start_date->diffInDays(now());
                $progress = $total > 0 ? min(100, round(($elapsed / $total) * 100)) : 0;
                @endphp
                <div class="flex items-center justify-between text-xs text-blue-200 mb-1.5">
                    <span>Progression</span>
                    <span class="font-bold text-white">{{ $progress }}%</span>
                </div>
                <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full bg-white rounded-full transition-all duration-1000"
                        style="width: {{ $progress }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] text-blue-300 mt-1.5">
                    <span>{{ $activeYear->start_date->format('M Y') }}</span>
                    <span>{{ $activeYear->end_date->format('M Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Trimestres de l'année active --}}
        @if($activeYear->terms->isNotEmpty())
        <div class="px-6 pb-5">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach($activeYear->terms->sortBy('start_date') as $term)
                @php
                $isCurrentTerm = now()->between($term->start_date, $term->end_date);
                $isPast = now()->isAfter($term->end_date);
                @endphp
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3
                            border border-white/20
                            {{ $isCurrentTerm ? 'ring-2 ring-white/50' : '' }}">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-semibold text-white">
                            {{ $term->name }}
                        </span>
                        @if($isCurrentTerm)
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full
                                     bg-emerald-400/20 text-emerald-300 font-medium">
                            En cours
                        </span>
                        @elseif($isPast)
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full
                                     bg-white/10 text-blue-200 font-medium">
                            Terminé
                        </span>
                        @else
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full
                                     bg-white/10 text-blue-200 font-medium">
                            À venir
                        </span>
                        @endif
                    </div>
                    <p class="text-[11px] text-blue-200">
                        {{ $term->start_date->format('d/m') }}
                        →
                        {{ $term->end_date->format('d/m/Y') }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         LISTE DES ANNÉES (SortableJS)
    ══════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl
                border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

        <div class="flex items-center justify-between px-5 py-4
                    border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                Toutes les années
            </h3>
            <span class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                <i class="bi bi-grip-vertical"></i>
                Glissez pour réorganiser
            </span>
        </div>

        @if($academicYears->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 px-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700
                        flex items-center justify-center mb-4">
                <i class="bi bi-calendar-x text-3xl text-slate-300 dark:text-slate-500"></i>
            </div>
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                Aucune année configurée
            </h3>
            <p class="text-xs text-slate-400 dark:text-slate-500 text-center mb-5">
                Commencez par créer votre première année académique.
            </p>
            <a href="{{ route('academic-years.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                      bg-blue-600 hover:bg-blue-700 text-white
                      transition-all shadow-sm shadow-blue-500/30">
                <i class="bi bi-plus-lg"></i>
                Créer une année
            </a>
        </div>
        @else
        <ul id="academic-years-list" class="divide-y divide-slate-100 dark:divide-slate-700">
            @foreach($academicYears as $year)
            @php
            $isActive = $year->id === $activeYear?->id;
            $isPast = now()->isAfter($year->end_date);
            $isFuture = now()->isBefore($year->start_date);
            $progress = 0;
            if (!$isFuture) {
            $total = $year->start_date->diffInDays($year->end_date);
            $elapsed = $year->start_date->diffInDays(now());
            $progress = $total > 0 ? min(100, round(($elapsed / $total) * 100)) : 0;
            }
            @endphp
            <li class="year-item group flex flex-col sm:flex-row sm:items-center gap-4
                       px-5 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/50
                       transition-colors cursor-grab active:cursor-grabbing" data-id="{{ $year->id }}">

                {{-- Drag handle --}}
                <div class="hidden sm:flex items-center shrink-0
                            text-slate-300 dark:text-slate-600
                            group-hover:text-slate-400 dark:group-hover:text-slate-500
                            transition-colors">
                    <i class="bi bi-grip-vertical text-lg"></i>
                </div>

                {{-- Icône + Statut --}}
                <div class="shrink-0">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center
                                {{ $isActive
                                    ? 'bg-blue-600 shadow-md shadow-blue-500/30'
                                    : ($isPast
                                        ? 'bg-slate-100 dark:bg-slate-700'
                                        : 'bg-amber-100 dark:bg-amber-900/30') }}">
                        <i class="bi bi-calendar-check-fill text-xl
                                  {{ $isActive
                                    ? 'text-white'
                                    : ($isPast
                                        ? 'text-slate-400 dark:text-slate-500'
                                        : 'text-amber-600 dark:text-amber-400') }}"></i>
                    </div>
                </div>

                {{-- Infos principales --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                            {{ $year->name }}
                        </h4>

                        {{-- Badge statut --}}
                        @if($isActive)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                     text-[10px] font-semibold
                                     bg-emerald-100 dark:bg-emerald-900/30
                                     text-emerald-700 dark:text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Active
                        </span>
                        @elseif($isPast)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                     text-[10px] font-semibold
                                     bg-slate-100 dark:bg-slate-700
                                     text-slate-500 dark:text-slate-400">
                            <i class="bi bi-check2"></i>
                            Terminée
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                     text-[10px] font-semibold
                                     bg-amber-100 dark:bg-amber-900/30
                                     text-amber-700 dark:text-amber-400">
                            <i class="bi bi-clock"></i>
                            À venir
                        </span>
                        @endif
                    </div>

                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">
                        <i class="bi bi-calendar3 text-slate-400 mr-1"></i>
                        {{ $year->start_date->format('d M Y') }}
                        →
                        {{ $year->end_date->format('d M Y') }}
                        ·
                        <span class="font-medium">{{ $year->terms->count() }} trimestre(s)</span>
                    </p>

                    {{-- Barre de progression --}}
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-700
                                    rounded-full overflow-hidden max-w-xs">
                            <div class="h-full rounded-full transition-all duration-700
                                        {{ $isActive
                                            ? 'bg-blue-500'
                                            : ($isPast ? 'bg-slate-400' : 'bg-amber-400') }}"
                                style="width: {{ $progress }}%">
                            </div>
                        </div>
                        <span class="text-[10px] font-medium text-slate-400 dark:text-slate-500
                                     shrink-0">
                            {{ $isFuture ? '0' : $progress }}%
                        </span>
                    </div>
                </div>

                {{-- Trimestres --}}
                <div class="hidden xl:flex items-center gap-1.5 shrink-0">
                    @foreach($year->terms->sortBy('start_date') as $term)
                    @php
                    $isCurrent = now()->between($term->start_date, $term->end_date);
                    $termPast = now()->isAfter($term->end_date);
                    @endphp
                    <div class="px-2.5 py-1.5 rounded-lg text-[10px] font-medium
                                {{ $isCurrent
                                    ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 ring-1 ring-blue-300 dark:ring-blue-700'
                                    : ($termPast
                                        ? 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'
                                        : 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400') }}">
                        {{ $term->name }}
                    </div>
                    @endforeach
                    @if($year->terms->isEmpty())
                    <span class="text-xs text-slate-400 dark:text-slate-500 italic">
                        Aucun trimestre
                    </span>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-1.5 shrink-0
                            sm:opacity-0 sm:group-hover:opacity-100
                            transition-opacity duration-200">

                    {{-- Activer --}}
                    @if(!$isActive)
                    <form method="POST" action="{{ route('academic-years.activate', $year) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" title="Définir comme année active"
                            onclick="return confirm('Définir {{ $year->name }} comme année académique active ?')" class="w-8 h-8 rounded-lg flex items-center justify-center
                                       bg-emerald-50 dark:bg-emerald-900/20
                                       text-emerald-600 dark:text-emerald-400
                                       hover:bg-emerald-100 dark:hover:bg-emerald-900/40
                                       transition-colors focus:outline-none">
                            <i class="bi bi-check2-circle text-sm"></i>
                        </button>
                    </form>
                    @endif

                    {{-- Gérer trimestres --}}
                    <a href="{{ route('terms.index', ['academic_year_id' => $year->id]) }}" title="Gérer les trimestres"
                        class="w-8 h-8 rounded-lg flex items-center justify-center
                              bg-violet-50 dark:bg-violet-900/20
                              text-violet-600 dark:text-violet-400
                              hover:bg-violet-100 dark:hover:bg-violet-900/40
                              transition-colors">
                        <i class="bi bi-calendar-range text-sm"></i>
                    </a>

                    {{-- Modifier --}}
                    <a href="{{ route('academic-years.edit', $year) }}" title="Modifier" class="w-8 h-8 rounded-lg flex items-center justify-center
                              bg-slate-100 dark:bg-slate-700
                              text-slate-600 dark:text-slate-300
                              hover:bg-slate-200 dark:hover:bg-slate-600
                              transition-colors">
                        <i class="bi bi-pencil-fill text-sm"></i>
                    </a>

                    {{-- Supprimer --}}
                    @if(!$isActive)
                    <button onclick="deleteYear({{ $year->id }}, '{{ addslashes($year->name) }}')" title="Supprimer"
                        class="w-8 h-8 rounded-lg flex items-center justify-center
                                   bg-red-50 dark:bg-red-900/20
                                   text-red-500 dark:text-red-400
                                   hover:bg-red-100 dark:hover:bg-red-900/40
                                   transition-colors focus:outline-none">
                        <i class="bi bi-trash3-fill text-sm"></i>
                    </button>
                    @else
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                bg-slate-50 dark:bg-slate-800
                                text-slate-300 dark:text-slate-600
                                cursor-not-allowed" title="Impossible de supprimer l'année active">
                        <i class="bi bi-lock-fill text-sm"></i>
                    </div>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>

        {{-- Pagination --}}
        @if($academicYears->hasPages())
        <div class="px-5 py-3.5 border-t border-slate-100 dark:border-slate-700
                    flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $academicYears->firstItem() }} – {{ $academicYears->lastItem() }}
                sur {{ $academicYears->total() }} années
            </p>
            <div class="flex items-center gap-1">
                @if(!$academicYears->onFirstPage())
                <a href="{{ $academicYears->previousPageUrl() }}" class="px-3 py-1.5 rounded-xl text-xs
                          border border-slate-200 dark:border-slate-700
                          text-slate-600 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                    <i class="bi bi-chevron-left"></i>
                </a>
                @endif
                @foreach($academicYears->getUrlRange(
                max(1, $academicYears->currentPage() - 2),
                min($academicYears->lastPage(), $academicYears->currentPage() + 2)
                ) as $page => $url)
                @if($page == $academicYears->currentPage())
                <span class="px-3 py-1.5 rounded-xl text-xs font-semibold
                                 bg-blue-600 text-white">{{ $page }}</span>
                @else
                <a href="{{ $url }}" class="px-3 py-1.5 rounded-xl text-xs
                              border border-slate-200 dark:border-slate-700
                              text-slate-600 dark:text-slate-400
                              hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                    {{ $page }}
                </a>
                @endif
                @endforeach
                @if($academicYears->hasMorePages())
                <a href="{{ $academicYears->nextPageUrl() }}" class="px-3 py-1.5 rounded-xl text-xs
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
    const list = document.getElementById('academic-years-list');
    if (list && typeof Sortable !== 'undefined') {
        Sortable.create(list, {
            animation: 200,
            handle: '.year-item',
            ghostClass: 'opacity-40 bg-blue-50 dark:bg-blue-950/30',
            chosenClass: 'ring-2 ring-blue-400 rounded-xl',
            delay: 80,
            delayOnTouchOnly: true,

            onEnd(evt) {
                const order = [...list.querySelectorAll('[data-id]')]
                    .map(el => el.dataset.id);

                // Optionnel : persister l'ordre via AJAX
                fetch('{{ route("academic-years.reorder") }}', {
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
                    message: 'Le classement des années a été réorganisé.',
                    delay: 2500,
                });
            }
        });
    }
});


function deleteYear(id, name) {
    if (!confirm(`Supprimer l'année "${name}" ? Cette action supprimera aussi les trimestres associés.`)) return;
    const form = document.getElementById('delete-form');
    form.action = `/academic-years/${id}`;
    form.submit();
}
</script>
@endpush