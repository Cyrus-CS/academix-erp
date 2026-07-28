@extends('layouts.base')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
@endsection

@section('page_title', 'Rapports & Statistiques')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

    {{-- ================= Header + Filtres ================= --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-r from-blue-50 to-emerald-50
                    dark:from-blue-950/30 dark:to-emerald-950/30
                    border-b border-slate-200 dark:border-slate-700
                    flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center shrink-0">
                    <i class="bi bi-graph-up-arrow text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                        Rapports & Statistiques
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Vue d'ensemble des performances de l'établissement
                    </p>
                </div>
            </div>

            <a href="{{ route('exports.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                      text-white bg-blue-600 hover:bg-blue-700
                      shadow-sm shadow-blue-600/20 transition-colors shrink-0">
                <i class="bi bi-download"></i>
                Exporter les données
            </a>
        </div>

        {{-- Formulaire de filtres (GET) --}}
        <form action="{{ route('reports.index') }}" method="GET"
            class="px-6 py-4 flex flex-col sm:flex-row flex-wrap items-end gap-4">

            <div class="w-full sm:w-auto sm:flex-1 min-w-[180px]">
                <x-forms.select name="academic_year_id" label="Année académique" icon="bi-calendar3"
                    :options="$academicYears" optionValue="id" optionLabel="name" :value="$currentYear?->id"
                    placeholder="Toutes les années" />
            </div>

            <div class="w-full sm:w-auto sm:flex-1 min-w-[180px]">
                <x-forms.select name="term_id" label="Trimestre" icon="bi-calendar-week" :options="$terms"
                    optionValue="id" optionLabel="name" :value="$currentTerm?->id" placeholder="Tous les trimestres" />
            </div>

            <div class="w-full sm:w-auto sm:flex-1 min-w-[180px]">
                <x-forms.select name="class_id" label="Classe" icon="bi-building" :options="$classes" optionValue="id"
                    optionLabel="name" :value="$currentClassId" placeholder="Toutes les classes" />
            </div>

            <div class="w-full sm:w-auto flex items-center gap-2">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium
                           text-white bg-slate-700 hover:bg-slate-800
                           dark:bg-slate-600 dark:hover:bg-slate-500 transition-colors w-full sm:w-auto">
                    <i class="bi bi-funnel-fill"></i>
                    Filtrer
                </button>
                @if($currentYear || $currentTerm || $currentClassId)
                <a href="{{ route('reports.index') }}" class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-lg text-sm font-medium
                          text-slate-500 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Réinitialiser">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ================= KPI Cards ================= --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        @php
        $kpis = [
        ['label' => 'Étudiants', 'value' => number_format($stats['total_students'], 0, ',', ' '), 'icon' =>
        'bi-mortarboard-fill', 'color' => 'blue'],
        ['label' => 'Enseignants actifs','value' => number_format($stats['total_teachers'], 0, ',', ' '), 'icon' =>
        'bi-person-workspace', 'color' => 'emerald'],
        ['label' => 'Classes', 'value' => number_format($stats['total_classes'], 0, ',', ' '), 'icon' => 'bi-building',
        'color' => 'cyan'],
        ['label' => 'Matières actives', 'value' => number_format($stats['total_subjects'], 0, ',', ' '), 'icon' =>
        'bi-journal-bookmark-fill','color' => 'amber'],
        ];
        @endphp

        @foreach($kpis as $kpi)
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                    border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-{{ $kpi['color'] }}-50 dark:bg-{{ $kpi['color'] }}-900/30
                            flex items-center justify-center">
                    <i
                        class="bi {{ $kpi['icon'] }} text-{{ $kpi['color'] }}-600 dark:text-{{ $kpi['color'] }}-400 text-lg"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $kpi['value'] }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $kpi['label'] }}</p>
        </div>
        @endforeach

        {{-- Revenus --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                    border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                    <i class="bi bi-cash-stack text-emerald-600 dark:text-emerald-400 text-lg"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                {{ number_format($stats['total_revenue'], 0, ',', ' ') }}
            </p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Revenus encaissés (FCFA)</p>
        </div>

        {{-- Impayés --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                    border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                    <i class="bi bi-hourglass-split text-amber-600 dark:text-amber-400 text-lg"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                {{ number_format($stats['pending_payments'], 0, ',', ' ') }}
            </p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Paiements en attente (FCFA)</p>
        </div>

        {{-- Taux de présence --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                    border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="bi bi-calendar-check-fill text-blue-600 dark:text-blue-400 text-lg"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $stats['attendance_rate'] }}%</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Taux de présence global</p>
        </div>

        {{-- Moyenne générale --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                    border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-900/30 flex items-center justify-center">
                    <i class="bi bi-award-fill text-cyan-600 dark:text-cyan-400 text-lg"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $stats['avg_grade'] }}/20</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Moyenne générale</p>
        </div>
    </div>

    {{-- ================= Charts Row 1 ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Présences (7 derniers jours) --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                    border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-bar-chart-fill text-blue-600 dark:text-blue-400"></i>
                    <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        Présences — 7 derniers jours
                    </h2>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Présent
                    </span>
                    <span class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Absent
                    </span>
                    <span class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Retard
                    </span>
                </div>
            </div>
            <div class="p-6">
                <canvas id="attendanceChart" height="110"></canvas>
            </div>
        </div>

        {{-- Répartition des inscriptions --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                    border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
                <i class="bi bi-pie-chart-fill text-blue-600 dark:text-blue-400"></i>
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                    Répartition par classe
                </h2>
            </div>
            <div class="p-6">
                <canvas id="enrollmentsChart" height="220"></canvas>
            </div>
        </div>
    </div>

    {{-- ================= Charts Row 2 ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Revenus (6 derniers mois) --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                    border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
                <i class="bi bi-graph-up text-emerald-600 dark:text-emerald-400"></i>
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                    Revenus — 6 derniers mois
                </h2>
            </div>
            <div class="p-6">
                <canvas id="paymentsChart" height="200"></canvas>
            </div>
        </div>

        {{-- Moyennes par matière --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                    border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
                <i class="bi bi-journal-bookmark-fill text-cyan-600 dark:text-cyan-400"></i>
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                    Moyennes par matière
                    @if($currentTerm)
                    <span class="text-xs font-normal text-slate-400">— {{ $currentTerm->name }}</span>
                    @endif
                </h2>
            </div>
            <div class="p-6">
                <canvas id="gradesChart" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- ================= Tables : Top classes / Top étudiants ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top classes --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                    border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
                <i class="bi bi-trophy-fill text-amber-500"></i>
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                    Top 5 classes — Meilleures moyennes
                </h2>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($topClasses as $index => $item)
                <div class="flex items-center gap-4 px-6 py-3.5">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shrink-0
                                {{ $index === 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400' :
                                   ($index === 1 ? 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300' :
                                   ($index === 2 ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-400' :
                                   'bg-slate-50 text-slate-400 dark:bg-slate-900 dark:text-slate-500')) }}">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">
                            {{ $item->classe?->name ?? 'Classe supprimée' }}
                        </p>
                        <p class="text-xs text-slate-400 dark:text-slate-500">
                            {{ $item->total_grades }} note{{ $item->total_grades > 1 ? 's' : '' }}
                            enregistrée{{ $item->total_grades > 1 ? 's' : '' }}
                        </p>
                    </div>
                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400 shrink-0">
                        {{ number_format($item->avg_score, 2) }}/20
                    </span>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10 text-slate-400 dark:text-slate-500">
                    <i class="bi bi-inbox text-3xl mb-2"></i>
                    <p class="text-sm">Aucune donnée disponible</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Top étudiants --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                    border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
                <i class="bi bi-star-fill text-amber-500"></i>
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                    Top 10 étudiants
                </h2>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700 max-h-[420px] overflow-y-auto">
                @forelse($topStudents as $index => $item)
                @php $user = $item->student?->user; @endphp
                <div class="flex items-center gap-3 px-6 py-3">
                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500 w-5 shrink-0">
                        {{ $index + 1 }}
                    </span>
                    @if($user?->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                        class="w-8 h-8 rounded-full object-cover shrink-0" />
                    @else
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-emerald-500
                                flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ strtoupper(substr($user?->name ?? 'E', 0, 1)) }}
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate">
                            {{ $user?->name ?? 'Étudiant inconnu' }}
                        </p>
                        <p class="text-xs text-slate-400 dark:text-slate-500">
                            {{ $item->total_grades }} évaluation{{ $item->total_grades > 1 ? 's' : '' }}
                        </p>
                    </div>
                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400 shrink-0">
                        {{ number_format($item->avg_score, 2) }}/20
                    </span>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10 text-slate-400 dark:text-slate-500">
                    <i class="bi bi-inbox text-3xl mb-2"></i>
                    <p class="text-sm">Aucune donnée disponible</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ================= Récapitulatif financier ================= --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
            <i class="bi bi-wallet2 text-emerald-600 dark:text-emerald-400"></i>
            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                Récapitulatif financier
            </h2>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
            $financeCards = [
            ['label' => 'Payé', 'value' => $financialSummary['total_paid'], 'color' => 'emerald', 'icon' =>
            'bi-check-circle-fill'],
            ['label' => 'En attente','value' => $financialSummary['total_pending'], 'color' => 'amber', 'icon' =>
            'bi-hourglass-split'],
            ['label' => 'En retard', 'value' => $financialSummary['total_overdue'], 'color' => 'red', 'icon' =>
            'bi-exclamation-circle-fill'],
            ['label' => 'Annulé', 'value' => $financialSummary['total_cancelled'], 'color' => 'slate', 'icon' =>
            'bi-x-circle-fill'],
            ];
            @endphp

            @foreach($financeCards as $card)
            <div class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 dark:border-slate-700
                        bg-slate-50 dark:bg-slate-900/40">
                <div class="w-10 h-10 rounded-xl bg-{{ $card['color'] }}-100 dark:bg-{{ $card['color'] }}-900/30
                            flex items-center justify-center shrink-0">
                    <i
                        class="bi {{ $card['icon'] }} text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ $card['label'] }}</p>
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">
                        {{ number_format($card['value'], 0, ',', ' ') }} FCFA
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Répartition par mode de paiement --}}
        @if($financialSummary['by_method']->isNotEmpty())
        <div class="px-6 pb-6">
            <p class="text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-3">
                Répartition par mode de paiement
            </p>
            <div class="space-y-2.5">
                @php $totalByMethod = $financialSummary['by_method']->sum('total'); @endphp
                @foreach($financialSummary['by_method'] as $method => $data)
                @php
                $percent = $totalByMethod > 0 ? round(($data->total / $totalByMethod) * 100, 1) : 0;
                $methodLabels = [
                'cash' => 'Espèces',
                'bank_transfer' => 'Virement bancaire',
                'mobile_money' => 'Mobile Money',
                'check' => 'Chèque',
                'card' => 'Carte bancaire',
                ];
                @endphp
                <div class="flex items-center gap-3">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300 w-36 shrink-0 truncate">
                        {{ $methodLabels[$method] ?? ucfirst($method) }}
                    </span>
                    <div class="flex-1 h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                        <div class="h-full rounded-full bg-blue-600" style="width: {{ $percent }}%"></div>
                    </div>
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-200 w-28 text-right shrink-0">
                        {{ number_format($data->total, 0, ',', ' ') }} F ({{ $percent }}%)
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Détection du thème actuel pour adapter les couleurs Chart.js ──
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(148, 163, 184, 0.1)' : 'rgba(148, 163, 184, 0.15)';
    const textColor = isDark ? '#94A3B8' : '#64748B';

    Chart.defaults.color = textColor;
    Chart.defaults.font.family = "'Inter', sans-serif";

    // ── Données injectées depuis le contrôleur ─────────────────────────
    const attendanceData = @json($chartData['attendance']);
    const paymentsData = @json($chartData['payments']);
    const gradesData = @json($chartData['grades']);
    const enrollmentsData = @json($chartData['enrollments']);

    // ── Graphique : Présences (barres empilées) ─────────────────────────
    new Chart(document.getElementById('attendanceChart'), {
        type: 'bar',
        data: {
            labels: attendanceData.labels,
            datasets: [{
                    label: 'Présent',
                    data: attendanceData.present,
                    backgroundColor: '#10B981',
                    borderRadius: 6
                },
                {
                    label: 'Absent',
                    data: attendanceData.absent,
                    backgroundColor: '#EF4444',
                    borderRadius: 6
                },
                {
                    label: 'Retard',
                    data: attendanceData.late,
                    backgroundColor: '#F59E0B',
                    borderRadius: 6
                },
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    stacked: true,
                    grid: {
                        display: false
                    }
                },
                y: {
                    stacked: true,
                    grid: {
                        color: gridColor
                    },
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                },
            }
        }
    });

    // ── Graphique : Revenus (ligne) ─────────────────────────────────────
    new Chart(document.getElementById('paymentsChart'), {
        type: 'line',
        data: {
            labels: paymentsData.labels,
            datasets: [{
                label: 'Revenus (FCFA)',
                data: paymentsData.amounts,
                borderColor: '#2563EB',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#2563EB',
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        color: gridColor
                    },
                    beginAtZero: true
                }
            }
        }
    });

    // ── Graphique : Moyennes par matière (barres horizontales) ──────────
    new Chart(document.getElementById('gradesChart'), {
        type: 'bar',
        data: {
            labels: gradesData.labels,
            datasets: [{
                label: 'Moyenne',
                data: gradesData.scores,
                backgroundColor: '#06B6D4',
                borderRadius: 6,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        color: gridColor
                    },
                    beginAtZero: true,
                    max: 20
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // ── Graphique : Répartition par classe (doughnut) ───────────────────
    new Chart(document.getElementById('enrollmentsChart'), {
        type: 'doughnut',
        data: {
            labels: enrollmentsData.labels,
            datasets: [{
                data: enrollmentsData.counts,
                backgroundColor: ['#2563EB', '#10B981', '#F59E0B', '#06B6D4', '#EF4444',
                    '#8B5CF6', '#EC4899', '#84CC16'
                ],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        padding: 12,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush