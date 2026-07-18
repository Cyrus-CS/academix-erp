@extends('layouts.base')

@section('page_title', $subject->name)

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600">/</span>
<a href="{{ route('subjects.index') }}"
    class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Matières</a>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('subjects.index') }}"
            class="w-9 h-9 rounded-xl flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-blue-600 transition-all">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">Matière : {{ $subject->name }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Code {{ $subject->code ?? '—' }} •
                Coef {{ $subject->coefficient ?? '—' }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('subjects.edit', $subject) }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 transition-all"><i
                class="bi bi-pencil-square"></i> Modifier</a>
        <form id="delete-subject-form" action="{{ route('subjects.destroy', $subject) }}" method="POST">
            @csrf @method('DELETE')
            <button id="delete-subject-btn" type="button"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition-all"><i
                    class="bi bi-trash3"></i> Supprimer</button>
        </form>
    </div>
</div>
@endsection

@section('content')
@php
$assignments = $subject->teacherAssignments;
$grades = $subject->grades;
$avg = $grades->avg('score');
$classesCount = $assignments->pluck('school_class_id')->unique()->count();
$teachersCount = $assignments->pluck('teacher_id')->unique()->count();
@endphp

<div
    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden mb-6">
    <div class="p-6 sm:p-7">
        <div class="flex flex-col sm:flex-row gap-6">
            <div
                class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center shadow-lg shrink-0">
                <i class="bi bi-journal-richtext text-white text-2xl"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-3 mb-2">
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $subject->name }}</h2>
                    <span
                        class="px-2.5 py-1 rounded-full text-xs font-mono font-medium bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300">{{ $subject->code ?? 'CODE' }}</span>
                    @if($subject->coefficient)
                    <span
                        class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">Coeff
                        x{{ $subject->coefficient }}</span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                    {{ $subject->description ?? 'Aucune description pour cette matière.' }}</p>
            </div>
        </div>
    </div>
    <div class="h-1 w-full bg-gradient-to-r from-amber-500 to-orange-500"></div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Enseignants affectés</p>
        <div class="flex items-end justify-between mt-2">
            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $teachersCount }}</p>
            <div
                class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                <i class="bi bi-person-workspace"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Classes liées</p>
        <div class="flex items-end justify-between mt-2">
            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $classesCount }}</p>
            <div
                class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <i class="bi bi-collection"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Notes récentes</p>
        <div class="flex items-end justify-between mt-2">
            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $grades->count() }}</p>
            <div
                class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                <i class="bi bi-star"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Moyenne matière</p>
        <div class="flex items-end justify-between mt-2">
            <p
                class="text-2xl font-bold {{ $avg >= 12 ? 'text-emerald-600 dark:text-emerald-400' : ($avg >= 10 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">
                {{ $avg ? number_format($avg,2).'/20' : '—' }}</p>
            <div
                class="w-8 h-8 rounded-lg bg-cyan-50 dark:bg-cyan-900/30 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                <i class="bi bi-graph-up"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    {{-- Assignments --}}
    <div
        class="lg:col-span-7 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div
            class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2"><i
                    class="bi bi-person-lines-fill text-blue-500"></i> Affectations</h3>
            <span
                class="text-xs px-2.5 py-1 rounded-full bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300">{{ $assignments->count() }}
                total</span>
        </div>
        @if($assignments->count())
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead
                    class="text-[11px] uppercase font-semibold tracking-wider text-slate-400 bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-5 py-3 text-left">Enseignant</th>
                        <th class="px-5 py-3 text-left">Classe</th>
                        <th class="px-5 py-3 text-left">Année</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @foreach($assignments as $assign)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-emerald-500 flex items-center justify-center text-white text-[10px] font-bold">
                                    {{ strtoupper(substr($assign->teacher->user->name ?? 'T',0,1)) }}</div>
                                <span
                                    class="font-medium text-slate-800 dark:text-slate-200">{{ $assign->teacher->user->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-300">{{ $assign->schoolClass->name ?? '—' }}
                        </td>
                        <td class="px-5 py-3"><span
                                class="px-2 py-1 rounded-full text-xs bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ $assign->academicYear->name ?? '—' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700">
            @foreach($assignments as $assign)
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200">
                        {{ $assign->teacher->user->name ?? '|' }}</p>
                    <p class="text-xs text-slate-500">{{ $assign->schoolClass->name ?? '|' }} •
                        {{ $assign->academicYear->name ?? '' }}</p>
                </div>
                <i class="bi bi-chevron-right text-slate-300 text-xs"></i>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-10 text-center"><i class="bi bi-inbox text-2xl text-slate-300"></i>
            <p class="text-sm text-slate-500 mt-2">Aucune affectation pour cette matière</p>
        </div>
        @endif
    </div>

    {{-- Recent Grades --}}
    <div
        class="lg:col-span-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden flex flex-col">
        <div
            class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2"><i
                    class="bi bi-award text-amber-500"></i> Dernières notes</h3>
            <a href="{{ route('grades.index') }}?subject_id={{ $subject->id }}"
                class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">Tout voir</a>
        </div>
        <div class="flex-1">
            @forelse($grades as $grade)
            <div
                class="p-4 flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors border-b border-slate-50 dark:border-slate-700/50 last:border-0">
                <div
                    class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-[10px] font-bold text-slate-600 dark:text-slate-300">
                    {{ strtoupper(substr($grade->student->user->name ?? 'E',0,1)) }}</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">
                        {{ $grade->student->user->name ?? 'Élève' }}</p>
                    <p class="text-xs text-slate-500">{{ $grade->term->name ?? $grade->type ?? 'Évaluation' }} •
                        {{ $grade->created_at->diffForHumans() }}</p>
                </div>
                <div class="text-right">
                    <p
                        class="text-sm font-bold {{ $grade->score >= 12 ? 'text-emerald-600 dark:text-emerald-400' : ($grade->score >= 10 ? 'text-amber-600' : 'text-red-500') }}">
                        {{ $grade->score }}/20</p>
                    <p class="text-[11px] text-slate-400">Coeff {{ $grade->coefficient ?? 1 }}</p>
                </div>
            </div>
            @empty
            <div class="py-16 text-center">
                <div
                    class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                    <i class="bi bi-journal-x text-xl text-slate-400"></i>
                </div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucune note</p>
                <p class="text-xs text-slate-400 mt-1">Les évaluations apparaîtront ici.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('delete-subject-btn');
    const form = document.getElementById('delete-subject-form');
    if (btn && form) {
        btn.addEventListener('click', () => {
            if (confirm('Supprimer cette matière ?')) form.submit();
        });
    }
});
</script>
@endsection