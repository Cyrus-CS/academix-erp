@extends('layouts.base')

@section('page_title', $teacher->user->name ?? 'Enseignant')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('teachers.index') }}"
    class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Enseignants
</a>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('teachers.index') }}" class="w-9 h-9 rounded-xl flex items-center justify-center
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-500 hover:text-blue-600 hover:border-blue-300 dark:hover:border-blue-600
                  transition-all duration-200">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
                Profil enseignant
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                {{ $teacher->user->name ?? 'Enseignant' }}
                • Mat. {{ $teacher->employee_number ?? '—' }}
            </p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('teacher-contracts.create') }}?teacher_id={{ $teacher->id }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200">
            <i class="bi bi-file-earmark-text"></i>
            <span class="hidden sm:inline">Nouveau contrat</span>
        </a>

        <a href="{{ route('teachers.edit', $teacher) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200">
            <i class="bi bi-pencil-square"></i>
            <span class="hidden sm:inline">Modifier</span>
        </a>

        <form id="delete-teacher-form" action="{{ route('teachers.destroy', $teacher) }}" method="POST">
            @csrf
            @method('DELETE')
            <button id="delete-teacher-btn" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                           bg-red-600 hover:bg-red-700 text-white
                           shadow-sm hover:shadow-red-500/20 transition-all duration-200">
                <i class="bi bi-trash3"></i>
                <span class="hidden sm:inline">Supprimer</span>
            </button>
        </form>
    </div>
</div>
@endsection

@section('content')
@php
$activeContract = $stats['active_contract'] ?? null;
$totalClasses = $stats['total_classes'] ?? 0;
$totalSubjects = $stats['total_subjects'] ?? 0;
$totalGrades = $stats['total_grades'] ?? 0;
$hasAvatar = $teacher->user?->avatar;
@endphp

{{-- ── Hero Profil ─────────────────────────────────────────────── --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
            dark:border-slate-700 shadow-sm overflow-hidden mb-6">

    {{-- Cover gradient --}}
    <div class="h-24 sm:h-32 bg-linear-to-r from-blue-600 via-blue-500 to-emerald-500 relative">
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'30\' height=\'30\' viewBox=\'0 0 30 30\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M15 0C6.716 0 0 6.716 0 15c0 8.284 6.716 15 15 15 8.284 0 15-6.716 15-15C30 6.716 23.284 0 15 0zm0 27C8.373 27 3 21.627 3 15S8.373 3 15 3s12 5.373 12 12-5.373 12-12 12z\' fill=\'%23ffffff\' fill-opacity=\'0.04\'/%3E%3C/svg%3E')]">
        </div>
    </div>

    <div class="px-5 sm:px-8 pb-6 sm:pb-8">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-10 sm:-mt-14">

            {{-- Avatar --}}
            @if($hasAvatar)
            <img src="{{ asset('storage/' . $teacher->user->avatar) }}" alt="{{ $teacher->user->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover
                            ring-4 ring-white dark:ring-slate-800 shadow-xl shrink-0">
            @else
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl shrink-0
                            bg-linear-to-br from-blue-600 to-emerald-500
                            flex items-center justify-center
                            text-white text-2xl font-black
                            ring-4 ring-white dark:ring-slate-800 shadow-xl">
                {{ strtoupper(substr($teacher->user->name ?? 'T', 0, 2)) }}
            </div>
            @endif

            {{-- Infos principales --}}
            <div class="flex-1 min-w-0 pt-2 sm:pt-0 sm:pb-1">
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
                        {{ $teacher->user->name ?? 'Enseignant' }}
                    </h2>

                    @if($activeContract)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                 bg-emerald-50 dark:bg-emerald-900/20
                                 text-emerald-700 dark:text-emerald-300
                                 border border-emerald-200 dark:border-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Contrat actif
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                 bg-slate-100 dark:bg-slate-700
                                 text-slate-500 dark:text-slate-400">
                        Aucun contrat actif
                    </span>
                    @endif
                </div>

                <p class="text-sm text-slate-500 dark:text-slate-400">
                    <span class="font-semibold text-slate-700 dark:text-slate-300">
                        {{ $teacher->qualification ?? 'Qualification non définie' }}
                    </span>
                    @if($teacher->nationality ?? $teacher->nationalité ?? null)
                    • {{ $teacher->nationality ?? $teacher->nationalité }}
                    @endif
                    @if($teacher->employee_number)
                    • Mat. <span class="font-mono font-semibold">{{ $teacher->employee_number }}</span>
                    @endif
                </p>
            </div>

            {{-- Actions profil --}}
            <div class="sm:pb-1 flex items-center gap-2">
                <a href="mailto:{{ $teacher->user->email }}" class="w-9 h-9 rounded-xl flex items-center justify-center
                          bg-slate-100 dark:bg-slate-700
                          text-slate-600 dark:text-slate-300
                          hover:bg-blue-50 dark:hover:bg-blue-900/20
                          hover:text-blue-600 dark:hover:text-blue-400
                          transition-all" title="Envoyer un email">
                    <i class="bi bi-envelope-fill text-sm"></i>
                </a>
            </div>
        </div>

        {{-- Infos contact --}}
        <div class="mt-5 flex flex-wrap gap-3 sm:gap-5 text-xs text-slate-500 dark:text-slate-400">
            <span class="inline-flex items-center gap-1.5">
                <i class="bi bi-envelope text-slate-400"></i>
                {{ $teacher->user->email ?? 'Aucun email' }}
            </span>
            @if($teacher->phone ?? null)
            <span class="inline-flex items-center gap-1.5">
                <i class="bi bi-telephone text-slate-400"></i>
                {{ $teacher->phone }}
            </span>
            @endif
            @if($teacher->address ?? null)
            <span class="inline-flex items-center gap-1.5">
                <i class="bi bi-geo-alt text-slate-400"></i>
                {{ $teacher->address }}
            </span>
            @endif
            <span class="inline-flex items-center gap-1.5">
                <i class="bi bi-calendar-plus text-slate-400"></i>
                Depuis {{ $teacher->created_at->format('M Y') }}
            </span>
        </div>
    </div>
    <div class="h-1 w-full bg-linear-to-r from-blue-600 to-emerald-500"></div>
</div>

{{-- ── Stats ─────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
    ['label' => 'Classes', 'value' => $totalClasses, 'sub' => 'Enseignées', 'icon' => 'bi-collection-fill', 'color' =>
    'blue'],
    ['label' => 'Matières', 'value' => $totalSubjects, 'sub' => 'Assignées', 'icon' => 'bi-journal-bookmark-fill',
    'color' => 'emerald'],
    ['label' => 'Notes saisies', 'value' => $totalGrades, 'sub' => 'Total évaluations', 'icon' => 'bi-star-fill',
    'color' => 'amber'],
    ['label' => 'Contrat', 'value' => $activeContract ? 'Actif' : 'Inactif', 'sub' => $activeContract ? 'En cours de
    validité' : 'Aucun contrat actif', 'icon' => 'bi-file-earmark-check-fill', 'color' => $activeContract ? 'emerald' :
    'red'],
    ] as $s)
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 p-5 shadow-sm">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                    {{ $s['label'] }}
                </p>
                <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1.5">
                    {{ $s['value'] }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $s['sub'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                {{ $s['color'] === 'blue'    ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : '' }}
                {{ $s['color'] === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : '' }}
                {{ $s['color'] === 'amber'   ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : '' }}
                {{ $s['color'] === 'red'     ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400' : '' }}">
                <i class="bi {{ $s['icon'] }} text-lg"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    {{-- Colonne gauche --}}
    <div class="lg:col-span-8 space-y-6">

        {{-- Affectations --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700
                        bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="bi bi-diagram-3-fill text-blue-500"></i>
                    Affectations d'enseignement
                    <span class="text-xs font-normal text-slate-500">({{ $teacher->assignments->count() }})</span>
                </h3>
                <a href="{{ route('teacher-assignments.create') }}?teacher_id={{ $teacher->id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                          bg-blue-600 hover:bg-blue-700 text-white transition-all">
                    <i class="bi bi-plus-lg"></i>
                    Ajouter
                </a>
            </div>

            @if($teacher->assignments->count())
            {{-- Desktop --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-[11px] uppercase font-semibold tracking-wider
                                      text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-5 py-3 text-left">Matière</th>
                            <th class="px-5 py-3 text-left">Classe</th>
                            <th class="px-5 py-3 text-left">Année</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @foreach($teacher->assignments as $assign)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-900/20
                                                    flex items-center justify-center shrink-0">
                                        <i
                                            class="bi bi-journal-bookmark text-amber-600 dark:text-amber-400 text-xs"></i>
                                    </div>
                                    <span class="font-medium text-slate-800 dark:text-slate-200">
                                        {{ $assign->subject->name ?? '—' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-300">
                                {{ $assign->schoolClass->name ?? '—' }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs
                                                 bg-slate-100 dark:bg-slate-700
                                                 text-slate-600 dark:text-slate-300">
                                    {{ $assign->academicYear->name ?? '—' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('teacher-assignments.show', $assign) }}" class="inline-flex w-7 h-7 items-center justify-center rounded-lg
                                              border border-slate-200 dark:border-slate-600
                                              text-slate-500 hover:text-blue-600
                                              hover:border-blue-300 dark:hover:border-blue-500
                                              transition-all">
                                    <i class="bi bi-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile --}}
            <div class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700">
                @foreach($teacher->assignments as $assign)
                <div class="p-4 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-900/20
                                    flex items-center justify-center shrink-0">
                        <i class="bi bi-journal-bookmark text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">
                            {{ $assign->subject->name ?? '—' }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ $assign->schoolClass->name ?? '—' }}
                            • {{ $assign->academicYear->name ?? '—' }}
                        </p>
                    </div>
                    <a href="{{ route('teacher-assignments.show', $assign) }}" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-700
                                  flex items-center justify-center text-slate-400
                                  hover:text-blue-600 transition-colors">
                        <i class="bi bi-chevron-right text-xs"></i>
                    </a>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-12 text-center">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-700
                                flex items-center justify-center mb-3">
                    <i class="bi bi-diagram-3 text-xl text-slate-400"></i>
                </div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    Aucune affectation
                </p>
                <p class="text-xs text-slate-400 mt-1">Cet enseignant n'est assigné à aucune classe.</p>
            </div>
            @endif
        </div>

        {{-- Dernières notes saisies --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700
                        bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="bi bi-award-fill text-amber-500"></i>
                    Dernières notes saisies
                </h3>
                <a href="{{ route('grades.index') }}?teacher_id={{ $teacher->id }}"
                    class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">
                    Tout voir
                </a>
            </div>

            @forelse($teacher->grades as $grade)
            @php
            $gScore = (float) ($grade->score ?? 0);
            $gClass = $gScore >= 12
            ? 'text-emerald-600 dark:text-emerald-400'
            : ($gScore >= 10 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400');
            @endphp
            <div class="px-5 py-3.5 flex items-center gap-3
                        border-b border-slate-100 dark:border-slate-700/50 last:border-0
                        hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                <div class="w-8 h-8 rounded-full shrink-0
                            bg-linear-to-br from-blue-500 to-emerald-500
                            flex items-center justify-center text-white text-[10px] font-bold">
                    {{ strtoupper(substr($grade->student->user->name ?? 'E', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">
                        {{ $grade->student->user->name ?? 'Élève' }}
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                        {{ $grade->subject->name ?? '—' }} • {{ $grade->created_at->diffForHumans() }}
                    </p>
                </div>
                <span class="text-sm font-bold {{ $gClass }}">
                    {{ number_format($gScore, 2) }}/20
                </span>
            </div>
            @empty
            <div class="py-10 text-center">
                <i class="bi bi-journal-x text-2xl text-slate-300 dark:text-slate-600"></i>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Aucune note saisie</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Colonne droite --}}
    <div class="lg:col-span-4 space-y-6">

        {{-- Contrat actif --}}
        @if($activeContract)
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700
                        bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="bi bi-file-earmark-check-fill text-emerald-500"></i>
                    Contrat actif
                </h3>
            </div>
            <div class="p-5 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">
                        {{ ucfirst($activeContract->contract_type ?? 'CDI') }}
                    </p>
                </div>

                @foreach([
                ['icon' => 'bi-calendar-plus', 'label' => 'Début', 'value' => $activeContract->start_date ?
                \Carbon\Carbon::parse($activeContract->start_date)->format('d/m/Y') : '—'],
                ['icon' => 'bi-calendar-x', 'label' => 'Fin', 'value' => $activeContract->end_date ?
                \Carbon\Carbon::parse($activeContract->end_date)->format('d/m/Y') : 'Indéterminé'],
                ['icon' => 'bi-cash', 'label' => 'Salaire', 'value' => $activeContract->salary ?
                number_format($activeContract->salary, 0, ',', ' ').' FCFA' : '—'],
                ] as $cInfo)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/20
                                flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                        <i class="bi {{ $cInfo['icon'] }} text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">
                            {{ $cInfo['label'] }}
                        </p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                            {{ $cInfo['value'] }}
                        </p>
                    </div>
                </div>
                @endforeach

                <a href="{{ route('teacher-contracts.show', $activeContract) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                          text-sm font-medium
                          bg-emerald-50 dark:bg-emerald-900/20
                          border border-emerald-200 dark:border-emerald-800
                          text-emerald-700 dark:text-emerald-300
                          hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-all">
                    <i class="bi bi-eye"></i>
                    Voir le contrat
                </a>
            </div>
        </div>
        @else
        <div class="rounded-2xl p-5 border border-dashed border-slate-300 dark:border-slate-600 text-center">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-700
                        flex items-center justify-center mb-3">
                <i class="bi bi-file-earmark-x text-xl text-slate-400"></i>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucun contrat actif</p>
            <p class="text-xs text-slate-400 mt-1 mb-4">Cet enseignant n'a pas de contrat en cours.</p>
            <a href="{{ route('teacher-contracts.create') }}?teacher_id={{ $teacher->id }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold
                      bg-blue-600 hover:bg-blue-700 text-white transition-all">
                <i class="bi bi-plus-circle"></i>
                Créer un contrat
            </a>
        </div>
        @endif

        {{-- Historique contrats --}}
        @if($teacher->contracts->count())
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700
                        bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="bi bi-clock-history text-cyan-500"></i>
                    Historique contrats
                </h3>
                <a href="{{ route('teacher-contracts.index') }}?teacher_id={{ $teacher->id }}"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                    Tout voir
                </a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
                @foreach($teacher->contracts as $contract)
                @php
                $cStatus = strtolower($contract->status ?? 'inactive');
                $cColor = match($cStatus) {
                'active' => 'text-emerald-600 dark:text-emerald-400',
                'expired' => 'text-slate-400 dark:text-slate-500',
                'cancelled' => 'text-red-500 dark:text-red-400',
                default => 'text-amber-500 dark:text-amber-400',
                };
                $cLabel = match($cStatus) {
                'active' => 'Actif',
                'expired' => 'Expiré',
                'cancelled' => 'Annulé',
                default => ucfirst($cStatus),
                };
                @endphp
                <div class="p-4 flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700
                                flex items-center justify-center shrink-0">
                        <i class="bi bi-file-earmark-text text-slate-500 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">
                            {{ ucfirst($contract->contract_type ?? 'Contrat') }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') : '—' }}
                        </p>
                    </div>
                    <span class="text-xs font-semibold {{ $cColor }}">{{ $cLabel }}</span>
                    <a href="{{ route('teacher-contracts.show', $contract) }}" class="w-7 h-7 rounded-lg flex items-center justify-center
                              border border-slate-200 dark:border-slate-600
                              text-slate-500 hover:text-blue-600 transition-all">
                        <i class="bi bi-eye text-xs"></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Liens rapides --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm p-4 space-y-2">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
                Actions rapides
            </p>
            @foreach([
            ['route' => 'grades.create', 'params' => ['teacher_id' => $teacher->id], 'icon' => 'bi-plus-circle', 'label'
            => 'Saisir une note', 'color' => 'amber'],
            ['route' => 'attendance.create', 'params' => [], 'icon' => 'bi-calendar-check', 'label' => 'Marquer
            présence', 'color' => 'blue'],
            ['route' => 'teacher-assignments.create', 'params' => ['teacher_id' => $teacher->id], 'icon' =>
            'bi-diagram-3', 'label' => 'Nouvelle affectation', 'color' => 'emerald'],
            ] as $action)
            <a href="{{ route($action['route']) }}{{ count($action['params']) ? '?'.http_build_query($action['params']) : '' }}"
                class="flex items-center gap-3 p-3 rounded-xl text-sm
                      border border-slate-100 dark:border-slate-700
                      text-slate-700 dark:text-slate-300
                      hover:bg-{{ $action['color'] }}-50 dark:hover:bg-{{ $action['color'] }}-950/20
                      hover:border-{{ $action['color'] }}-200 dark:hover:border-{{ $action['color'] }}-800
                      hover:text-{{ $action['color'] }}-600 dark:hover:text-{{ $action['color'] }}-400
                      transition-all duration-200 group">
                <i class="bi {{ $action['icon'] }} text-base"></i>
                {{ $action['label'] }}
                <i class="bi bi-arrow-right-short ml-auto opacity-0 group-hover:opacity-100 transition-opacity"></i>
            </a>
            @endforeach
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('delete-teacher-btn');
    const form = document.getElementById('delete-teacher-form');

    btn?.addEventListener('click', () => {
        if (confirm(
                'Supprimer définitivement cet enseignant ?\n\n' +
                'Ses affectations, notes et contrats seront également affectés.'
            )) {
            form.submit();
        }
    });
});
</script>
@endsection