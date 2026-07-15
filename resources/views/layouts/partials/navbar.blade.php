<header class="sticky top-0 z-40 flex items-center justify-between h-16 px-4 sm:px-6
               bg-white/90 dark:bg-slate-800/90 backdrop-blur-md
               border-b border-slate-200 dark:border-slate-700 shadow-sm">

    {{-- ── Gauche : Toggle + Breadcrumb ── --}}
    <div class="flex items-center gap-3">

        {{-- Toggle Sidebar --}}
        <button id="sidebar-toggle" class="p-2 rounded-xl text-slate-500 dark:text-slate-400
           hover:bg-slate-100 dark:hover:bg-slate-700
           hover:text-blue-600 dark:hover:text-blue-400
           transition-all duration-200 focus:outline-none" aria-label="Toggle sidebar">
            <i id="toggle-icon" class="bi bi-layout-sidebar-inset-reverse text-xl"></i>
        </button>

        {{-- Breadcrumb --}}
        <nav class="hidden sm:flex items-center gap-1.5 text-sm" aria-label="Fil d'Ariane">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-1 text-slate-400 dark:text-slate-500
                       hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                <i class="bi bi-house-fill text-xs"></i>
                <span>Accueil</span>
            </a>

            @hasSection('breadcrumb')
            @yield('breadcrumb')
            @endif

            <span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
            <span class="font-semibold text-slate-700 dark:text-slate-200">
                @yield('page_title', 'Tableau de bord')
            </span>
        </nav>
    </div>

    {{-- ── Droite : Actions ── --}}
    <div class="flex items-center gap-2">

        {{-- Bouton Recherche --}}
        <button data-open-search class="hidden sm:flex items-center gap-2 px-3 py-2 rounded-xl text-sm
           text-slate-400 dark:text-slate-500
           border border-slate-200 dark:border-slate-700
           bg-slate-50 dark:bg-slate-800/50
           hover:border-blue-300 dark:hover:border-blue-600
           hover:text-blue-600 dark:hover:text-blue-400
           transition-all duration-200 min-w-40
           focus:outline-none" title="Rechercher (Ctrl+K)">
            <i class="bi bi-search text-sm"></i>
            <span class="text-xs">Rechercher…</span>
            <kbd class="ml-auto text-[10px] px-1.5 py-0.5 rounded-md
                bg-slate-200 dark:bg-slate-700 font-mono">⌘K</kbd>
        </button>

        {{-- Toggle Dark / Light ── --}}
        <button id="theme-toggle" class="relative p-2 rounded-xl text-slate-500 dark:text-slate-400
           hover:bg-slate-100 dark:hover:bg-slate-700
           hover:text-amber-500 dark:hover:text-amber-400
           transition-all duration-200 focus:outline-none" aria-label="Toggle theme">
            <i id="icon-sun" class="bi bi-sun-fill text-xl"></i>
            <i id="icon-moon" class="bi bi-moon-stars-fill text-xl hidden"></i>
        </button>

        {{-- Notifications ── --}}
        <div class="relative" id="notif-wrapper">
            <button id="notif-btn" class="relative p-2 rounded-xl text-slate-500 dark:text-slate-400
                   hover:bg-slate-100 dark:hover:bg-slate-700
                   hover:text-blue-600 dark:hover:text-blue-400
                   transition-all duration-200 focus:outline-none" aria-label="Notifications">
                <i class="bi bi-bell-fill text-xl"></i>

                {{-- Badge --}}
                @php $unreadCount = auth()->user()?->unreadNotifications->count() ?? 0; @endphp
                <span id="notif-badge" class="absolute -top-0.5 -right-0.5 min-w-4.5 h-4.5 px-1
                     bg-red-500 text-white text-[10px] font-bold
                     rounded-full flex items-center justify-center
                     transition-all duration-300
                     {{ $unreadCount > 0 ? '' : 'hidden' }}" aria-live="polite">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            </button>

            {{-- Dropdown --}}
            <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 origin-top-right
                bg-white dark:bg-slate-800
                border border-slate-200 dark:border-slate-700
                rounded-2xl shadow-xl overflow-hidden z-50
                opacity-0 scale-95 translate-y-1
                transition-all duration-200">

                {{-- Header --}}
                <div class="flex items-center justify-between px-4 py-3
                    border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-sm text-slate-800 dark:text-slate-100">
                            Notifications
                        </h3>
                        <span id="notif-badge-header" class="px-1.5 py-0.5 rounded-full text-[10px] font-bold
                             bg-red-100 dark:bg-red-900/30
                             text-red-600 dark:text-red-400
                             {{ $unreadCount > 0 ? '' : 'hidden' }}">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    </div>
                    <button id="mark-all-read" class="text-xs text-blue-600 dark:text-blue-400
                           hover:underline font-medium focus:outline-none
                           {{ $unreadCount === 0 ? 'hidden' : '' }}">
                        Tout marquer lu
                    </button>
                </div>

                {{-- Liste notifications --}}
                <div id="notif-list" class="divide-y divide-slate-100 dark:divide-slate-700 max-h-72 overflow-y-auto">

                    @forelse(auth()->user()?->unreadNotifications ?? [] as $notification)
                    @php
                    $data = $notification->data ?? [];
                    $typeIcons = [
                    'payment' => ['icon' => 'bi-credit-card-fill', 'color' => 'text-emerald-500', 'bg' =>
                    'bg-emerald-100 dark:bg-emerald-900/30'],
                    'attendance' => ['icon' => 'bi-person-check-fill', 'color' => 'text-blue-500', 'bg' => 'bg-blue-100
                    dark:bg-blue-900/30'],
                    'grade' => ['icon' => 'bi-pencil-square', 'color' => 'text-amber-500', 'bg' => 'bg-amber-100
                    dark:bg-amber-900/30'],
                    'announcement'=> ['icon' => 'bi-megaphone-fill', 'color' => 'text-cyan-500', 'bg' => 'bg-cyan-100
                    dark:bg-cyan-900/30'],
                    'info' => ['icon' => 'bi-info-circle-fill', 'color' => 'text-blue-500', 'bg' => 'bg-blue-100
                    dark:bg-blue-900/30'],
                    ];
                    $cfg = $typeIcons[$data['type'] ?? 'info'] ?? $typeIcons['info'];
                    @endphp
                    <div class="flex gap-3 px-4 py-3
                        hover:bg-slate-50 dark:hover:bg-slate-700/50
                        transition-colors cursor-pointer"
                        onclick="{{ isset($data['url']) ? "window.location='{$data['url']}'" : '' }}">
                        <div class="w-8 h-8 rounded-full {{ $cfg['bg'] }}
                            flex items-center justify-center shrink-0">
                            <i class="bi {{ $cfg['icon'] }} {{ $cfg['color'] }} text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">
                                {{ $data['title'] ?? 'Notification' }}
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5
                              line-clamp-2 leading-relaxed">
                                {{ $data['message'] ?? '' }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                        {{-- Indicateur non lu --}}
                        <div class="w-2 h-2 rounded-full bg-blue-500 shrink-0 mt-1.5"></div>
                    </div>
                    @empty
                    <div id="notif-empty" class="flex flex-col items-center justify-center py-8
                        text-slate-400 dark:text-slate-500">
                        <i class="bi bi-bell-slash text-3xl mb-2"></i>
                        <p class="text-xs">Aucune nouvelle notification</p>
                    </div>
                    @endforelse
                </div>

                {{-- Footer --}}
                <div class="px-4 py-2.5 border-t border-slate-100 dark:border-slate-700 text-center">
                    <a href="{{ route('notifications.index') }}" class="text-xs text-blue-600 dark:text-blue-400
                      hover:underline font-medium">
                        Voir toutes les notifications
                    </a>
                </div>
            </div>
        </div>


        {{-- Avatar / User Dropdown ── --}}
        <div class="relative">
            <button data-dropdown="user-dropdown" class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-xl
               hover:bg-slate-100 dark:hover:bg-slate-700
               transition-all duration-200 focus:outline-none" aria-label="Menu utilisateur">
                @if(auth()->user()?->avatar)
                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}"
                    class="w-8 h-8 rounded-full object-cover ring-2 ring-blue-500/30" />
                @else
                <div class="w-8 h-8 rounded-full bg-linear-to-br from-blue-500 to-emerald-500
                        flex items-center justify-center text-white text-xs font-bold
                        ring-2 ring-blue-500/30">
                    {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()?->name ?? 'User')[1] ?? '', 0, 1)) }}
                </div>
                @endif
                <div class="hidden md:block text-left">
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 leading-tight">
                        {{ auth()->user()?->name ?? 'Utilisateur' }}
                    </p>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 capitalize">
                        {{ auth()->user()?->getRoleNames()->first() ?? 'Rôle' }}
                    </p>
                </div>
                <i class="bi bi-chevron-down text-xs text-slate-400 hidden md:block"></i>
            </button>

            {{-- Dropdown --}}
            <div id="user-dropdown" data-dropdown-menu class="hidden absolute right-0 mt-2 w-56 origin-top-right
               bg-white dark:bg-slate-800
               border border-slate-200 dark:border-slate-700
               rounded-2xl shadow-xl overflow-hidden z-50
               opacity-0 scale-95 translate-y-1
               transition-all duration-200">
                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700
                    bg-linear-to-r from-blue-50 to-emerald-50
                    dark:from-blue-950/30 dark:to-emerald-950/30">
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        {{ auth()->user()?->name ?? 'Utilisateur' }}
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                        {{ auth()->user()?->email ?? '' }}
                    </p>
                </div>

                <div class="py-1.5">
                    @foreach([
                    ['route' => 'profile.edit', 'icon' => 'bi-person-circle', 'label' => 'Mon profil'],
                    ['route' => 'settings.index', 'icon' => 'bi-gear-fill', 'label' => 'Paramètres'],
                    ['route' => 'help.index', 'icon' => 'bi-question-circle', 'label' => 'Aide & Support'],
                    ] as $item)
                    <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm
                      text-slate-700 dark:text-slate-300
                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                      hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <i class="bi {{ $item['icon'] }} text-base w-4 text-center"></i>
                        {{ $item['label'] }}
                    </a>
                    @endforeach
                </div>

                <div class="border-t border-slate-100 dark:border-slate-700 py-1.5">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm
                               text-red-600 dark:text-red-400
                               hover:bg-red-50 dark:hover:bg-red-900/20
                               transition-colors focus:outline-none">
                            <i class="bi bi-box-arrow-right text-base w-4 text-center"></i>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>{{-- end actions --}}
</header>