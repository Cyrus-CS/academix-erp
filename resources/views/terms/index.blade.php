@extends('layouts.base')

@section('page_title', 'Trimestres')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm">
            <i class="bi bi-calendar3 text-white text-lg"></i>
        </div>
        <div>
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                Trimestres
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $terms->total() }} trimestre(s) au total
            </p>
        </div>
    </div>
    <a href="{{ route('terms.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  bg-blue-600 hover:bg-blue-700 text-white
                  shadow-sm hover:shadow-md transition-all duration-200
                  focus:outline-none focus:ring-2 focus:ring-blue-500/40 shrink-0">
        <i class="bi bi-plus-lg"></i>
        Nouveau trimestre
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ── Filtres ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('terms.index') }}" class="flex flex-wrap items-end gap-3">

            <div class="flex-1 min-w-44 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="bi bi-mortarboard text-slate-400 text-sm"></i>
                </span>
                <select name="academic_year_id" class="w-full pl-8 pr-7 py-2 rounded-xl border text-xs
                               text-slate-700 dark:text-slate-300
                               bg-white dark:bg-slate-700/50 appearance-none
                               border-slate-200 dark:border-slate-600
                               focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <option value="">Toutes les années</option>
                    @foreach($academicYears as $year)
                    <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                        {{ $year->name }}
                    </option>
                    @endforeach
                </select>
                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <i class="bi bi-chevron-down text-slate-400 text-[10px]"></i>
                </span>
            </div>

            <div class="flex-1 min-w-36 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="bi bi-circle-fill text-slate-400 text-xs"></i>
                </span>
                <select name="status" class="w-full pl-8 pr-7 py-2 rounded-xl border text-xs
                               text-slate-700 dark:text-slate-300
                               bg-white dark:bg-slate-700/50 appearance-none
                               border-slate-200 dark:border-slate-600
                               focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <option value="">Tous les statuts</option>
                    <option value="current" {{ request('status') === 'current' ? 'selected' : '' }}>En cours</option>
                    <option value="other" {{ request('status') === 'other'   ? 'selected' : '' }}>Terminé</option>
                </select>
                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <i class="bi bi-chevron-down text-slate-400 text-[10px]"></i>
                </span>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-medium
                               bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
                    <i class="bi bi-funnel-fill"></i>
                    Filtrer
                </button>
                @if(request()->hasAny(['academic_year_id', 'status']))
                <a href="{{ route('terms.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium
                          border border-slate-200 dark:border-slate-700
                          text-slate-600 dark:text-slate-400
                          hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all duration-200">
                    <i class="bi bi-x-circle"></i>
                    Réinitialiser
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Cards Trimestres ── --}}
    @forelse($terms as $term)
    @php
    $progress = 0;
    if ($term->start_date && $term->end_date) {
    $total = $term->start_date->diffInDays($term->end_date);
    $elapsed = min($total, now()->diffInDays($term->start_date, false) >= 0
    ? now()->diffInDays($term->start_date)
    : $total);
    $progress = $total > 0 ? min(100, round(($elapsed / $total) * 100)) : 0;
    }
    @endphp
    <div class="bg-white dark:bg-slate-800 rounded-2xl border
                {{ $term->is_current ? 'border-blue-300 dark:border-blue-700' : 'border-slate-200 dark:border-slate-700' }}
                shadow-sm overflow-hidden">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5">

            {{-- Infos --}}
            <div class="flex items-start gap-4 min-w-0 flex-1">
                <div class="w-11 h-11 rounded-xl shrink-0
                            {{ $term->is_current
                                ? 'bg-blue-600'
                                : 'bg-slate-100 dark:bg-slate-700' }}
                            flex items-center justify-center">
                    <i class="bi bi-calendar3
                              {{ $term->is_current ? 'text-white' : 'text-slate-400 dark:text-slate-500' }}
                              text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-bold text-slate-800 dark:text-slate-100">
                            {{ $term->name }}
                        </h3>
                        @if($term->is_current)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px]
                                     font-semibold bg-blue-100 dark:bg-blue-900/30
                                     text-blue-700 dark:text-blue-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                            En cours
                        </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $term->academicYear?->name }}
                    </p>
                    <div class="flex items-center gap-4 mt-2 text-xs text-slate-500 dark:text-slate-400">
                        <span class="flex items-center gap-1">
                            <i class="bi bi-calendar-event text-[10px]"></i>
                            {{ $term->start_date?->format('d/m/Y') ?? '|' }}
                        </span>
                        <span class="text-slate-300 dark:text-slate-600">→</span>
                        <span class="flex items-center gap-1">
                            <i class="bi bi-calendar-check text-[10px]"></i>
                            {{ $term->end_date?->format('d/m/Y') ?? '—' }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="bi bi-pencil text-[10px]"></i>
                            {{ $term->grades_count ?? 0 }} notes
                        </span>
                    </div>

                    {{-- Barre progression --}}
                    @if($term->is_current)
                    <div class="mt-3 space-y-1">
                        <div class="flex items-center justify-between text-[10px] text-slate-400">
                            <span>Progression</span>
                            <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $progress }}%</span>
                        </div>
                        <div class="h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full transition-all duration-500"
                                style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('terms.show', $term) }}" class="p-2 rounded-xl text-slate-400 hover:text-blue-600 dark:hover:text-blue-400
                          hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200" title="Voir">
                    <i class="bi bi-eye text-base"></i>
                </a>
                <a href="{{ route('terms.edit', $term) }}" class="p-2 rounded-xl text-slate-400 hover:text-amber-600 dark:hover:text-amber-400
                          hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all duration-200" title="Modifier">
                    <i class="bi bi-pencil text-base"></i>
                </a>
                @if(!$term->is_current)
                <form method="POST" action="{{ route('terms.destroy', $term) }}" class="inline"
                    onsubmit="return confirm('Supprimer ce trimestre ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-red-600 dark:hover:text-red-400
                                   hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200"
                        title="Supprimer">
                        <i class="bi bi-trash3 text-base"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm p-16 text-center">
        <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700/50
                    flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-calendar3 text-3xl text-slate-300 dark:text-slate-600"></i>
        </div>
        <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-3">
            Aucun trimestre créé
        </p>
        <a href="{{ route('terms.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium
                  bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
            <i class="bi bi-plus-lg"></i>
            Créer le premier trimestre
        </a>
    </div>
    @endforelse

    {{-- Pagination --}}
    @if($terms->hasPages())
    <div class="flex justify-center">
        {{ $terms->onEachSide(1)->links('vendor.pagination.tailwind') }}
    </div>
    @endif

</div>
@endsection