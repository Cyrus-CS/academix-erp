@extends('layouts.base')

@section('page_title', 'Détail de présence')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('attendance.index') }}"
    class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Présences
</a>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('attendance.index') }}" class="w-9 h-9 rounded-xl flex items-center justify-center
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-500 hover:text-blue-600 hover:border-blue-300 dark:hover:border-blue-600
                  transition-all duration-200">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
                Détail de présence
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                {{ $attendance->student->user->name ?? 'Élève' }} • {{ $attendance->classe->name ?? 'Classe' }}
            </p>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('attendance.edit', $attendance) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200">
            <i class="bi bi-pencil-square"></i>
            <span class="hidden sm:inline">Modifier</span>
        </a>

        <form id="delete-attendance-form" action="{{ route('attendance.destroy', $attendance) }}" method="POST">
            @csrf
            @method('DELETE')
            <button id="delete-attendance-btn" type="button"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                           bg-red-600 hover:bg-red-700 text-white shadow-sm hover:shadow-red-500/20 transition-all duration-200">
                <i class="bi bi-trash3"></i>
                <span class="hidden sm:inline">Supprimer</span>
            </button>
        </form>
    </div>
</div>
@endsection

@section('content')
@php
$status = strtolower($attendance->status ?? 'unknown');

$statusMap = [
'present' => [
'label' => 'Présent',
'icon' => 'bi-check-circle-fill',
'text' => 'text-emerald-700 dark:text-emerald-300',
'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',
'border' => 'border-emerald-200 dark:border-emerald-800',
'badge' => 'bg-emerald-600 text-white',
],
'absent' => [
'label' => 'Absent',
'icon' => 'bi-x-circle-fill',
'text' => 'text-red-700 dark:text-red-300',
'bg' => 'bg-red-50 dark:bg-red-900/20',
'border' => 'border-red-200 dark:border-red-800',
'badge' => 'bg-red-600 text-white',
],
'late' => [
'label' => 'En retard',
'icon' => 'bi-clock-fill',
'text' => 'text-amber-700 dark:text-amber-300',
'bg' => 'bg-amber-50 dark:bg-amber-900/20',
'border' => 'border-amber-200 dark:border-amber-800',
'badge' => 'bg-amber-500 text-white',
],
];

$cfg = $statusMap[$status] ?? [
'label' => ucfirst($attendance->status ?? 'Non défini'),
'icon' => 'bi-info-circle-fill',
'text' => 'text-cyan-700 dark:text-cyan-300',
'bg' => 'bg-cyan-50 dark:bg-cyan-900/20',
'border' => 'border-cyan-200 dark:border-cyan-800',
'badge' => 'bg-cyan-500 text-white',
];

$attendanceDate = $attendance->date
? \Carbon\Carbon::parse($attendance->date)
: $attendance->created_at;
@endphp

{{-- Hero --}}
<div
    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden mb-6">
    <div class="p-6 sm:p-8">
        <div class="flex flex-col lg:flex-row gap-6 lg:items-start">
            <div
                class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg shrink-0 {{ $cfg['bg'] }} border {{ $cfg['border'] }}">
                <i class="bi {{ $cfg['icon'] }} text-2xl {{ $cfg['text'] }}"></i>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2.5 mb-3">
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold {{ $cfg['badge'] }}">
                        <i class="bi {{ $cfg['icon'] }}"></i>
                        {{ $cfg['label'] }}
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium
                                 bg-slate-100 dark:bg-slate-700
                                 text-slate-600 dark:text-slate-300
                                 border border-slate-200 dark:border-slate-600">
                        <i class="bi bi-calendar3"></i>
                        {{ $attendanceDate->format('d/m/Y') }}
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium
                                 bg-blue-50 dark:bg-blue-900/20
                                 text-blue-700 dark:text-blue-300
                                 border border-blue-200 dark:border-blue-800">
                        <i class="bi bi-collection-fill"></i>
                        {{ $attendance->classe->name ?? 'Classe non définie' }}
                    </span>
                </div>

                <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                    {{ $attendance->student->user->name ?? 'Élève inconnu' }}
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Suivi individuel de la présence journalière de l'élève.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
                    <div
                        class="rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 p-4">
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Statut</p>
                        <p class="text-sm font-bold mt-1 {{ $cfg['text'] }}">{{ $cfg['label'] }}</p>
                    </div>
                    <div
                        class="rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 p-4">
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Date</p>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-1">
                            {{ $attendanceDate->format('d M Y') }}</p>
                    </div>
                    <div
                        class="rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 p-4">
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Enregistré</p>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-1">
                            {{ $attendance->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        class="h-1 w-full {{ $status === 'present' ? 'bg-emerald-500' : ($status === 'absent' ? 'bg-red-500' : ($status === 'late' ? 'bg-amber-500' : 'bg-cyan-500')) }}">
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    {{-- Main Details --}}
    <div class="lg:col-span-8 space-y-6">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div
                class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="bi bi-card-checklist text-blue-500"></i>
                    Informations de présence
                </h3>
            </div>

            <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1">
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Élève</p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        {{ $attendance->student->user->name ?? '—' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $attendance->student->user->email ?? 'Aucun email' }}</p>
                </div>

                <div class="space-y-1">
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Classe</p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        {{ $attendance->classe->name ?? '—' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $attendance->classe->level ?? 'Niveau non défini' }}</p>
                </div>

                <div class="space-y-1">
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Date de présence</p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        {{ $attendanceDate->format('d/m/Y') }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $attendanceDate->locale('fr')->translatedFormat('l') }}</p>
                </div>

                <div class="space-y-1">
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Statut</p>
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $cfg['bg'] }} {{ $cfg['text'] }} border {{ $cfg['border'] }}">
                        <i class="bi {{ $cfg['icon'] }}"></i>
                        {{ $cfg['label'] }}
                    </span>
                </div>

                <div class="md:col-span-2">
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-1.5">Observation</p>
                    <div
                        class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 p-4 min-h-24">
                        <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                            {{ $attendance->remarks ?? $attendance->note ?? $attendance->comment ?? 'Aucune observation renseignée pour cet enregistrement.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Interpretation --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 sm:p-6">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4">
                <i class="bi bi-info-circle text-cyan-500"></i>
                Interprétation du statut
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div
                    class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/10 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="bi bi-check-circle-fill text-emerald-500"></i>
                        <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">Présent</p>
                    </div>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 leading-relaxed">
                        L'élève a été effectivement présent au cours ou à l'activité.
                    </p>
                </div>

                <div class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/10 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="bi bi-x-circle-fill text-red-500"></i>
                        <p class="text-sm font-semibold text-red-700 dark:text-red-300">Absent</p>
                    </div>
                    <p class="text-xs text-red-600 dark:text-red-400 leading-relaxed">
                        L'élève était absent durant la session concernée.
                    </p>
                </div>

                <div
                    class="rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/10 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="bi bi-clock-fill text-amber-500"></i>
                        <p class="text-sm font-semibold text-amber-700 dark:text-amber-300">Retard</p>
                    </div>
                    <p class="text-xs text-amber-600 dark:text-amber-400 leading-relaxed">
                        L'élève est arrivé après le début prévu de la session.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="lg:col-span-4 space-y-6">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="bi bi-person-badge text-emerald-500"></i>
                    Profil élève
                </h3>
            </div>

            <div class="p-5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 rounded-full bg-linear-to-br from-blue-600 to-emerald-500 flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($attendance->student->user->name ?? 'E', 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                            {{ $attendance->student->user->name ?? 'Élève' }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                            {{ $attendance->student->admission_number ?? $attendance->student->matricule ?? 'Matricule non défini' }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <i class="bi bi-envelope text-slate-400"></i>
                        <span class="truncate">{{ $attendance->student->user->email ?? 'Aucun email' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <i class="bi bi-collection text-slate-400"></i>
                        <span>{{ $attendance->classe->name ?? 'Classe non définie' }}</span>
                    </div>
                </div>

                <a href="{{ route('students.show', $attendance->student) }}" class="mt-5 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                          bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
                    <i class="bi bi-eye"></i>
                    Voir le profil élève
                </a>
            </div>
        </div>

        <div class="rounded-2xl p-5 border {{ $cfg['border'] }} {{ $cfg['bg'] }}">
            <div class="flex items-start gap-3">
                <div
                    class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/70 dark:bg-slate-900/30 shrink-0">
                    <i class="bi {{ $cfg['icon'] }} {{ $cfg['text'] }} text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-bold {{ $cfg['text'] }}">
                        Enregistrement : {{ $cfg['label'] }}
                    </p>
                    <p class="text-xs mt-1 {{ $cfg['text'] }} opacity-90 leading-relaxed">
                        Cette fiche confirme le statut de présence de l'élève pour la date indiquée.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('delete-attendance-btn');
    const form = document.getElementById('delete-attendance-form');

    if (btn && form) {
        btn.addEventListener('click', () => {
            if (confirm('Supprimer cet enregistrement de présence ?')) {
                form.submit();
            }
        });
    }
});
</script>
@endsection