@if(session()->hasAny(['success', 'error', 'warning', 'info']))
<div id="flash-container" class="fixed top-5 right-5 z-200 flex flex-col gap-3 w-full max-w-sm pointer-events-none">
    @foreach([
    'success' => [
    'icon' => 'bi-check-circle-fill',
    'iconColor' => 'text-emerald-500',
    'border' => 'border-emerald-500/20',
    'delay' => 5000,
    ],
    'error' => [
    'icon' => 'bi-x-circle-fill',
    'iconColor' => 'text-red-500',
    'border' => 'border-red-500/20',
    'delay' => 6000,
    ],
    'warning' => [
    'icon' => 'bi-exclamation-triangle-fill',
    'iconColor' => 'text-amber-500',
    'border' => 'border-amber-500/20',
    'delay' => 5500,
    ],
    'info' => [
    'icon' => 'bi-info-circle-fill',
    'iconColor' => 'text-cyan-500',
    'border' => 'border-cyan-500/20',
    'delay' => 5000,
    ],
    ] as $type => $cfg)
    @if(session($type))
    <div data-toast data-delay="{{ $cfg['delay'] }}" class="toast-item pointer-events-auto
                   flex items-start gap-3.5 px-4 py-3.5
                   rounded-xl shadow-lg border {{ $cfg['border'] }}
                   bg-slate-800/95 dark:bg-slate-900/95
                   backdrop-blur-sm
                   translate-x-0 opacity-100
                   transition-all duration-300 ease-out">
        {{-- Icône --}}
        <i class="bi {{ $cfg['icon'] }} {{ $cfg['iconColor'] }}
                      text-xl shrink-0 mt-0.5"></i>

        {{-- Contenu --}}
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-white leading-snug">
                @php
                $titles = [
                'success' => 'Succès',
                'error' => 'Erreur',
                'warning' => 'Attention',
                'info' => 'Information',
                ];
                @endphp
                {{ $titles[$type] }}
            </p>
            <p class="text-sm text-slate-400 mt-0.5 leading-snug">
                {{ session($type) }}
            </p>
        </div>

        {{-- Bouton fermer --}}
        <button data-toast-close class="shrink-0 text-slate-500 hover:text-slate-300
                       transition-colors focus:outline-none mt-0.5" aria-label="Fermer">
            <i class="bi bi-x-lg text-sm"></i>
        </button>

        {{-- Barre de progression --}}
        <div class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-xl overflow-hidden">
            <div data-toast-progress class="h-full {{ str_replace('text-', 'bg-', $cfg['iconColor']) }}
                           w-full origin-left" style="animation: toast-shrink {{ $cfg['delay'] }}ms linear forwards;">
            </div>
        </div>

    </div>
    @endif
    @endforeach
</div>
@endif