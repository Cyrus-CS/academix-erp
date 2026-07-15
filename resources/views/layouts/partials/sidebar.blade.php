{{-- 
 layouts/partials/
├── sidebar.blade.php        ← fichier principal
└── sidebar-nav.blade.php    ← items de navigation (séparé car volumineux)

--}}
<aside id="sidebar" class="fixed top-0 left-0 h-full z-40
bg-white dark:bg-slate-900
border-r border-slate-200 dark:border-slate-700
flex flex-col transition-all duration-300 ease-in-out shadow-sm" :class="[
isCollapsed ? 'w-18 sidebar-collapsed' : 'w-65',
mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
]">
    {{-- ══════════════════════════════════════
         BRAND / LOGO
    ══════════════════════════════════════ --}}
    <div class="flex items-center h-16 px-4 shrink-0
                border-b border-slate-200 dark:border-slate-700">

        {{-- Logo icon --}}
        <div class="shrink-0 w-9 h-9 rounded-xl
                    bg-linear-to-br from-blue-600 to-emerald-500
                    flex items-center justify-center shadow-md">
            <i class="bi bi-mortarboard-fill text-white text-lg"></i>
        </div>

        {{-- Brand text --}}
        <div class="ml-3 overflow-hidden sidebar-brand-text transition-all duration-300">
            <p class="text-sm font-bold leading-tight text-slate-800 dark:text-slate-100 whitespace-nowrap">
                School <span class="brand-gradient">ERP</span>
            </p>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 whitespace-nowrap">
                Gestion scolaire
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         NAVIGATION SCROLLABLE
    ══════════════════════════════════════ --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-3 space-y-0.5 px-2">
        @include('layouts.partials.sidebar-nav')
    </nav>

    {{-- ══════════════════════════════════════
         SIDEBAR FOOTER : Année académique
    ══════════════════════════════════════ --}}
    <div class="shrink-0 border-t border-slate-200 dark:border-slate-700 p-3">

        {{-- Année académique active --}}
        <div class="flex items-center gap-2.5 px-2 py-2 rounded-xl
                   bg-blue-50 dark:bg-blue-950/40
                   border border-blue-100 dark:border-blue-900/50
                   overflow-hidden transition-all duration-300" x-show="!isCollapsed"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">
            <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center shrink-0">
                <i class="bi bi-calendar-check-fill text-white text-xs"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-medium text-blue-400 dark:text-blue-500 uppercase tracking-wide">
                    Année active
                </p>
                <p class="text-xs font-semibold text-blue-700 dark:text-blue-300 truncate">
                    {{-- Récupérer depuis la config ou la session --}}
                    {{ config('school.current_year', '2024 – 2025') }}
                </p>
            </div>
        </div>
    </div>
</aside>

<div id="sidebar-overlay" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 lg:hidden">
</div>