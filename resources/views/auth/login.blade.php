@php
use Illuminate\Support\Facades\Auth;
Auth::loginUsingId(1);

@endphp
<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Connexion | {{ config('app.name', 'School ERP') }}</title>

    {{-- Favicon --}}
    @php $favicon = $schoolSettings['favicon'] ?? ''; @endphp
    @if($favicon)
    <link rel="icon" href="{{ asset('storage/' . $favicon) }}" />
    @else
    <link rel="icon" href="{{ asset('favicon.ico') }}" />
    @endif

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="{{ asset('asset/css/bootstrap-icons.min.css') }}">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-slate-50 dark:bg-slate-900 antialiased font-inter">

    <div class="min-h-screen flex">

        {{-- ══════════════════════════════════════
         PANNEAU GAUCHE — Illustration
    ══════════════════════════════════════ --}}
        <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 relative overflow-hidden
                bg-linear-to-br from-blue-600 via-blue-700 to-emerald-600">

            {{-- Motif décoratif --}}
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>
            </div>

            {{-- Cercles décoratifs --}}
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full
                    bg-white/5 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full
                    bg-emerald-400/10 blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                    w-64 h-64 rounded-full bg-white/5 blur-2xl"></div>

            {{-- Contenu --}}
            <div class="relative z-10 flex flex-col justify-between p-12 w-full">

                {{-- Logo + Nom --}}
                <div class="flex items-center gap-3">
                    @php $logo = $schoolSettings['logo'] ?? ''; @endphp
                    @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}"
                        alt="{{ $schoolSettings['school_name'] ?? 'School ERP' }}"
                        class="w-10 h-10 rounded-xl object-contain bg-white/10 p-1" />
                    @else
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm
                            flex items-center justify-center">
                        <i class="bi bi-mortarboard-fill text-white text-xl"></i>
                    </div>
                    @endif
                    <div>
                        <p class="text-white font-bold text-lg leading-tight">
                            {{ $schoolSettings['school_name'] ?? config('app.name', 'School ERP') }}
                        </p>
                        <p class="text-blue-200 text-xs">Gestion scolaire</p>
                    </div>
                </div>

                {{-- Message central --}}
                <div class="text-center">
                    <div class="w-24 h-24 rounded-3xl bg-white/15 backdrop-blur-sm
                            flex items-center justify-center mx-auto mb-8
                            shadow-2xl shadow-blue-900/20">
                        <i class="bi bi-mortarboard-fill text-white text-5xl"></i>
                    </div>
                    <h2 class="text-4xl font-extrabold text-white mb-4 leading-tight">
                        Bienvenue sur<br />votre espace scolaire
                    </h2>
                    <p class="text-blue-200 text-lg max-w-md mx-auto leading-relaxed">
                        {{ $schoolSettings['school_motto'] ?? 'Gérez facilement votre établissement depuis un seul endroit.' }}
                    </p>

                    {{-- Features --}}
                    <div class="mt-10 grid grid-cols-3 gap-4 max-w-sm mx-auto">
                        @foreach([
                        ['icon' => 'bi-mortarboard', 'label' => 'Étudiants'],
                        ['icon' => 'bi-calendar-check', 'label' => 'Présences'],
                        ['icon' => 'bi-graph-up-arrow', 'label' => 'Résultats'],
                        ['icon' => 'bi-people', 'label' => 'Enseignants'],
                        ['icon' => 'bi-cash-coin', 'label' => 'Paiements'],
                        ['icon' => 'bi-bell', 'label' => 'Alertes'],
                        ] as $feature)
                        <div class="flex flex-col items-center gap-2 p-3 rounded-2xl
                                bg-white/10 backdrop-blur-sm">
                            <i class="bi {{ $feature['icon'] }} text-white text-xl"></i>
                            <span class="text-blue-100 text-xs font-medium">{{ $feature['label'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Footer --}}
                <div class="text-center">
                    <p class="text-blue-300 text-xs">
                        © {{ date('Y') }} {{ $schoolSettings['school_name'] ?? 'School ERP' }}
                        | Tous droits réservés,Créer par<a href="https://www.facebook.com/profile.php?id=61578476155133"
                            target="_blank">Eben-Ezer SISSOU</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════
         PANNEAU DROIT — Formulaire
    ══════════════════════════════════════ --}}
        <div class="flex-1 flex flex-col items-center justify-center
                px-6 py-12 lg:px-12 xl:px-16
                bg-white dark:bg-slate-900">

            {{-- Toggle theme --}}
            <div class="absolute top-5 right-5">
                <button id="theme-toggle" class="p-2.5 rounded-xl text-slate-500 dark:text-slate-400
                           hover:bg-slate-100 dark:hover:bg-slate-800
                           hover:text-amber-500 dark:hover:text-amber-400
                           transition-all duration-200 focus:outline-none border
                           border-slate-200 dark:border-slate-700" aria-label="Changer le thème">
                    <i id="icon-sun" class="bi bi-sun-fill text-lg"></i>
                    <i id="icon-moon" class="bi bi-moon-stars-fill text-lg hidden"></i>
                </button>
            </div>

            {{-- Logo mobile --}}
            <div class="lg:hidden flex items-center gap-3 mb-10">
                <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-md">
                    <i class="bi bi-mortarboard-fill text-white text-lg"></i>
                </div>
                <div>
                    <p class="font-bold text-slate-800 dark:text-slate-100 leading-tight">
                        {{ $schoolSettings['school_name'] ?? config('app.name') }}
                    </p>
                    <p class="text-xs text-slate-400">Gestion scolaire</p>
                </div>
            </div>

            {{-- Formulaire --}}
            <div class="w-full max-w-md">

                {{-- Titre --}}
                <div class="mb-8">
                    <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mb-1">
                        Connexion
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Entrez vos identifiants pour accéder à votre espace
                    </p>
                </div>

                {{-- Status session (reset password, etc.) --}}
                @if(session('status'))
                <div class="mb-5 flex items-start gap-3 px-4 py-3.5 rounded-xl
                        bg-emerald-50 dark:bg-emerald-900/20
                        border border-emerald-200 dark:border-emerald-800">
                    <i class="bi bi-check-circle-fill text-emerald-500 shrink-0 mt-0.5"></i>
                    <p class="text-sm text-emerald-700 dark:text-emerald-400">
                        {{ session('status') }}
                    </p>
                </div>
                @endif

                {{-- Erreurs globales --}}
                @if($errors->any())
                <div class="mb-5 flex items-start gap-3 px-4 py-3.5 rounded-xl
                        bg-red-50 dark:bg-red-900/20
                        border border-red-200 dark:border-red-800">
                    <i class="bi bi-exclamation-circle-fill text-red-500 shrink-0 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-semibold text-red-700 dark:text-red-400 mb-1">
                            Identifiants incorrects
                        </p>
                        @foreach($errors->all() as $error)
                        <p class="text-xs text-red-600 dark:text-red-400">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}" id="login-form" novalidate>
                    @csrf

                    <div class="space-y-5">

                        {{-- Email --}}
                        <div>
                            <label for="email" class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200 mb-1.5">
                                <i class="bi bi-envelope text-slate-400 text-xs"></i>
                                Adresse e-mail
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                    <i class="bi bi-envelope text-slate-400 text-sm"></i>
                                </span>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                    autofocus autocomplete="email" placeholder="votre@email.com"
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border text-sm
                                          text-slate-800 dark:text-slate-100
                                          bg-white dark:bg-slate-800
                                          placeholder:text-slate-400
                                          focus:outline-none focus:ring-2 transition
                                          {{ $errors->has('email')
                                              ? 'border-red-500 focus:ring-red-500/40 focus:border-red-500'
                                              : 'border-slate-300 dark:border-slate-600 focus:ring-blue-600/40 focus:border-blue-600' }}" />
                            </div>
                            @error('email')
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Mot de passe --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="password" class="flex items-center gap-1.5 text-sm font-medium
                                          text-slate-700 dark:text-slate-200">
                                    <i class="bi bi-lock text-slate-400 text-xs"></i>
                                    Mot de passe
                                    <span class="text-red-500">*</span>
                                </label>
                                @if(Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-blue-600 dark:text-blue-400
                                      hover:underline font-medium">
                                    Mot de passe oublié ?
                                </a>
                                @endif
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                    <i class="bi bi-lock text-slate-400 text-sm"></i>
                                </span>
                                <input type="password" id="password" name="password" required
                                    autocomplete="current-password" placeholder="••••••••"
                                    class="w-full pl-10 pr-12 py-3 rounded-xl border text-sm
                                          text-slate-800 dark:text-slate-100
                                          bg-white dark:bg-slate-800
                                          placeholder:text-slate-400
                                          focus:outline-none focus:ring-2 transition
                                          {{ $errors->has('password')
                                              ? 'border-red-500 focus:ring-red-500/40 focus:border-red-500'
                                              : 'border-slate-300 dark:border-slate-600 focus:ring-blue-600/40 focus:border-blue-600' }}" />
                                {{-- Toggle visibility --}}
                                <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 flex items-center pr-3.5
                                           text-slate-400 hover:text-slate-600
                                           dark:hover:text-slate-300 transition-colors
                                           focus:outline-none">
                                    <i id="pw-eye" class="bi bi-eye text-sm"></i>
                                </button>
                            </div>
                            @error('password')
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Se souvenir de moi --}}
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-slate-300 dark:border-slate-600
                                          text-blue-600 focus:ring-blue-500 focus:ring-2
                                          dark:bg-slate-800 cursor-pointer" />
                                <span class="text-sm text-slate-600 dark:text-slate-300">
                                    Se souvenir de moi
                                </span>
                            </label>
                        </div>

                        {{-- Bouton submit --}}
                        <button type="submit" id="login-btn" class="w-full flex items-center justify-center gap-2.5
                                   px-6 py-3.5 rounded-xl text-sm font-semibold text-white
                                   bg-blue-600 hover:bg-blue-700
                                   shadow-sm shadow-blue-600/30
                                   transition-all duration-200 focus:outline-none
                                   focus:ring-2 focus:ring-blue-600/50
                                   disabled:opacity-60 disabled:cursor-not-allowed">
                            <i class="bi bi-box-arrow-in-right text-base" id="login-icon"></i>
                            <span id="login-label">Se connecter</span>
                        </button>
                    </div>
                </form>

                {{-- Lien inscription --}}
                @if(Route::has('register'))
                <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700 text-center">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Pas encore de compte ?
                        <a href="{{ route('register') }}"
                            class="text-blue-600 dark:text-blue-400 font-semibold hover:underline ml-1">
                            Créer un compte
                        </a>
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // ── Toggle mot de passe visible ───────────────────────────────
        const toggleBtn = document.getElementById('toggle-password');
        const pwInput = document.getElementById('password');
        const pwEye = document.getElementById('pw-eye');

        toggleBtn?.addEventListener('click', function() {
            const isPassword = pwInput.type === 'password';
            pwInput.type = isPassword ? 'text' : 'password';
            pwEye.className = isPassword ?
                'bi bi-eye-slash text-sm' :
                'bi bi-eye text-sm';
        });

        // ── État loading du bouton ────────────────────────────────────
        const form = document.getElementById('login-form');
        const loginBtn = document.getElementById('login-btn');
        const loginIcon = document.getElementById('login-icon');
        const loginLabel = document.getElementById('login-label');

        form?.addEventListener('submit', function() {
            if (loginBtn) {
                loginBtn.disabled = true;
                loginIcon.className = 'bi bi-arrow-repeat animate-spin text-base';
                loginLabel.textContent = 'Connexion…';
            }
        });

        // ── Dark mode (réutilise theme.js via l'event) ────────────────
        const themeToggle = document.getElementById('theme-toggle');
        const iconSun = document.getElementById('icon-sun');
        const iconMoon = document.getElementById('icon-moon');
        const html = document.documentElement;

        function applyTheme(dark) {
            html.classList.toggle('dark', dark);
            iconSun?.classList.toggle('hidden', dark);
            iconMoon?.classList.toggle('hidden', !dark);
            localStorage.setItem('theme', dark ? 'dark' : 'light');
        }

        // Init
        applyTheme(
            localStorage.getItem('theme') === 'dark' ||
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)
        );

        themeToggle?.addEventListener('click', function() {
            applyTheme(!html.classList.contains('dark'));
        });
    });
    </script>

</body>

</html>