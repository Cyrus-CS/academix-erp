@extends('layouts.base')

@section('page_title', 'Exports')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Gestion des exports</span>
@endsection

@section('content')

<div class="max-w-5xl mx-auto space-y-6">

    {{-- ════════════════════ HEADER ════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-5 bg-linear-to-r from-blue-50 to-emerald-50
                    dark:from-blue-950/30 dark:to-emerald-950/30
                    border-b border-slate-200 dark:border-slate-700
                    flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-linear-to-br from-blue-600 to-emerald-500
                            flex items-center justify-center shrink-0 shadow-md">
                    <i class="bi bi-download text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                        Exports de données
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Téléchargez les données de l'établissement au format Excel (.xlsx)
                    </p>
                </div>
            </div>

            {{-- Lien vers les rapports --}}
            <a href="{{ route('reports.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                      text-sm font-medium
                      text-slate-600 dark:text-slate-300
                      bg-white dark:bg-slate-700
                      border border-slate-200 dark:border-slate-600
                      hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors shrink-0">
                <i class="bi bi-graph-up-arrow"></i>
                Voir les rapports
            </a>
        </div>
    </div>

    {{-- ════════════════════ INFO BANNER ════════════════════ --}}
    <div class="flex items-start gap-3 px-4 py-3.5 rounded-xl
                bg-cyan-50 dark:bg-cyan-900/20
                border border-cyan-200 dark:border-cyan-800">
        <i class="bi bi-info-circle-fill text-cyan-500 shrink-0 mt-0.5"></i>
        <div class="text-xs text-cyan-700 dark:text-cyan-400 leading-relaxed">
            <strong>Format :</strong> Tous les exports sont au format
            <strong>Microsoft Excel (.xlsx)</strong>.
            Les fichiers sont générés en temps réel avec les données actuelles de la base.
            Le nom du fichier inclut la date d'export pour faciliter l'archivage.
        </div>
    </div>

    {{-- ════════════════════ CARDS EXPORTS ════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

        @php
        $exportCards = [
        [
        'label' => 'Étudiants',
        'description' => 'Liste complète des étudiants avec leur classe, matricule, informations personnelles et
        tuteur.',
        'icon' => 'bi-mortarboard-fill',
        'color' => 'blue',
        'gradient' => 'from-blue-500 to-blue-600',
        'route' => route('exports.students'),
        'filename' => 'etudiants_' . now()->format('Y-m-d') . '.xlsx',
        'columns' => ['Matricule', 'Nom', 'Email', 'Classe', 'Année académique', 'Date de naissance', 'Genre',
        'Adresse'],
        'badge' => 'Étudiants inscrits',
        ],
        [
        'label' => 'Notes',
        'description' => 'Toutes les notes par étudiant, matière et trimestre avec coefficients et moyennes pondérées.',
        'icon' => 'bi-journal-bookmark-fill',
        'color' => 'emerald',
        'gradient' => 'from-emerald-500 to-emerald-600',
        'route' => route('exports.grades'),
        'filename' => 'notes_' . now()->format('Y-m-d') . '.xlsx',
        'columns' => ['Matricule', 'Étudiant', 'Classe', 'Matière', 'Trimestre', 'Type', 'Note /20', 'Coefficient'],
        'badge' => 'Notes & évaluations',
        ],
        [
        'label' => 'Présences',
        'description' => 'Historique complet des présences, absences et retards avec motifs de justification.',
        'icon' => 'bi-calendar-check-fill',
        'color' => 'amber',
        'gradient' => 'from-amber-500 to-amber-600',
        'route' => route('exports.attendance'),
        'filename' => 'presences_' . now()->format('Y-m-d') . '.xlsx',
        'columns' => ['Date', 'Matricule', 'Étudiant', 'Classe', 'Matière', 'Statut', 'Motif'],
        'badge' => 'Suivi des présences',
        ],
        [
        'label' => 'Paiements',
        'description' => 'Historique des paiements avec statuts, modes de règlement et références de transaction.',
        'icon' => 'bi-cash-stack',
        'color' => 'cyan',
        'gradient' => 'from-cyan-500 to-cyan-600',
        'route' => route('exports.payments'),
        'filename' => 'paiements_' . now()->format('Y-m-d') . '.xlsx',
        'columns' => ['Référence', 'Étudiant', 'Type de frais', 'Montant dû', 'Montant payé', 'Statut', 'Mode'],
        'badge' => 'Suivi financier',
        ],
        ];
        @endphp

        @foreach($exportCards as $card)
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                    border-slate-200 dark:border-slate-700 overflow-hidden
                    hover:shadow-md transition-shadow duration-200 group">

            {{-- Card header avec gradient --}}
            <div class="px-6 py-5 bg-linear-to-r {{ $card['gradient'] }}/5
                        dark:{{ $card['gradient'] }}/10
                        border-b border-slate-100 dark:border-slate-700
                        flex items-center gap-4">

                <div class="w-12 h-12 rounded-2xl bg-linear-to-br {{ $card['gradient'] }}
                            flex items-center justify-center shrink-0 shadow-sm
                            group-hover:scale-105 transition-transform duration-200">
                    <i class="bi {{ $card['icon'] }} text-white text-xl"></i>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">
                            {{ $card['label'] }}
                        </h2>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-medium
                                     bg-{{ $card['color'] }}-100 dark:bg-{{ $card['color'] }}-900/40
                                     text-{{ $card['color'] }}-700 dark:text-{{ $card['color'] }}-400">
                            {{ $card['badge'] }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                        {{ $card['filename'] }}
                    </p>
                </div>
            </div>

            {{-- Card body --}}
            <div class="px-6 py-5 space-y-4">

                {{-- Description --}}
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    {{ $card['description'] }}
                </p>

                {{-- Colonnes incluses --}}
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-widest
                               text-slate-400 dark:text-slate-500 mb-2">
                        Colonnes incluses
                    </p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($card['columns'] as $column)
                        <span class="text-[11px] px-2 py-0.5 rounded-md
                                     bg-slate-100 dark:bg-slate-700
                                     text-slate-600 dark:text-slate-300">
                            {{ $column }}
                        </span>
                        @endforeach
                        <span class="text-[11px] px-2 py-0.5 rounded-md
                                     bg-slate-50 dark:bg-slate-800
                                     text-slate-400 dark:text-slate-500 italic">
                            + plus...
                        </span>
                    </div>
                </div>

                {{-- Bouton export --}}
                <a href="{{ $card['route'] }}" data-export-btn class="group/btn w-full inline-flex items-center justify-center gap-2.5
                          px-4 py-3 rounded-xl text-sm font-semibold
                          text-{{ $card['color'] }}-700 dark:text-{{ $card['color'] }}-400
                          bg-{{ $card['color'] }}-50 dark:bg-{{ $card['color'] }}-900/20
                          border border-{{ $card['color'] }}-200 dark:border-{{ $card['color'] }}-800
                          hover:bg-{{ $card['color'] }}-600 hover:text-white
                          dark:hover:bg-{{ $card['color'] }}-600 dark:hover:text-white
                          hover:border-{{ $card['color'] }}-600
                          hover:shadow-sm hover:shadow-{{ $card['color'] }}-600/20
                          transition-all duration-200">

                    <i class="bi bi-file-earmark-excel-fill text-base
                              group-hover/btn:scale-110 transition-transform duration-200"></i>
                    Télécharger {{ $card['label'] }}
                    <i class="bi bi-download text-xs ml-auto
                              group-hover/btn:translate-y-0.5 transition-transform duration-200"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ════════════════════ SECTION CONSEILS ════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700
                    flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-900/30
                        flex items-center justify-center">
                <i class="bi bi-lightbulb-fill text-amber-500 text-sm"></i>
            </div>
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                Conseils d'utilisation
            </h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/30
                            flex items-center justify-center shrink-0 mt-0.5">
                    <i class="bi bi-filter text-blue-600 dark:text-blue-400 text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-0.5">
                        Filtres avancés
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Consultez les <a href="{{ route('reports.index') }}"
                            class="text-blue-600 dark:text-blue-400 hover:underline">rapports</a>
                        pour filtrer par classe, trimestre ou période avant d'exporter.
                    </p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30
                            flex items-center justify-center shrink-0 mt-0.5">
                    <i class="bi bi-clock-history text-emerald-600 dark:text-emerald-400 text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-0.5">
                        Données en temps réel
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Chaque export reflète l'état actuel de la base de données au moment
                        du téléchargement.
                    </p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-900/30
                            flex items-center justify-center shrink-0 mt-0.5">
                    <i class="bi bi-archive text-purple-600 dark:text-purple-400 text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-0.5">
                        Archivage
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        La date est incluse dans le nom du fichier pour faciliter
                        l'organisation de vos archives.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Feedback visuel pendant le téléchargement ─────────────────
    document.querySelectorAll('[data-export-btn]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const originalHtml = btn.innerHTML;
            const card = btn.closest('.group');

            // Changer l'apparence pendant la génération
            btn.innerHTML = `
                <i class="bi bi-arrow-repeat animate-spin text-base"></i>
                Génération en cours…
                <i class="bi bi-hourglass-split text-xs ml-auto"></i>
            `;
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.75';

            // Restaurer après 4s (temps de génération estimé)
            setTimeout(function() {
                btn.innerHTML = originalHtml;
                btn.style.pointerEvents = '';
                btn.style.opacity = '';

                window.showToast({
                    type: 'success',
                    title: 'Export terminé',
                    message: 'Votre fichier Excel a été téléchargé avec succès.',
                    delay: 4000,
                });
            }, 4000);
        });
    });
});
</script>
@endpush