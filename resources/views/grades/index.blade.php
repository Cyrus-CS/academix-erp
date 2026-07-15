@extends('layouts.base')

@section('page_title', 'Notes')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm shrink-0">
            <i class="bi bi-pencil-square text-white text-lg"></i>
        </div>
        <div>
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                Gestion des notes
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $grades->total() }} note(s) enregistrée(s)
            </p>
        </div>
    </div>

    <a href="{{ route('grades.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  bg-blue-600 hover:bg-blue-700 text-white
                  shadow-sm hover:shadow-md transition-all duration-200
                  focus:outline-none focus:ring-2 focus:ring-blue-500/40 shrink-0">
        <i class="bi bi-plus-lg"></i>
        Saisir une note
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ── Filtres ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('grades.index') }}" class="flex flex-wrap items-end gap-3">

            {{-- Classe --}}
            <div class="flex-1 min-w-36 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="bi bi-building text-slate-400 text-sm"></i>
                </span>
                <select name="class_id" class="w-full pl-8 pr-7 py-2 rounded-xl border text-xs
                               text-slate-700 dark:text-slate-300
                               bg-white dark:bg-slate-700/50 appearance-none
                               border-slate-200 dark:border-slate-600
                               focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                    @endforeach
                </select>
                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <i class="bi bi-chevron-down text-slate-400 text-[10px]"></i>
                </span>
            </div>

            {{-- Matière --}}
            <div class="flex-1 min-w-36 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="bi bi-journal-bookmark text-slate-400 text-sm"></i>
                </span>
                <select name="subject_id" class="w-full pl-8 pr-7 py-2 rounded-xl border text-xs
                               text-slate-700 dark:text-slate-300
                               bg-white dark:bg-slate-700/50 appearance-none
                               border-slate-200 dark:border-slate-600
                               focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <option value="">Toutes les matières</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                    @endforeach
                </select>
                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <i class="bi bi-chevron-down text-slate-400 text-[10px]"></i>
                </span>
            </div>

            {{-- Trimestre --}}
            <div class="flex-1 min-w-36 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="bi bi-calendar3 text-slate-400 text-sm"></i>
                </span>
                <select name="term_id" class="w-full pl-8 pr-7 py-2 rounded-xl border text-xs
                               text-slate-700 dark:text-slate-300
                               bg-white dark:bg-slate-700/50 appearance-none
                               border-slate-200 dark:border-slate-600
                               focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <option value="">Tous les trimestres</option>
                    @foreach($terms as $term)
                    <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                        {{ $term->name }} — {{ $term->academicYear?->name }}
                    </option>
                    @endforeach
                </select>
                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <i class="bi bi-chevron-down text-slate-400 text-[10px]"></i>
                </span>
            </div>

            {{-- Type --}}
            <div class="flex-1 min-w-32 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="bi bi-list-check text-slate-400 text-sm"></i>
                </span>
                <select name="type" class="w-full pl-8 pr-7 py-2 rounded-xl border text-xs
                               text-slate-700 dark:text-slate-300
                               bg-white dark:bg-slate-700/50 appearance-none
                               border-slate-200 dark:border-slate-600
                               focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <option value="">Tous les types</option>
                    <option value="homework" {{ request('type') === 'homework' ? 'selected' : '' }}>Devoir</option>
                    <option value="test" {{ request('type') === 'test'     ? 'selected' : '' }}>Test</option>
                    <option value="exam" {{ request('type') === 'exam'     ? 'selected' : '' }}>Examen</option>
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
                @if(request()->hasAny(['class_id', 'subject_id', 'term_id', 'type']))
                <a href="{{ route('grades.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium
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

    {{-- ── Tableau ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50">
                    <tr>
                        @foreach([
                        'Étudiant', 'Matière', 'Classe',
                        'Type', 'Note', 'Trimestre', 'Date', 'Actions'
                        ] as $th)
                        <th class="px-4 py-3 text-left text-xs font-semibold
                                   text-slate-500 dark:text-slate-400 uppercase tracking-wide
                                   whitespace-nowrap">
                            {{ $th }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($grades as $grade)
                    @php
                    $pct = $grade->max_score > 0 ? ($grade->score / $grade->max_score) * 100 : 0;
                    $score20 = $grade->max_score > 0 ? ($grade->score / $grade->max_score) * 20 : 0;
                    $colorClass = match(true) {
                    $score20 >= 16 => 'text-emerald-600 dark:text-emerald-400',
                    $score20 >= 12 => 'text-blue-600 dark:text-blue-400',
                    $score20 >= 10 => 'text-amber-600 dark:text-amber-400',
                    default => 'text-red-600 dark:text-red-400',
                    };
                    $bgClass = match(true) {
                    $score20 >= 16 => 'bg-emerald-500',
                    $score20 >= 12 => 'bg-blue-500',
                    $score20 >= 10 => 'bg-amber-500',
                    default => 'bg-red-500',
                    };
                    $typeConfig = [
                    'homework' => ['label' => 'Devoir', 'color' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700
                    dark:text-blue-400'],
                    'test' => ['label' => 'Test', 'color' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700
                    dark:text-amber-400'],
                    'exam' => ['label' => 'Examen', 'color' => 'bg-red-100 dark:bg-red-900/30 text-red-700
                    dark:text-red-400'],
                    ][$grade->type] ?? ['label' => $grade->type, 'color' => 'bg-slate-100 text-slate-600'];
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">

                        {{-- Étudiant --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full shrink-0
                                            bg-linear-to-br from-blue-500 to-emerald-500
                                            flex items-center justify-center text-white text-[10px] font-bold">
                                    {{ strtoupper(substr($grade->student?->user?->name ?? 'E', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-100 truncate">
                                        {{ $grade->student?->user?->name ?? '—' }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                        {{ $grade->student?->student_number ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Matière --}}
                        <td class="px-4 py-3.5">
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">
                                {{ $grade->subject?->name ?? '—' }}
                            </span>
                            @if($grade->subject?->coefficient)
                            <span class="ml-1 text-[10px] text-slate-400">
                                (coef. {{ $grade->subject->coefficient }})
                            </span>
                            @endif
                        </td>

                        {{-- Classe --}}
                        <td class="px-4 py-3.5">
                            <span class="text-xs text-slate-600 dark:text-slate-400">
                                {{ $grade->schoolClass?->name ?? '—' }}
                            </span>
                        </td>

                        {{-- Type --}}
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                         {{ $typeConfig['color'] }}">
                                {{ $typeConfig['label'] }}
                            </span>
                        </td>

                        {{-- Note --}}
                        <td class="px-4 py-3.5">
                            <div class="space-y-1 min-w-24">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold {{ $colorClass }}">
                                        {{ number_format($grade->score, 2) }}
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        / {{ $grade->max_score }}
                                    </span>
                                </div>
                                <div class="h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full {{ $bgClass }} rounded-full transition-all"
                                        style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        </td>

                        {{-- Trimestre --}}
                        <td class="px-4 py-3.5">
                            <span class="text-xs text-slate-600 dark:text-slate-400">
                                {{ $grade->term?->name ?? '—' }}
                            </span>
                        </td>

                        {{-- Date --}}
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $grade->graded_at?->format('d/m/Y') ?? '—' }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('grades.edit', $grade) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 dark:hover:text-blue-400
                                          hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200"
                                    title="Modifier">
                                    <i class="bi bi-pencil text-sm"></i>
                                </a>
                                <form method="POST" action="{{ route('grades.destroy', $grade) }}" class="inline"
                                    onsubmit="return confirm('Supprimer cette note ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 dark:hover:text-red-400
                                                   hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200"
                                        title="Supprimer">
                                        <i class="bi bi-trash3 text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700/50
                                            flex items-center justify-center">
                                    <i class="bi bi-pencil-square text-3xl text-slate-300 dark:text-slate-600"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">
                                    Aucune note enregistrée
                                </p>
                                <a href="{{ route('grades.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium
                                          bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
                                    <i class="bi bi-plus-lg"></i>
                                    Saisir la première note
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($grades->hasPages())
        <div class="px-4 py-3.5 border-t border-slate-100 dark:border-slate-700
                    flex items-center justify-between gap-4">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $grades->firstItem() }}–{{ $grades->lastItem() }} sur {{ $grades->total() }} notes
            </p>
            {{ $grades->onEachSide(1)->links('vendor.pagination.tailwind') }}
        </div>
        @endif
    </div>

</div>
@endsection