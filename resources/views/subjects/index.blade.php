@extends('layouts.base')

@section('page_title', 'Matières')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm">
            <i class="bi bi-journal-bookmark-fill text-white text-lg"></i>
        </div>
        <div>
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100">Matières</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $subjects->total() }} matière(s) enregistrée(s)
            </p>
        </div>
    </div>
    <a href="{{ route('subjects.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  bg-blue-600 hover:bg-blue-700 text-white
                  shadow-sm hover:shadow-md transition-all duration-200
                  focus:outline-none focus:ring-2 focus:ring-blue-500/40 shrink-0">
        <i class="bi bi-plus-lg"></i>
        Nouvelle matière
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ── Filtres ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('subjects.index') }}" class="flex flex-wrap items-end gap-3">

            <div class="flex-1 min-w-48 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="bi bi-search text-slate-400 text-sm"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher une matière…"
                    class="w-full pl-8 pr-3.5 py-2 rounded-xl border text-xs
                              text-slate-700 dark:text-slate-300
                              bg-white dark:bg-slate-700/50 placeholder-slate-400
                              border-slate-200 dark:border-slate-600
                              focus:outline-none focus:ring-2 focus:ring-blue-500/30">
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
                    <option value="active" {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('subjects.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium
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

    {{-- ── Grille matières ── --}}
    @if($subjects->isNotEmpty())
    <x-sortable-grid resource="subjects" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($subjects as $subject)
        <x-sortable-item :id="$subject->id" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm hover:shadow-md
                transition-all duration-200 overflow-hidden group">

            {{-- Barre colorée top --}}
            <div class="h-1.5 bg-linear-to-r from-blue-600 to-emerald-500"></div>

            <div class="p-5">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30
                                    flex items-center justify-center shrink-0">
                            <i class="bi bi-journal-bookmark-fill
                                      text-blue-600 dark:text-blue-400 text-base"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-800 dark:text-slate-100 truncate">
                                {{ $subject->name }}
                            </h3>
                            <p class="text-xs font-mono text-slate-400 dark:text-slate-500">
                                {{ $subject->code }}
                            </p>
                        </div>
                    </div>

                    {{-- Statut --}}
                    @if($subject->is_active)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px]
                                 font-semibold shrink-0
                                 bg-emerald-100 dark:bg-emerald-900/30
                                 text-emerald-700 dark:text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Active
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px]
                                 font-semibold shrink-0
                                 bg-slate-100 dark:bg-slate-700
                                 text-slate-500 dark:text-slate-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        Inactive
                    </span>
                    @endif
                </div>

                {{-- Description --}}
                @if($subject->description)
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-2 mb-4">
                    {{ $subject->description }}
                </p>
                @endif

                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-2 mb-4">
                    @foreach([
                    ['label' => 'Coef.', 'value' => $subject->coefficient, 'icon' => 'bi-percent'],
                    ['label' => 'Notes', 'value' => $subject->grades_count ?? 0, 'icon' => 'bi-pencil'],
                    ['label' => 'Profs', 'value' => $subject->teacher_assignments_count ?? 0, 'icon' =>
                    'bi-person-badge'],
                    ] as $stat)
                    <div class="text-center px-2 py-2 rounded-lg
                                bg-slate-50 dark:bg-slate-700/30">
                        <i class="bi {{ $stat['icon'] }} text-slate-400 dark:text-slate-500 text-xs block mb-0.5"></i>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100">
                            {{ $stat['value'] }}
                        </p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500">
                            {{ $stat['label'] }}
                        </p>
                    </div>
                    @endforeach
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                    <a href="{{ route('subjects.show', $subject) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2
                              rounded-xl text-xs font-medium
                              text-slate-600 dark:text-slate-400
                              hover:bg-slate-100 dark:hover:bg-slate-700/50
                              transition-all duration-200">
                        <i class="bi bi-eye"></i>
                        Voir
                    </a>
                    <a href="{{ route('subjects.edit', $subject) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2
                              rounded-xl text-xs font-medium
                              text-blue-600 dark:text-blue-400
                              hover:bg-blue-50 dark:hover:bg-blue-900/20
                              transition-all duration-200">
                        <i class="bi bi-pencil"></i>
                        Modifier
                    </a>
                    <form method="POST" action="{{ route('subjects.destroy', $subject) }}"
                        onsubmit="return confirm('Supprimer « {{ $subject->name }} » ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center p-2 rounded-xl text-xs
                                       text-slate-400 hover:text-red-600 dark:hover:text-red-400
                                       hover:bg-red-50 dark:hover:bg-red-900/20
                                       transition-all duration-200">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </div>
            </div>
        </x-sortable-item>
        @endforeach
    </x-sortable-grid>

    {{-- Pagination --}}
    @if($subjects->hasPages())
    <div class="flex justify-center">
        {{ $subjects->onEachSide(1)->links('vendor.pagination.tailwind') }}
    </div>
    @endif

    @else
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm p-16 text-center">
        <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700/50
                    flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-journal-bookmark text-3xl text-slate-300 dark:text-slate-600"></i>
        </div>
        <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-3">
            Aucune matière enregistrée
        </p>
        <a href="{{ route('subjects.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium
                  bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
            <i class="bi bi-plus-lg"></i>
            Ajouter la première matière
        </a>
    </div>
    @endif

</div>
@endsection