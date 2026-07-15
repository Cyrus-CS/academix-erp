@extends('layouts.base')

@section('page_title', 'Types de frais')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm">
            <i class="bi bi-tag-fill text-white text-lg"></i>
        </div>
        <div>
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                Types de frais
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $feeTypes->total() }} type(s) de frais enregistré(s)
            </p>
        </div>
    </div>
    <a href="{{ route('fee-types.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  bg-blue-600 hover:bg-blue-700 text-white
                  shadow-sm hover:shadow-md transition-all duration-200
                  focus:outline-none focus:ring-2 focus:ring-blue-500/40 shrink-0">
        <i class="bi bi-plus-lg"></i>
        Nouveau type
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ── Filtres ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('fee-types.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-48 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="bi bi-search text-slate-400 text-sm"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Rechercher un type de frais…" class="w-full pl-8 pr-3.5 py-2 rounded-xl border text-xs
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
                    <option value="active" {{ request('status') === 'active'   ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
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
                <a href="{{ route('fee-types.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium
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

    {{-- ── Grille fee types ── --}}
    @if($feeTypes->isNotEmpty())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($feeTypes as $feeType)
        @php
        $freqConfig = [
        'monthly' => ['label' => 'Mensuel', 'icon' => 'bi-calendar-month', 'color' => 'blue'],
        'quarterly' => ['label' => 'Trimestriel', 'icon' => 'bi-calendar3', 'color' => 'cyan'],
        'yearly' => ['label' => 'Annuel', 'icon' => 'bi-calendar-year', 'color' => 'indigo'],
        'one_time' => ['label' => 'Unique', 'icon' => 'bi-1-circle', 'color' => 'purple'],
        ][$feeType->frequency] ?? ['label' => $feeType->frequency, 'icon' => 'bi-arrow-repeat', 'color' => 'slate'];
        @endphp
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm hover:shadow-md
                    transition-all duration-200 overflow-hidden">

            {{-- Dégradé top --}}
            <div class="bg-linear-to-r from-blue-600 to-emerald-500 p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px]
                                 font-semibold bg-white/20 text-white">
                        <i class="bi {{ $freqConfig['icon'] }} text-[10px]"></i>
                        {{ $freqConfig['label'] }}
                    </span>
                    @if($feeType->is_active)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px]
                                 font-semibold bg-emerald-400/20 text-emerald-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                        Actif
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px]
                                 font-semibold bg-white/10 text-white/70">
                        Inactif
                    </span>
                    @endif
                </div>
                <h3 class="font-bold text-white text-base truncate">{{ $feeType->name }}</h3>
                <p class="text-2xl font-extrabold text-white mt-1">
                    {{ number_format($feeType->amount, 0, ',', ' ') }}
                    <span class="text-sm font-normal text-blue-200">FCFA</span>
                </p>
            </div>

            <div class="p-5 space-y-4">

                {{-- Description --}}
                @if($feeType->description)
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-2">
                    {{ $feeType->description }}
                </p>
                @endif

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="text-center px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-700/30">
                        <p class="text-lg font-bold text-slate-800 dark:text-slate-100">
                            {{ $feeType->payments_count ?? 0 }}
                        </p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500">Paiements</p>
                    </div>
                    <div class="text-center px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-700/30">
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">
                            {{ number_format($feeType->payments_sum_amount ?? 0, 0, ',', ' ') }}
                        </p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500">Total collecté (FCFA)</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 pt-2 border-t border-slate-100 dark:border-slate-700">
                    <a href="{{ route('fee-types.show', $feeType) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2
                              rounded-xl text-xs font-medium
                              text-slate-600 dark:text-slate-400
                              hover:bg-slate-100 dark:hover:bg-slate-700/50
                              transition-all duration-200">
                        <i class="bi bi-eye"></i>
                        Voir
                    </a>
                    <a href="{{ route('fee-types.edit', $feeType) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2
                              rounded-xl text-xs font-medium
                              text-blue-600 dark:text-blue-400
                              hover:bg-blue-50 dark:hover:bg-blue-900/20
                              transition-all duration-200">
                        <i class="bi bi-pencil"></i>
                        Modifier
                    </a>
                    <form method="POST" action="{{ route('fee-types.destroy', $feeType) }}"
                        onsubmit="return confirm('Supprimer « {{ $feeType->name }} » ?')">
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
        </div>
        @endforeach
    </div>

    @if($feeTypes->hasPages())
    <div class="flex justify-center">
        {{ $feeTypes->onEachSide(1)->links('vendor.pagination.tailwind') }}
    </div>
    @endif

    @else
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm p-16 text-center">
        <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700/50
                    flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-tag text-3xl text-slate-300 dark:text-slate-600"></i>
        </div>
        <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-3">
            Aucun type de frais créé
        </p>
        <a href="{{ route('fee-types.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium
                  bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
            <i class="bi bi-plus-lg"></i>
            Créer le premier type
        </a>
    </div>
    @endif

</div>
@endsection