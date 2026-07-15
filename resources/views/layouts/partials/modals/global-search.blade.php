<div x-data="globalSearch()" x-cloak @open-search.window="open = true; $nextTick(() => $refs.searchInput?.focus())"
    @keydown.ctrl.k.window.prevent="open = true; $nextTick(() => $refs.searchInput?.focus())"
    @keydown.escape.window="if(open) open = false" x-show="open" style="display: none"
    class="fixed inset-0 z-100 flex items-start justify-center pt-[15vh] px-4">
    {{-- Backdrop --}}
    <div @click="open = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    {{-- Modal --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2" class="relative w-full max-w-xl
               bg-white dark:bg-slate-800
               border border-slate-200 dark:border-slate-700
               rounded-2xl shadow-2xl overflow-hidden z-10" @click.stop>
        {{-- Input --}}
        <div class="flex items-center gap-3 px-4 py-3.5
                    border-b border-slate-100 dark:border-slate-700">
            <i class="bi bi-search text-blue-600 dark:text-blue-400 text-lg shrink-0"></i>
            <input x-ref="searchInput" x-model="query" @input.debounce.300ms="search()" @keydown.escape="open = false"
                type="text" placeholder="Rechercher un élève, enseignant, classe…" class="flex-1 bg-transparent text-sm text-slate-800 dark:text-slate-100
                       placeholder-slate-400 dark:placeholder-slate-500
                       outline-none border-none focus:ring-0" />
            <button @click="open = false" class="flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-md
                       bg-slate-100 dark:bg-slate-700
                       text-slate-500 dark:text-slate-400 font-mono
                       hover:bg-slate-200 dark:hover:bg-slate-600
                       transition-colors shrink-0">
                Échap
            </button>
        </div>

        {{-- Résultats dynamiques --}}
        <div x-show="results.length > 0" class="border-b border-slate-100 dark:border-slate-700">
            <p class="text-[10px] font-semibold uppercase tracking-wider
                      text-slate-400 dark:text-slate-500 px-4 pt-3 pb-1.5">
                Résultats
            </p>
            <div class="max-h-48 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700">
                <template x-for="result in results" :key="result.id">
                    <a :href="result.url" @click="open = false" class="flex items-center gap-3 px-4 py-2.5
                               hover:bg-slate-50 dark:hover:bg-slate-700/50
                               transition-colors">
                        <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40
                                    flex items-center justify-center shrink-0">
                            <i class="bi text-blue-600 dark:text-blue-400 text-sm"
                                :class="result.icon ?? 'bi-search'"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate"
                                x-text="result.label"></p>
                            <p class="text-xs text-slate-400 dark:text-slate-500" x-text="result.sublabel ?? ''"></p>
                        </div>
                        <span class="ml-auto text-[10px] px-2 py-0.5 rounded-full
                                     bg-slate-100 dark:bg-slate-700
                                     text-slate-500 dark:text-slate-400 shrink-0" x-text="result.type ?? ''">
                        </span>
                    </a>
                </template>
            </div>
        </div>

        {{-- État : chargement --}}
        <div x-show="loading" class="flex items-center justify-center py-6 gap-2
                                     text-slate-400 dark:text-slate-500">
            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span class="text-xs">Recherche en cours…</span>
        </div>

        {{-- État : aucun résultat --}}
        <div x-show="query.length >= 2 && !loading && results.length === 0" class="flex flex-col items-center justify-center py-8
                    text-slate-400 dark:text-slate-500">
            <i class="bi bi-search text-2xl mb-2"></i>
            <p class="text-sm">Aucun résultat pour
                <span class="font-semibold text-slate-600 dark:text-slate-300" x-text='"« " + query + " »"'></span>
            </p>
        </div>

        {{-- Accès rapide (affiché si pas de query) --}}
        <div x-show="query.length < 2 && !loading" class="p-3">
            <p class="text-[10px] font-semibold uppercase tracking-wider
                      text-slate-400 dark:text-slate-500 px-2 mb-1.5">
                Accès rapide
            </p>
            <div class="grid grid-cols-2 gap-1.5">
                @foreach([
                ['icon' => 'bi-people-fill', 'label' => 'Élèves', 'color' => 'blue', 'route' => 'students.index'],
                ['icon' => 'bi-person-badge-fill', 'label' => 'Enseignants', 'color' => 'emerald', 'route' =>
                'teachers.index'],
                ['icon' => 'bi-building', 'label' => 'Classes', 'color' => 'violet', 'route' => 'classes.index'],
                ['icon' => 'bi-cash-stack', 'label' => 'Paiements', 'color' => 'amber', 'route' => 'payments.index'],
                ] as $item)
                <a href="{{ route($item['route']) }}" @click="open = false" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm
                               text-slate-700 dark:text-slate-300
                               hover:bg-slate-50 dark:hover:bg-slate-700/50
                               hover:text-{{ $item['color'] }}-600
                               dark:hover:text-{{ $item['color'] }}-400
                               transition-colors">
                    <i class="bi {{ $item['icon'] }} text-{{ $item['color'] }}-500
                                  text-base w-4 text-center"></i>
                    {{ $item['label'] }}
                </a>
                @endforeach
            </div>

            {{-- Raccourcis clavier --}}
            <div class="flex items-center justify-between mt-3 px-2 pt-3
                        border-t border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-3 text-[10px] text-slate-400 dark:text-slate-500">
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 font-mono">↵</kbd>
                        Ouvrir
                    </span>
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 font-mono">Échap</kbd>
                        Fermer
                    </span>
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 font-mono">⌘K</kbd>
                        Ouvrir
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>