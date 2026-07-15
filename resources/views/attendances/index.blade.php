@extends('layouts.base')

@section('page_title', 'Présences')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm">
            <i class="bi bi-person-check-fill text-white text-lg"></i>
        </div>
        <div>
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                Gestion des présences
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Taux de présence global :
                <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                    {{ $stats['rate'] }}%
                </span>
            </p>
        </div>
    </div>
    <a href="{{ route('attendance.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  bg-blue-600 hover:bg-blue-700 text-white
                  shadow-sm hover:shadow-md transition-all duration-200
                  focus:outline-none focus:ring-2 focus:ring-blue-500/40 shrink-0">
        <i class="bi bi-plus-lg"></i>
        Saisir les présences
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ── Stats ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
        ['label' => 'Total', 'value' => $stats['total'], 'icon' => 'bi-list-check', 'color' => 'blue', 'bg' =>
        'from-blue-500 to-indigo-500'],
        ['label' => 'Présents', 'value' => $stats['present'], 'icon' => 'bi-check-circle-fill', 'color' => 'emerald',
        'bg' => 'from-emerald-500 to-teal-500'],
        ['label' => 'Absents', 'value' => $stats['absent'], 'icon' => 'bi-x-circle-fill', 'color' => 'red', 'bg' =>
        'from-red-500 to-rose-500'],
        ['label' => 'En retard', 'value' => $stats['late'], 'icon' => 'bi-clock-fill', 'color' => 'amber', 'bg' =>
        'from-amber-500 to-orange-500'],
        ] as $stat)
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm p-4 overflow-hidden relative">
            <div class="absolute -right-3 -top-3 w-16 h-16 rounded-full opacity-10
                        bg-linear-to-br {{ $stat['bg'] }}"></div>
            <div class="flex items-start justify-between gap-2 relative">
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">{{ $stat['label'] }}</p>
                    <p class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">
                        {{ $stat['value'] }}
                    </p>
                </div>
                <div class="w-9 h-9 rounded-xl shrink-0
                            bg-linear-to-br {{ $stat['bg'] }}
                            flex items-center justify-center">
                    <i class="bi {{ $stat['icon'] }} text-white text-sm"></i>
                </div>
            </div>

            @if($stat['label'] !== 'Total' && $stats['total'] > 0)
            <div class="mt-3">
                @php $pct = round(($stat['value'] / $stats['total']) * 100); @endphp
                <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1">
                    <span>{{ $pct }}%</span>
                </div>
                <div class="h-1 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-linear-to-r {{ $stat['bg'] }} rounded-full" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- ── Filtres ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('attendance.index') }}" class="flex flex-wrap items-end gap-3">

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

            {{-- Date --}}
            <div class="flex-1 min-w-36">
                <input type="date" name="date" value="{{ request('date') }}" class="w-full px-3 py-2 rounded-xl border text-xs
                              text-slate-700 dark:text-slate-300
                              bg-white dark:bg-slate-700/50
                              border-slate-200 dark:border-slate-600
                              focus:outline-none focus:ring-2 focus:ring-blue-500/30">
            </div>

            {{-- Statut --}}
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
                    <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Présent</option>
                    <option value="absent" {{ request('status') === 'absent'  ? 'selected' : '' }}>Absent</option>
                    <option value="late" {{ request('status') === 'late'    ? 'selected' : '' }}>En retard</option>
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
                @if(request()->hasAny(['class_id', 'date', 'status', 'student_id']))
                <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium
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
                        @foreach(['Étudiant', 'Classe', 'Date', 'Statut', 'Note', 'Actions'] as $th)
                        <th class="px-4 py-3 text-left text-xs font-semibold
                                   text-slate-500 dark:text-slate-400 uppercase tracking-wide whitespace-nowrap">
                            {{ $th }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($attendances as $attendance)
                    @php
                    $statusConfig = [
                    'present' => ['label' => 'Présent', 'class' => 'bg-emerald-100 dark:bg-emerald-900/30
                    text-emerald-700 dark:text-emerald-400', 'icon' => 'bi-check-circle-fill'],
                    'absent' => ['label' => 'Absent', 'class' => 'bg-red-100 dark:bg-red-900/30 text-red-700
                    dark:text-red-400', 'icon' => 'bi-x-circle-fill'],
                    'late' => ['label' => 'En retard', 'class' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700
                    dark:text-amber-400', 'icon' => 'bi-clock-fill'],
                    ][$attendance->status] ?? ['label' => $attendance->status, 'class' => 'bg-slate-100 text-slate-600',
                    'icon' => 'bi-circle'];
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">

                        {{-- Étudiant --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full shrink-0
                                            bg-linear-to-br from-blue-500 to-emerald-500
                                            flex items-center justify-center text-white text-[10px] font-bold">
                                    {{ strtoupper(substr($attendance->student?->user?->name ?? 'E', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-100 truncate">
                                        {{ $attendance->student?->user?->name ?? '—' }}
                                    </p>
                                    <p class="text-[10px] text-slate-400">
                                        {{ $attendance->student?->student_number ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Classe --}}
                        <td class="px-4 py-3.5">
                            <span class="text-xs text-slate-600 dark:text-slate-400">
                                {{ $attendance->classe?->name ?? '—' }}
                            </span>
                        </td>

                        {{-- Date --}}
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="text-xs text-slate-600 dark:text-slate-400">
                                {{ $attendance->date?->format('d/m/Y') ?? '—' }}
                            </span>
                        </td>

                        {{-- Statut --}}
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full
                                         text-[10px] font-semibold {{ $statusConfig['class'] }}">
                                <i class="bi {{ $statusConfig['icon'] }} text-[8px]"></i>
                                {{ $statusConfig['label'] }}
                            </span>
                        </td>

                        {{-- Note --}}
                        <td class="px-4 py-3.5">
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $attendance->note ?? '—' }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('attendance.edit', $attendance) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 dark:hover:text-amber-400
                                          hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all duration-200"
                                    title="Modifier">
                                    <i class="bi bi-pencil text-sm"></i>
                                </a>
                                <form method="POST" action="{{ route('attendance.destroy', $attendance) }}"
                                    class="inline" onsubmit="return confirm('Supprimer cet enregistrement ?')">
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
                        <td colspan="6" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700/50
                                            flex items-center justify-center">
                                    <i class="bi bi-person-check text-3xl text-slate-300 dark:text-slate-600"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">
                                    Aucune présence enregistrée
                                </p>
                                <a href="{{ route('attendance.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium
                                          bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
                                    <i class="bi bi-plus-lg"></i>
                                    Saisir les présences
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
        <div class="px-4 py-3.5 border-t border-slate-100 dark:border-slate-700
                    flex items-center justify-between gap-4">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $attendances->firstItem() }}–{{ $attendances->lastItem() }} sur {{ $attendances->total() }}
                enregistrements
            </p>
            {{ $attendances->onEachSide(1)->links('vendor.pagination.tailwind') }}
        </div>
        @endif
    </div>

</div>
@endsection