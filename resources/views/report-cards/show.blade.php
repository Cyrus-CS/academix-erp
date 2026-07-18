@extends('layouts.base')

@section('page_title', 'Bulletin scolaire')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('report-cards.index') }}"
    class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Bulletins
</a>
@endsection

@section('page_header')
<div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('report-cards.index') }}" class="w-9 h-9 rounded-xl flex items-center justify-center
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-500 hover:text-blue-600 hover:border-blue-300 dark:hover:border-blue-600
                  transition-all duration-200">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
                Bulletin scolaire
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                {{ $reportCard->student->user->name ?? 'Élève' }} • {{ $reportCard->term->name ?? 'Trimestre' }}
            </p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        @hasanyrole('Admin|Teacher')
        <a href="{{ route('report-cards.edit', $reportCard) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200">
            <i class="bi bi-pencil-square"></i>
            <span class="hidden sm:inline">Modifier</span>
        </a>
        @endhasanyrole

        <a href="{{ $downloadUrl }}" target="_blank" rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  bg-blue-600 hover:bg-blue-700 text-white shadow-sm hover:shadow-blue-500/20 transition-all duration-200">
            <i class="bi bi-download"></i>
            <span>Télécharger PDF</span>
        </a>
    </div>
</div>
@endsection

@section('content')
@php
$average = (float) ($reportCard->average ?? $reportCard->grades->avg('score') ?? 0);
$gradesCount = $reportCard->grades->count();
$bestGrade = $reportCard->grades->max('score');
$lowestGrade = $reportCard->grades->min('score');
$rank = $reportCard->rank ?? $reportCard->position ?? null;
$mention = $reportCard->mention ?? (
$average >= 16 ? 'Excellent' :
($average >= 14 ? 'Très bien' :
($average >= 12 ? 'Bien' :
($average >= 10 ? 'Passable' : 'Insuffisant')))
);
$decision = $reportCard->decision ?? ($average >= 10 ? 'Admis' : 'À renforcer');
$progress = max(0, min(100, round(($average / 20) * 100)));

if ($average >= 16) {
$theme = 'emerald';
} elseif ($average >= 14) {
$theme = 'blue';
} elseif ($average >= 12) {
$theme = 'cyan';
} elseif ($average >= 10) {
$theme = 'amber';
} else {
$theme = 'red';
}

$themes = [
'emerald' => [
'text' => 'text-emerald-700 dark:text-emerald-300',
'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',
'border' => 'border-emerald-200 dark:border-emerald-800',
'bar' => 'bg-emerald-500',
'grad' => 'from-emerald-500 to-teal-500',
],
'blue' => [
'text' => 'text-blue-700 dark:text-blue-300',
'bg' => 'bg-blue-50 dark:bg-blue-900/20',
'border' => 'border-blue-200 dark:border-blue-800',
'bar' => 'bg-blue-500',
'grad' => 'from-blue-500 to-cyan-500',
],
'cyan' => [
'text' => 'text-cyan-700 dark:text-cyan-300',
'bg' => 'bg-cyan-50 dark:bg-cyan-900/20',
'border' => 'border-cyan-200 dark:border-cyan-800',
'bar' => 'bg-cyan-500',
'grad' => 'from-cyan-500 to-sky-500',
],
'amber' => [
'text' => 'text-amber-700 dark:text-amber-300',
'bg' => 'bg-amber-50 dark:bg-amber-900/20',
'border' => 'border-amber-200 dark:border-amber-800',
'bar' => 'bg-amber-500',
'grad' => 'from-amber-500 to-orange-500',
],
'red' => [
'text' => 'text-red-700 dark:text-red-300',
'bg' => 'bg-red-50 dark:bg-red-900/20',
'border' => 'border-red-200 dark:border-red-800',
'bar' => 'bg-red-500',
'grad' => 'from-red-500 to-rose-500',
],
];

$cfg = $themes[$theme];
@endphp

{{-- Hero bulletin --}}
<div
    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden mb-6">
    <div class="p-6 sm:p-8">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="xl:col-span-8">
                <div class="flex flex-col sm:flex-row gap-5">
                    <div
                        class="w-18 h-18 rounded-3xl border shadow-lg shrink-0 flex items-center justify-center {{ $cfg['bg'] }} {{ $cfg['border'] }}">
                        <div class="text-center">
                            <p class="text-2xl font-black {{ $cfg['text'] }}">{{ number_format($average, 2) }}</p>
                            <p class="text-[10px] font-semibold text-slate-500">/20</p>
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2.5 mb-3">
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border {{ $cfg['bg'] }} {{ $cfg['border'] }} {{ $cfg['text'] }}">
                                <i class="bi bi-award-fill"></i>
                                {{ $mention }}
                            </span>

                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                                         {{ $average >= 10 ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white' }}">
                                <i
                                    class="bi {{ $average >= 10 ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
                                {{ $decision }}
                            </span>

                            @if($rank)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium
                                         bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300
                                         border border-slate-200 dark:border-slate-600">
                                <i class="bi bi-trophy-fill text-amber-500"></i>
                                Rang : {{ $rank }}
                            </span>
                            @endif
                        </div>

                        <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                            {{ $reportCard->student->user->name ?? 'Élève' }}
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            {{ $reportCard->student->classe->name ?? 'Classe non définie' }} •
                            {{ $reportCard->term->name ?? 'Trimestre' }} •
                            {{ $reportCard->term->academicYear->name ?? 'Année académique' }}
                        </p>

                        <div class="mt-5">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-medium text-slate-500 dark:text-slate-400">Performance globale</span>
                                <span class="font-bold {{ $cfg['text'] }}">{{ $progress }}%</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700 {{ $cfg['bar'] }}"
                                    style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-4">
                <div class="h-full rounded-2xl border border-slate-200 dark:border-slate-700
                            bg-linear-to-br from-slate-50 to-blue-50/40 dark:from-slate-900 dark:to-blue-950/20
                            p-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">
                        Résumé du bulletin
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                                <i class="bi bi-journal-check text-blue-500"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-500">Matières évaluées</p>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $gradesCount }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                                <i class="bi bi-arrow-up-right-circle text-emerald-500"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-500">Meilleure note</p>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    {{ $bestGrade !== null ? number_format((float) $bestGrade, 2).'/20' : '—' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                                <i class="bi bi-arrow-down-right-circle text-red-500"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-500">Plus faible note</p>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    {{ $lowestGrade !== null ? number_format((float) $lowestGrade, 2).'/20' : '—' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                                <i class="bi bi-shield-lock text-cyan-500"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-500">Lien sécurisé PDF</p>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    Temporaire
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 rounded-xl {{ $cfg['bg'] }} {{ $cfg['border'] }} border p-4">
                        <p class="text-xs font-semibold {{ $cfg['text'] }} mb-1">Téléchargement sécurisé</p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                            Le lien de téléchargement du bulletin PDF est signé temporairement pour renforcer la
                            sécurité d'accès.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="h-1 bg-linear-to-r {{ $cfg['grad'] }}"></div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Moyenne générale</p>
        <p class="text-2xl font-bold mt-2 {{ $cfg['text'] }}">{{ number_format($average, 2) }}/20</p>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $mention }}</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Décision</p>
        <p
            class="text-2xl font-bold mt-2 {{ $average >= 10 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
            {{ $decision }}</p>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Synthèse trimestrielle</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Rang</p>
        <p class="text-2xl font-bold mt-2 text-slate-800 dark:text-slate-100">{{ $rank ?? '—' }}</p>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Position de l'élève</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Matières</p>
        <p class="text-2xl font-bold mt-2 text-slate-800 dark:text-slate-100">{{ $gradesCount }}</p>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Éléments évalués</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    {{-- Notes détaillées --}}
    <div
        class="lg:col-span-8 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div
            class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <i class="bi bi-table text-blue-500"></i>
                Détail des notes
            </h3>
            <span class="text-xs text-slate-500 dark:text-slate-400">{{ $gradesCount }} matière(s)</span>
        </div>

        @if($reportCard->grades->count())
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead
                    class="text-[11px] uppercase font-semibold tracking-wider text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-5 py-3 text-left">Matière</th>
                        <th class="px-5 py-3 text-left">Coefficient</th>
                        <th class="px-5 py-3 text-left">Note</th>
                        <th class="px-5 py-3 text-left">Performance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @foreach($reportCard->grades as $grade)
                    @php
                    $gScore = (float) ($grade->score ?? 0);
                    $gPct = max(0, min(100, round(($gScore / 20) * 100)));
                    $gCoef = $grade->coefficient ?? 1;
                    $gClass = $gScore >= 12
                    ? 'text-emerald-600 dark:text-emerald-400'
                    : ($gScore >= 10 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400');
                    $gBar = $gScore >= 12
                    ? 'bg-emerald-500'
                    : ($gScore >= 10 ? 'bg-amber-500' : 'bg-red-500');
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-medium text-slate-800 dark:text-slate-200">
                                {{ $grade->subject->name ?? 'Matière' }}</p>
                        </td>
                        <td class="px-5 py-3">
                            <span
                                class="px-2.5 py-1 rounded-full text-xs bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                {{ $gCoef }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="font-bold {{ $gClass }}">{{ number_format($gScore, 2) }}/20</span>
                        </td>
                        <td class="px-5 py-3 min-w-44">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full {{ $gBar }} rounded-full" style="width: {{ $gPct }}%"></div>
                                </div>
                                <span
                                    class="text-xs font-semibold text-slate-500 dark:text-slate-400 w-10 text-right">{{ $gPct }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700">
            @foreach($reportCard->grades as $grade)
            @php
            $gScore = (float) ($grade->score ?? 0);
            $gPct = max(0, min(100, round(($gScore / 20) * 100)));
            $gClass = $gScore >= 12
            ? 'text-emerald-600 dark:text-emerald-400'
            : ($gScore >= 10 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400');
            $gBar = $gScore >= 12
            ? 'bg-emerald-500'
            : ($gScore >= 10 ? 'bg-amber-500' : 'bg-red-500');
            @endphp
            <div class="p-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">
                            {{ $grade->subject->name ?? 'Matière' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Coeff {{ $grade->coefficient ?? 1 }}</p>
                    </div>
                    <p class="text-sm font-bold {{ $gClass }}">{{ number_format($gScore, 2) }}/20</p>
                </div>
                <div class="mt-3 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full {{ $gBar }}" style="width: {{ $gPct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-16 text-center">
            <div
                class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                <i class="bi bi-journal-x text-xl text-slate-400"></i>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucune note disponible</p>
            <p class="text-xs text-slate-400 mt-1">Le détail des évaluations apparaîtra ici.</p>
        </div>
        @endif
    </div>

    {{-- Sidebar infos --}}
    <div class="lg:col-span-4 space-y-6">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="bi bi-person-fill text-emerald-500"></i>
                    Élève
                </h3>
            </div>

            <div class="p-5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 rounded-full bg-linear-to-br from-blue-600 to-emerald-500 flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($reportCard->student->user->name ?? 'E', 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                            {{ $reportCard->student->user->name ?? 'Élève' }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                            {{ $reportCard->student->classe->name ?? 'Classe non définie' }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <i class="bi bi-envelope text-slate-400"></i>
                        <span class="truncate">{{ $reportCard->student->user->email ?? 'Aucun email' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <i class="bi bi-collection text-slate-400"></i>
                        <span>{{ $reportCard->student->classe->name ?? 'Classe non définie' }}</span>
                    </div>
                </div>

                <a href="{{ route('students.show', $reportCard->student) }}" class="mt-5 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                          bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
                    <i class="bi bi-eye"></i>
                    Voir le profil élève
                </a>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="bi bi-calendar-range text-cyan-500"></i>
                    Période académique
                </h3>
            </div>

            <div class="p-5 space-y-4">
                <div>
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Trimestre</p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        {{ $reportCard->term->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Année académique
                    </p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        {{ $reportCard->term->academicYear->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Créé le</p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        {{ $reportCard->created_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection