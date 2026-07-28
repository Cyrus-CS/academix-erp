{{-- resources/views/teacher-contracts/show.blade.php --}}

@extends('layouts.base')

@php
$contract = $teacherContract;

$teacher = $contract->teacher;
$user = $teacher?->user;

$teacherName = $user?->name ?? 'Enseignant non renseigné';
$teacherEmail = $user?->email;
$teacherAvatar = $user?->avatar ?? null;

$employeeNumber = data_get($teacher, 'employee_number', '—');
$qualification = data_get($teacher, 'qualification');
$nationality = data_get($teacher, 'nationality') ?? data_get($teacher, 'nationalité');
$teacherStatus = data_get($teacher, 'status');

$contractNumber = $contract->contract_number ?: 'CTR-' . str_pad((string) $contract->id, 5, '0', STR_PAD_LEFT);

$startDate = $contract->start_date
? \Illuminate\Support\Carbon::parse($contract->start_date)
: null;

$endDate = $contract->end_date
? \Illuminate\Support\Carbon::parse($contract->end_date)
: null;

$now = now();

$typeMap = [
'permanent' => [
'label' => 'Permanent',
'icon' => 'bi-shield-check',
'badge' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-900/60',
],
'temporary' => [
'label' => 'Temporaire',
'icon' => 'bi-calendar-range',
'badge' => 'bg-cyan-50 text-cyan-700 border-cyan-200 dark:bg-cyan-950/40 dark:text-cyan-300 dark:border-cyan-900/60',
],
'part_time' => [
'label' => 'Temps partiel',
'icon' => 'bi-clock-history',
'badge' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300
dark:border-amber-900/60',
],
'internship' => [
'label' => 'Stage',
'icon' => 'bi-mortarboard',
'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300
dark:border-emerald-900/60',
],
];

$statusMap = [
'active' => [
'label' => 'Actif',
'icon' => 'bi-check-circle-fill',
'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300
dark:border-emerald-900/60',
'dot' => 'bg-emerald-500',
],
'expired' => [
'label' => 'Expiré',
'icon' => 'bi-calendar-x-fill',
'badge' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/40 dark:text-red-300 dark:border-red-900/60',
'dot' => 'bg-red-500',
],
'terminated' => [
'label' => 'Résilié',
'icon' => 'bi-x-octagon-fill',
'badge' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
'dot' => 'bg-slate-500',
],
];

$typeCfg = $typeMap[$contract->contract_type] ?? [
'label' => ucfirst((string) $contract->contract_type),
'icon' => 'bi-file-earmark-text',
'badge' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
];

$statusCfg = $statusMap[$contract->status] ?? [
'label' => ucfirst((string) $contract->status),
'icon' => 'bi-info-circle-fill',
'badge' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
'dot' => 'bg-slate-400',
];

$isExpiringSoon = $contract->status === 'active'
&& $endDate
&& $endDate->isFuture()
&& $endDate->lte($now->copy()->addDays(30));

$isExpiredByDate = $endDate && $endDate->isPast();

$currency = config('school.currency', 'FCFA');
$salary = number_format((float) $contract->salary, 0, ',', ' ') . ' ' . $currency;

$durationLabel = 'Indéterminée';

if ($startDate && $endDate) {
$months = (int) $startDate->diffInMonths($endDate);
$days = (int) $startDate->diffInDays($endDate);

$durationLabel = $months >= 1
? $months . ' mois'
: $days . ' jour' . ($days > 1 ? 's' : '');
}

$progress = null;

if ($startDate && $endDate) {
$totalDays = max(1, (int) $startDate->diffInDays($endDate));

if ($now->lt($startDate)) {
$elapsedDays = 0;
} elseif ($now->gt($endDate)) {
$elapsedDays = $totalDays;
} else {
$elapsedDays = (int) $startDate->diffInDays($now);
}

$progress = min(100, max(0, (int) round(($elapsedDays / $totalDays) * 100)));
}

$documentPath = $contract->contract_pdf_path;
$documentUrl = null;
$documentExists = false;
$documentExtension = null;
$documentName = null;

if ($documentPath) {
$documentUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($documentPath);
$documentExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($documentPath);
$documentExtension = strtoupper(pathinfo($documentPath, PATHINFO_EXTENSION));
$documentName = basename($documentPath);
}

$nameParts = preg_split('/\s+/', trim($teacherName));
$initials = strtoupper(substr($nameParts[0] ?? 'E', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
@endphp

@section('page_title', 'Contrat ' . $contractNumber)

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('teacher-contracts.index') }}"
    class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Contrats enseignants
</a>
@endsection

@section('page_header')
<div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
    <div>
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full
                        bg-blue-50 dark:bg-blue-950/40
                        border border-blue-100 dark:border-blue-900/60
                        text-blue-700 dark:text-blue-300 text-xs font-semibold mb-3">
            <i class="bi bi-briefcase-fill"></i>
            Ressources humaines
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">
            Détails du contrat
        </h1>

        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400 max-w-2xl">
            Consultation complète du contrat enseignant, des informations administratives,
            de la période d’engagement et du document associé.
        </p>
    </div>

    <div class="flex flex-wrap items-center gap-2 print:hidden">
        <a href="{{ route('teacher-contracts.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                      border border-slate-200 dark:border-slate-700
                      bg-white dark:bg-slate-800
                      text-slate-700 dark:text-slate-200
                      hover:bg-slate-50 dark:hover:bg-slate-700/70
                      transition-all duration-200">
            <i class="bi bi-arrow-left"></i>
            Retour
        </a>

        <button type="button" onclick="window.print()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                           border border-slate-200 dark:border-slate-700
                           bg-white dark:bg-slate-800
                           text-slate-700 dark:text-slate-200
                           hover:bg-slate-50 dark:hover:bg-slate-700/70
                           transition-all duration-200">
            <i class="bi bi-printer"></i>
            Imprimer
        </button>

        <a href="{{ route('teacher-contracts.edit', $contract) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                      bg-blue-600 text-white
                      hover:bg-blue-700
                      shadow-sm shadow-blue-600/20
                      transition-all duration-200">
            <i class="bi bi-pencil-square"></i>
            Modifier
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Alertes métier --}}
    @if($isExpiringSoon || ($contract->status === 'active' && $isExpiredByDate))
    <div
        class="rounded-2xl border px-4 py-3.5 flex items-start gap-3
                        {{ $isExpiredByDate
                            ? 'bg-red-50 border-red-200 text-red-700 dark:bg-red-950/30 dark:border-red-900/60 dark:text-red-300'
                            : 'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-950/30 dark:border-amber-900/60 dark:text-amber-300' }}">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0
                            {{ $isExpiredByDate
                                ? 'bg-red-100 dark:bg-red-900/50'
                                : 'bg-amber-100 dark:bg-amber-900/50' }}">
            <i class="bi {{ $isExpiredByDate ? 'bi-exclamation-octagon-fill' : 'bi-exclamation-triangle-fill' }}"></i>
        </div>

        <div class="min-w-0">
            <p class="text-sm font-semibold">
                {{ $isExpiredByDate ? 'Date de fin dépassée' : 'Contrat proche de l’expiration' }}
            </p>
            <p class="text-sm mt-0.5 opacity-90">
                @if($isExpiredByDate)
                Ce contrat est toujours marqué comme actif, mais sa date de fin est déjà passée.
                @else
                Ce contrat arrive à échéance le {{ $endDate->format('d/m/Y') }}.
                @endif
            </p>
        </div>
    </div>
    @endif

    {{-- Hero contrat --}}
    <section class="relative overflow-hidden rounded-2xl
                        bg-white dark:bg-slate-800
                        border border-slate-200 dark:border-slate-700
                        shadow-sm">
        <div class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-blue-600 via-cyan-500 to-emerald-500"></div>

        <div class="p-5 sm:p-6">
            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-5">

                <div class="flex items-start gap-4 min-w-0">
                    <div class="w-14 h-14 rounded-2xl
                                    bg-linear-to-br from-blue-600 to-emerald-500
                                    flex items-center justify-center text-white shadow-md shrink-0">
                        <i class="bi bi-file-earmark-text-fill text-2xl"></i>
                    </div>

                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-xs font-semibold {{ $statusCfg['badge'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $statusCfg['dot'] }}"></span>
                                {{ $statusCfg['label'] }}
                            </span>

                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-xs font-semibold {{ $typeCfg['badge'] }}">
                                <i class="bi {{ $typeCfg['icon'] }}"></i>
                                {{ $typeCfg['label'] }}
                            </span>

                            @if($isExpiringSoon)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-xs font-semibold
                                                 bg-amber-50 text-amber-700 border-amber-200
                                                 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-900/60">
                                <i class="bi bi-hourglass-split"></i>
                                Expire bientôt
                            </span>
                            @endif
                        </div>

                        <h2 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100 truncate">
                            Contrat n° {{ $contractNumber }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Affecté à
                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $teacherName }}</span>
                            @if($employeeNumber !== '—')
                            <span class="text-slate-300 dark:text-slate-600 mx-1">•</span>
                            Matricule : {{ $employeeNumber }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 xl:grid-cols-2 gap-3 xl:min-w-96">
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700
                                    bg-slate-50 dark:bg-slate-900/40 p-3">
                        <p class="text-[11px] uppercase tracking-wide font-semibold text-slate-400 dark:text-slate-500">
                            Début
                        </p>
                        <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-100">
                            {{ $startDate ? $startDate->format('d/m/Y') : '—' }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700
                                    bg-slate-50 dark:bg-slate-900/40 p-3">
                        <p class="text-[11px] uppercase tracking-wide font-semibold text-slate-400 dark:text-slate-500">
                            Fin
                        </p>
                        <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-100">
                            {{ $endDate ? $endDate->format('d/m/Y') : 'Indéterminée' }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700
                                    bg-slate-50 dark:bg-slate-900/40 p-3">
                        <p class="text-[11px] uppercase tracking-wide font-semibold text-slate-400 dark:text-slate-500">
                            Durée
                        </p>
                        <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-100">
                            {{ $durationLabel }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700
                                    bg-slate-50 dark:bg-slate-900/40 p-3">
                        <p class="text-[11px] uppercase tracking-wide font-semibold text-slate-400 dark:text-slate-500">
                            Salaire
                        </p>
                        <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-100">
                            {{ $salary }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Colonne principale --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Informations contrat --}}
            <section class="rounded-2xl
                                bg-white dark:bg-slate-800
                                border border-slate-200 dark:border-slate-700
                                shadow-sm overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700
                                bg-slate-50 dark:bg-slate-900/40">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-600/10 dark:bg-blue-500/10
                                        flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                                Informations du contrat
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Données administratives et contractuelles.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                            <p class="text-xs font-medium text-slate-400 dark:text-slate-500">
                                Numéro du contrat
                            </p>
                            <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                {{ $contractNumber }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                            <p class="text-xs font-medium text-slate-400 dark:text-slate-500">
                                Type de contrat
                            </p>
                            <p
                                class="mt-1 inline-flex items-center gap-2 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                <i class="bi {{ $typeCfg['icon'] }} text-blue-600 dark:text-blue-400"></i>
                                {{ $typeCfg['label'] }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                            <p class="text-xs font-medium text-slate-400 dark:text-slate-500">
                                Statut
                            </p>
                            <p
                                class="mt-1 inline-flex items-center gap-2 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                <i class="bi {{ $statusCfg['icon'] }} text-slate-400"></i>
                                {{ $statusCfg['label'] }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                            <p class="text-xs font-medium text-slate-400 dark:text-slate-500">
                                Rémunération
                            </p>
                            <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                {{ $salary }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                            <p class="text-xs font-medium text-slate-400 dark:text-slate-500">
                                Date de création
                            </p>
                            <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                {{ $contract->created_at?->format('d/m/Y à H:i') ?? '—' }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                            <p class="text-xs font-medium text-slate-400 dark:text-slate-500">
                                Dernière mise à jour
                            </p>
                            <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                {{ $contract->updated_at?->format('d/m/Y à H:i') ?? '—' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-700
                                        bg-slate-50 dark:bg-slate-900/40 p-4">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-9 h-9 rounded-xl bg-slate-200 dark:bg-slate-700
                                                flex items-center justify-center text-slate-500 dark:text-slate-300 shrink-0">
                                    <i class="bi bi-card-text"></i>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                        Description / notes
                                    </p>

                                    @if($contract->description)
                                    <p
                                        class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300 whitespace-pre-line">
                                        {{ $contract->description }}
                                    </p>
                                    @else
                                    <p class="mt-2 text-sm text-slate-400 dark:text-slate-500 italic">
                                        Aucune description renseignée pour ce contrat.
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Période / progression --}}
            <section class="rounded-2xl
                                bg-white dark:bg-slate-800
                                border border-slate-200 dark:border-slate-700
                                shadow-sm overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700
                                bg-slate-50 dark:bg-slate-900/40">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10
                                        flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                                Période contractuelle
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Suivi temporel du contrat.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div
                            class="relative rounded-2xl border border-slate-200 dark:border-slate-700 p-4 overflow-hidden">
                            <div class="absolute top-0 left-0 w-1 h-full bg-blue-600"></div>
                            <p class="text-xs font-medium text-slate-400 dark:text-slate-500">
                                Date de début
                            </p>
                            <p class="mt-1 text-lg font-bold text-slate-800 dark:text-slate-100">
                                {{ $startDate ? $startDate->format('d/m/Y') : '—' }}
                            </p>
                            @if($startDate)
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                {{ $startDate->diffForHumans() }}
                            </p>
                            @endif
                        </div>

                        <div
                            class="relative rounded-2xl border border-slate-200 dark:border-slate-700 p-4 overflow-hidden">
                            <div
                                class="absolute top-0 left-0 w-1 h-full {{ $endDate ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }}">
                            </div>
                            <p class="text-xs font-medium text-slate-400 dark:text-slate-500">
                                Date de fin
                            </p>
                            <p class="mt-1 text-lg font-bold text-slate-800 dark:text-slate-100">
                                {{ $endDate ? $endDate->format('d/m/Y') : 'Indéterminée' }}
                            </p>
                            @if($endDate)
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                {{ $endDate->diffForHumans() }}
                            </p>
                            @else
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Aucun terme fixé.
                            </p>
                            @endif
                        </div>
                    </div>

                    @if(!is_null($progress))
                    <div class="mt-6">
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Progression du contrat
                            </p>
                            <p class="text-sm font-bold text-blue-600 dark:text-blue-400">
                                {{ $progress }}%
                            </p>
                        </div>

                        <div class="h-3 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                            <div class="h-full rounded-full bg-linear-to-r from-blue-600 to-emerald-500 transition-all duration-300"
                                style="width: {{ $progress }}%"></div>
                        </div>

                        <div class="mt-2 flex items-center justify-between text-xs text-slate-400 dark:text-slate-500">
                            <span>{{ $startDate?->format('d/m/Y') }}</span>
                            <span>{{ $endDate?->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    @else
                    <div class="mt-6 rounded-2xl border border-blue-100 dark:border-blue-900/60
                                        bg-blue-50 dark:bg-blue-950/30 p-4
                                        text-blue-700 dark:text-blue-300">
                        <div class="flex items-start gap-3">
                            <i class="bi bi-infinity text-lg shrink-0"></i>
                            <div>
                                <p class="text-sm font-semibold">
                                    Contrat sans échéance définie
                                </p>
                                <p class="text-sm mt-0.5 opacity-90">
                                    La progression temporelle n’est pas calculée car aucune date de fin n’est
                                    renseignée.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </section>
        </div>

        {{-- Colonne latérale --}}
        <aside class="space-y-6">

            {{-- Enseignant --}}
            <section class="rounded-2xl
                                bg-white dark:bg-slate-800
                                border border-slate-200 dark:border-slate-700
                                shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700
                                bg-linear-to-r from-blue-50 to-emerald-50
                                dark:from-blue-950/30 dark:to-emerald-950/30">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                        Enseignant associé
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Profil administratif.
                    </p>
                </div>

                <div class="p-5">
                    <div class="flex items-center gap-4">
                        @if($teacherAvatar)
                        <img src="{{ asset('storage/' . $teacherAvatar) }}" alt="{{ $teacherName }}"
                            class="w-14 h-14 rounded-2xl object-cover ring-2 ring-blue-500/20">
                        @else
                        <div class="w-14 h-14 rounded-2xl
                                            bg-linear-to-br from-blue-600 to-emerald-500
                                            flex items-center justify-center text-white font-bold shadow-sm">
                            {{ $initials }}
                        </div>
                        @endif

                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">
                                {{ $teacherName }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                {{ $teacherEmail ?: 'Email non renseigné' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 divide-y divide-slate-100 dark:divide-slate-700">
                        <div class="py-3 flex items-center justify-between gap-3">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Matricule</span>
                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-100 text-right">
                                {{ $employeeNumber }}
                            </span>
                        </div>

                        <div class="py-3 flex items-center justify-between gap-3">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Qualification</span>
                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-100 text-right">
                                {{ $qualification ?: '—' }}
                            </span>
                        </div>

                        <div class="py-3 flex items-center justify-between gap-3">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Nationalité</span>
                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-100 text-right">
                                {{ $nationality ?: '—' }}
                            </span>
                        </div>

                        <div class="py-3 flex items-center justify-between gap-3">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Statut enseignant</span>
                            <span
                                class="text-sm font-semibold text-slate-800 dark:text-slate-100 text-right capitalize">
                                {{ $teacherStatus ?: '—' }}
                            </span>
                        </div>
                    </div>

                    @if($teacher)
                    <a href="{{ route('teachers.show', $teacher) }}" class="mt-4 inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl
                                      text-sm font-semibold
                                      border border-slate-200 dark:border-slate-700
                                      bg-white dark:bg-slate-800
                                      text-slate-700 dark:text-slate-200
                                      hover:bg-slate-50 dark:hover:bg-slate-700/70
                                      hover:text-blue-600 dark:hover:text-blue-400
                                      transition-all duration-200">
                        <i class="bi bi-person-lines-fill"></i>
                        Voir le profil
                    </a>
                    @endif
                </div>
            </section>

            {{-- Document --}}
            <section class="rounded-2xl
                                bg-white dark:bg-slate-800
                                border border-slate-200 dark:border-slate-700
                                shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700
                                bg-slate-50 dark:bg-slate-900/40">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                        Document du contrat
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Fichier PDF, DOC ou DOCX.
                    </p>
                </div>

                <div class="p-5">
                    @if($documentPath)
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700
                                        bg-slate-50 dark:bg-slate-900/40 p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-11 h-11 rounded-xl
                                                {{ $documentExtension === 'PDF'
                                                    ? 'bg-red-100 text-red-600 dark:bg-red-950/40 dark:text-red-400'
                                                    : 'bg-blue-100 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400' }}
                                                flex items-center justify-center shrink-0">
                                <i
                                    class="bi {{ $documentExtension === 'PDF' ? 'bi-file-earmark-pdf-fill' : 'bi-file-earmark-word-fill' }} text-xl"></i>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                                    {{ $documentName }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    Type : {{ $documentExtension ?: 'Document' }}
                                </p>

                                @unless($documentExists)
                                <p class="mt-2 text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    Fichier introuvable sur le disque public.
                                </p>
                                @endunless
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2 print:hidden">
                            <a href="{{ $documentUrl }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl
                                              text-sm font-semibold
                                              bg-blue-600 text-white
                                              hover:bg-blue-700
                                              transition-all duration-200">
                                <i class="bi bi-eye"></i>
                                Ouvrir
                            </a>

                            <a href="{{ $documentUrl }}" download class="inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl
                                              text-sm font-semibold
                                              border border-slate-200 dark:border-slate-700
                                              bg-white dark:bg-slate-800
                                              text-slate-700 dark:text-slate-200
                                              hover:bg-slate-50 dark:hover:bg-slate-700/70
                                              transition-all duration-200">
                                <i class="bi bi-download"></i>
                                Télécharger
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="rounded-2xl border border-dashed border-slate-300 dark:border-slate-600
                                        bg-slate-50 dark:bg-slate-900/40 p-6 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-700
                                            flex items-center justify-center mx-auto
                                            text-slate-400 dark:text-slate-500">
                            <i class="bi bi-file-earmark-x text-2xl"></i>
                        </div>

                        <p class="mt-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                            Aucun document joint
                        </p>

                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Vous pouvez ajouter le fichier du contrat depuis la page de modification.
                        </p>
                    </div>
                    @endif
                </div>
            </section>

            {{-- Zone suppression --}}
            <section class="rounded-2xl
                                bg-white dark:bg-slate-800
                                border border-slate-200 dark:border-slate-700
                                shadow-sm overflow-hidden print:hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                        Actions sensibles
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Gestion irréversible du contrat.
                    </p>
                </div>

                <div class="p-5">
                    @if($contract->status === 'active')
                    <div class="rounded-2xl border border-amber-200 dark:border-amber-900/60
                                        bg-amber-50 dark:bg-amber-950/30
                                        text-amber-700 dark:text-amber-300 p-4">
                        <div class="flex items-start gap-3">
                            <i class="bi bi-lock-fill text-lg shrink-0"></i>
                            <div>
                                <p class="text-sm font-semibold">
                                    Suppression verrouillée
                                </p>
                                <p class="text-sm mt-0.5 opacity-90">
                                    Un contrat actif ne peut pas être supprimé.
                                    Changez son statut avant toute suppression.
                                </p>
                            </div>
                        </div>
                    </div>
                    @else
                    <form action="{{ route('teacher-contracts.destroy', $contract) }}" method="POST"
                        onsubmit="return confirm('Confirmez-vous la suppression définitive de ce contrat ? Cette action est irréversible.');">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl
                                               text-sm font-semibold
                                               bg-red-600 text-white
                                               hover:bg-red-700
                                               shadow-sm shadow-red-600/20
                                               transition-all duration-200">
                            <i class="bi bi-trash3-fill"></i>
                            Supprimer le contrat
                        </button>
                    </form>

                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                        Cette action supprimera également le document associé s’il existe.
                    </p>
                    @endif
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection