@extends('layouts.base')

@section('page_title', 'Classe : ' . $classe->name)

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('classes.index') }}"
    class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Classes</a>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('classes.index') }}"
            class="w-9 h-9 rounded-xl flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-blue-600 hover:border-blue-300 dark:hover:border-blue-600 transition-all">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">Détail de la classe</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Gestion des élèves, enseignants et
                matières</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('classes.edit', $classe) }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
            <i class="bi bi-pencil-square"></i><span class="hidden sm:inline">Modifier</span>
        </a>
        <form id="delete-classe-form" action="{{ route('classes.destroy', $classe) }}" method="POST">
            @csrf @method('DELETE')
            <button id="delete-classe-btn" type="button"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-red-600 hover:bg-red-700 text-white shadow-sm hover:shadow-red-500/20 transition-all">
                <i class="bi bi-trash3"></i><span class="hidden sm:inline">Supprimer</span>
            </button>
        </form>
    </div>
</div>
@endsection

@section('content')
@php
$studentsCount = $classe->students->count();
$teachersCount = $classe->teachers->count();
$subjectsCount = $classe->subjects->count();
$capacity = $classe->capacity ?? 0;
$occupancy = $capacity > 0 ? round(($studentsCount / $capacity) * 100) : 0;
@endphp

{{-- Hero --}}
<div
    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden mb-6">
    <div class="p-6 sm:p-7">
        <div class="flex flex-col lg:flex-row lg:items-start gap-6">
            <div
                class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-600 to-emerald-500 flex items-center justify-center shadow-lg shadow-blue-500/20 shrink-0">
                <i class="bi bi-collection-fill text-white text-2xl"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2.5 mb-2">
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $classe->name }}</h2>
                    <span
                        class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">{{ $classe->level ?? 'Niveau non défini' }}</span>
                    @if($capacity)
                    <span
                        class="px-2.5 py-1 rounded-full text-[11px] font-medium {{ $occupancy >= 90 ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-200 dark:border-red-800' : 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' }} border">{{ $occupancy }}%
                        occupé</span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-3xl">
                    {{ $classe->description ?? 'Aucune description disponible pour cette classe.' }}</p>
                <div class="flex flex-wrap gap-3 sm:gap-5 mt-4 text-xs text-slate-500 dark:text-slate-400">
                    <span class="inline-flex items-center gap-1.5"><i class="bi bi-hash text-slate-400"></i>
                        {{ $classe->code ?? 'CODE-'.$classe->id }}</span>
                    <span class="inline-flex items-center gap-1.5"><i class="bi bi-people text-slate-400"></i> Capacité
                        : {{ $capacity ?: 'Illimitée' }}</span>
                    <span class="inline-flex items-center gap-1.5"><i class="bi bi-calendar3 text-slate-400"></i>
                        {{ $classe->created_at->diffForHumans() }}</span>
                    <span class="inline-flex items-center gap-1.5"><i class="bi bi-diagram-3 text-slate-400"></i>
                        {{ $subjectsCount }} matières</span>
                </div>
            </div>
        </div>
    </div>
    <div class="h-1 w-full bg-gradient-to-r from-blue-600 via-blue-500 to-emerald-500"></div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
    ['label' => 'Élèves inscrits', 'value' => $studentsCount, 'sub' => $capacity ? "/ $capacity places" : 'Total',
    'icon' => 'bi-people-fill', 'color' => 'blue'],
    ['label' => 'Enseignants', 'value' => $teachersCount, 'sub' => 'Affectés', 'icon' => 'bi-person-workspace', 'color'
    => 'emerald'],
    ['label' => 'Matières', 'value' => $subjectsCount, 'sub' => 'Enseignées', 'icon' => 'bi-journal-bookmark-fill',
    'color' => 'amber'],
    ['label' => 'Occupation', 'value' => $occupancy.'%', 'sub' => $capacity ? "$studentsCount / $capacity" : 'Pas de
    limite', 'icon' => 'bi-pie-chart-fill', 'color' => 'cyan'],
    ] as $stat)
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                    {{ $stat['label'] }}</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ $stat['value'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $stat['sub'] }}</p>
            </div>
            <div
                class="w-10 h-10 rounded-xl flex items-center justify-center 
                {{ $stat['color'] === 'blue' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : '' }}
                {{ $stat['color'] === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : '' }}
                {{ $stat['color'] === 'amber' ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : '' }}
                {{ $stat['color'] === 'cyan' ? 'bg-cyan-50 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400' : '' }}">
                <i class="bi {{ $stat['icon'] }} text-lg"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Students --}}
    <div
        class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div
            class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <i class="bi bi-people text-blue-500"></i> Élèves ({{ $studentsCount }})
            </h3>
            <a href="{{ route('students.index') }}?classe_id={{ $classe->id }}"
                class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">Voir tous</a>
        </div>
        @if($studentsCount > 0)
        {{-- Desktop Table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead
                    class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-5 py-3 text-left">Élève</th>
                        <th class="px-5 py-3 text-left">Matricule</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @foreach($classe->students as $student)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-emerald-500 flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($student->user->name ?? 'E', 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-slate-800 dark:text-slate-200 truncate">
                                        {{ $student->user->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                        {{ $student->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-300">
                            {{ $student->admission_number ?? $student->matricule ?? '—' }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('students.show', $student) }}"
                                class="inline-flex w-7 h-7 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-600 text-slate-500 hover:text-blue-600 hover:border-blue-300 dark:hover:border-blue-500 transition-all">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{-- Mobile Cards --}}
        <div class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700">
            @foreach($classe->students as $student)
            <div class="p-4 flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div
                        class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-emerald-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ strtoupper(substr($student->user->name ?? 'E',0,1)) }}</div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">
                            {{ $student->user->name }}</p>
                        <p class="text-xs text-slate-500">{{ $student->admission_number ?? '—' }}</p>
                    </div>
                </div>
                <a href="{{ route('students.show', $student) }}"
                    class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-700 flex items-center justify-center text-slate-500"><i
                        class="bi bi-chevron-right text-xs"></i></a>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-12 text-center">
            <div
                class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                <i class="bi bi-inbox text-xl text-slate-400"></i></div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucun élève inscrit</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Cette classe n'a pas encore d'élèves.</p>
        </div>
        @endif
    </div>

    {{-- Right Sidebar --}}
    <div class="space-y-6">
        {{-- Teachers --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2"><i
                        class="bi bi-person-badge text-emerald-500"></i> Enseignants ({{ $teachersCount }})</h3>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
                @forelse($classe->teachers as $teacher)
                <div class="p-4 flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <div
                        class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-bold text-xs shrink-0">
                        {{ strtoupper(substr($teacher->user->name ?? 'T',0,1)) }}</div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">
                            {{ $teacher->user->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $teacher->qualification ?? 'Enseignant' }}</p>
                    </div>
                    <a href="{{ route('teachers.show', $teacher) }}"
                        class="text-slate-400 hover:text-blue-600 transition-colors"><i
                            class="bi bi-box-arrow-up-right text-xs"></i></a>
                </div>
                @empty
                <div class="p-6 text-center text-xs text-slate-400">Aucun enseignant assigné</div>
                @endforelse
            </div>
        </div>

        {{-- Subjects --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2"><i
                        class="bi bi-journals text-amber-500"></i> Matières ({{ $subjectsCount }})</h3>
            </div>
            <div class="p-4">
                @if($subjectsCount > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($classe->subjects as $subject)
                    <a href="{{ route('subjects.show', $subject) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:border-blue-300 dark:hover:border-blue-600 hover:text-blue-600 dark:hover:text-blue-400 transition-all">
                        <i class="bi bi-book"></i> {{ $subject->name }}
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-slate-400 dark:text-slate-500 text-center py-3">Aucune matière liée</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('delete-classe-btn');
    const form = document.getElementById('delete-classe-form');
    if (btn && form) {
        btn.addEventListener('click', () => {
            if (confirm('Supprimer définitivement cette classe ? Cette action est irréversible.')) form
                .submit();
        });
    }
});
</script>
@endsection