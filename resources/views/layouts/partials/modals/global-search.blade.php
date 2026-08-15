{{-- ══════════════════════════════════════════════════════════════
     MODAL RECHERCHE GLOBALE — Ctrl+K / Cmd+K
     JS natif 
     Entités : Élèves · Enseignants · Classes · Matières · Paiements · Annonces
══════════════════════════════════════════════════════════════ --}}

<div id="search-modal"
     class="hidden fixed inset-0 z-[100] flex items-start justify-center pt-[15vh] px-4"
     role="dialog"
     aria-modal="true"
     aria-label="Recherche globale">

    {{-- Backdrop --}}
    <div id="search-backdrop"
         class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    {{-- ── Fenêtre modale ─────────────────────────────────────── --}}
    <div id="search-dialog"
         class="relative w-full max-w-xl
                bg-white dark:bg-slate-800
                border border-slate-200 dark:border-slate-700
                rounded-2xl shadow-2xl overflow-hidden z-10
                transition-all duration-200
                opacity-0 scale-95 translate-y-2">

        {{-- ── Barre de recherche ──────────────────────────────── --}}
        <div class="flex items-center gap-3 px-4 py-3.5
                    border-b border-slate-100 dark:border-slate-700">

            <i class="bi bi-search text-blue-600 dark:text-blue-400 text-lg shrink-0"></i>

            <input id="search-input"
                   type="text"
                   placeholder="Rechercher un élève, enseignant, classe, paiement…"
                   autocomplete="off"
                   spellcheck="false"
                   aria-autocomplete="list"
                   aria-controls="search-results-list"
                   class="flex-1 bg-transparent text-sm
                          text-slate-800 dark:text-slate-100
                          placeholder-slate-400 dark:placeholder-slate-500
                          outline-none border-none focus:ring-0" />

            {{-- Compteur de résultats (affiché pendant la recherche) --}}
            <span id="search-count"
                  class="hidden text-[10px] text-slate-400 dark:text-slate-500 shrink-0">
            </span>

            <button id="search-close-btn"
                    class="flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-md
                           bg-slate-100 dark:bg-slate-700
                           text-slate-500 dark:text-slate-400 font-mono
                           hover:bg-slate-200 dark:hover:bg-slate-600
                           transition-colors shrink-0"
                    aria-label="Fermer la recherche">
                Échap
            </button>
        </div>

        {{-- ── Loader ──────────────────────────────────────────── --}}
        <div id="search-loader"
             class="hidden items-center justify-center py-8 gap-2.5
                    text-slate-400 dark:text-slate-500">
            <svg class="animate-spin w-4 h-4 text-blue-600 dark:text-blue-400"
                 fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span class="text-xs">Recherche en cours…</span>
        </div>

        {{-- ── Résultats dynamiques ────────────────────────────── --}}
        <div id="search-results" class="hidden">

            {{-- Header résultats --}}
            <div class="flex items-center justify-between px-4 pt-3 pb-1.5">
                <p class="text-[10px] font-semibold uppercase tracking-wider
                          text-slate-400 dark:text-slate-500">
                    Résultats
                </p>
                <span id="search-count-label"
                      class="text-[10px] text-slate-400 dark:text-slate-500">
                </span>
            </div>

            {{-- Liste des résultats injectée par search.js --}}
            <div id="search-results-list"
                 role="listbox"
                 aria-label="Résultats de recherche"
                 class="max-h-72 overflow-y-auto
                        divide-y divide-slate-100 dark:divide-slate-700">
            </div>

            {{-- Footer résultats --}}
            <div class="px-4 py-2.5 border-t border-slate-100 dark:border-slate-700
                        flex items-center justify-between">
                <p class="text-[10px] text-slate-400 dark:text-slate-500">
                    Utilisez
                    <kbd class="px-1 py-0.5 rounded bg-slate-100 dark:bg-slate-700 font-mono text-[9px]">↑↓</kbd>
                    pour naviguer,
                    <kbd class="px-1 py-0.5 rounded bg-slate-100 dark:bg-slate-700 font-mono text-[9px]">↵</kbd>
                    pour ouvrir
                </p>
            </div>
        </div>

        {{-- ── État vide ────────────────────────────────────────── --}}
        <div id="search-empty"
             class="hidden flex-col items-center justify-center py-10
                    text-slate-400 dark:text-slate-500">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-700/50
                        flex items-center justify-center mb-3">
                <i class="bi bi-search text-xl text-slate-400"></i>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">
                Aucun résultat trouvé
            </p>
            <p class="text-xs text-center">
                Aucun résultat pour
                <span data-empty-query
                      class="font-semibold text-slate-600 dark:text-slate-300"></span>
            </p>
        </div>

        {{-- ── Accès rapide (affiché par défaut) ──────────────── --}}
        <div id="search-quick" class="p-3">

            <p class="text-[10px] font-semibold uppercase tracking-wider
                      text-slate-400 dark:text-slate-500 px-2 mb-2">
                Accès rapide
            </p>

            <div class="grid grid-cols-2 gap-1.5">
                @php
                    $quickItems = [
                        ['icon' => 'bi-people-fill',         'label' => 'Élèves',      'color' => 'blue',    'route' => 'students.index'],
                        ['icon' => 'bi-person-badge-fill',   'label' => 'Enseignants', 'color' => 'emerald', 'route' => 'teachers.index'],
                        ['icon' => 'bi-collection-fill',     'label' => 'Classes',     'color' => 'indigo',  'route' => 'classes.index'],
                        ['icon' => 'bi-cash-stack',          'label' => 'Paiements',   'color' => 'amber',   'route' => 'payments.index'],
                        ['icon' => 'bi-journal-bookmark',    'label' => 'Matières',    'color' => 'cyan',    'route' => 'subjects.index'],
                        ['icon' => 'bi-megaphone-fill',      'label' => 'Annonces',    'color' => 'red',     'route' => 'announcements.index'],
                    ];

                    $colorClasses = [
                        'blue'    => ['bg' => 'bg-blue-50 dark:bg-blue-900/20',       'icon' => 'text-blue-500 dark:text-blue-400'],
                        'emerald' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'icon' => 'text-emerald-500 dark:text-emerald-400'],
                        'indigo'  => ['bg' => 'bg-indigo-50 dark:bg-indigo-900/20',   'icon' => 'text-indigo-500 dark:text-indigo-400'],
                        'amber'   => ['bg' => 'bg-amber-50 dark:bg-amber-900/20',     'icon' => 'text-amber-500 dark:text-amber-400'],
                        'cyan'    => ['bg' => 'bg-cyan-50 dark:bg-cyan-900/20',       'icon' => 'text-cyan-500 dark:text-cyan-400'],
                        'red'     => ['bg' => 'bg-red-50 dark:bg-red-900/20',         'icon' => 'text-red-500 dark:text-red-400'],
                    ];
                @endphp

                @foreach($quickItems as $item)
                    @php $cls = $colorClasses[$item['color']]; @endphp
                    <a href="{{ route($item['route']) }}"
                       data-search-quick-link
                       class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl
                              text-slate-700 dark:text-slate-300
                              hover:bg-slate-50 dark:hover:bg-slate-700/50
                              transition-colors group">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ $cls['bg'] }}">
                            <i class="bi {{ $item['icon'] }} {{ $cls['icon'] }} text-sm"></i>
                        </div>
                        <span class="text-sm font-medium">{{ $item['label'] }}</span>
                        <i class="bi bi-arrow-right text-xs text-slate-300 dark:text-slate-600
                                  group-hover:text-slate-400 dark:group-hover:text-slate-400
                                  ml-auto transition-colors"></i>
                    </a>
                @endforeach
            </div>

            {{-- Raccourcis clavier --}}
            <div class="flex flex-wrap items-center gap-3 mt-3 px-2 pt-3
                        border-t border-slate-100 dark:border-slate-700
                        text-[10px] text-slate-400 dark:text-slate-500">
                <span class="flex items-center gap-1">
                    <kbd class="px-1.5 py-0.5 rounded
                                bg-slate-100 dark:bg-slate-700 font-mono">↵</kbd>
                    Ouvrir
                </span>
                <span class="flex items-center gap-1">
                    <kbd class="px-1.5 py-0.5 rounded
                                bg-slate-100 dark:bg-slate-700 font-mono">Échap</kbd>
                    Fermer
                </span>
                <span class="flex items-center gap-1">
                    <kbd class="px-1.5 py-0.5 rounded
                                bg-slate-100 dark:bg-slate-700 font-mono">⌘K</kbd>
                    Ouvrir
                </span>
                <span class="flex items-center gap-1">
                    <kbd class="px-1.5 py-0.5 rounded
                                bg-slate-100 dark:bg-slate-700 font-mono">↑↓</kbd>
                    Naviguer
                </span>
            </div>
        </div>
    </div>
</div>