<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Inscription | {{ config('app.name', 'School ERP') }}</title>

    @php $favicon = $schoolSettings['favicon'] ?? ''; @endphp
    @if($favicon)
    <link rel="icon" href="{{ asset('storage/' . $favicon) }}" />
    @else
    <link rel="icon" href="{{ asset('favicon.ico') }}" />
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />-->
    <link rel="stylesheet" href="{{ asset('asset/css/bootstrap-icons.min.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 dark:bg-slate-900 antialiased font-inter">

    <div class="min-h-screen flex">

        {{-- ══════════════════════════════════════
         PANNEAU GAUCHE — Illustration
    ══════════════════════════════════════ --}}
        <div class="hidden lg:flex lg:w-2/5 relative overflow-hidden
                bg-linear-to-br from-emerald-500 via-emerald-600 to-blue-600">

            {{-- Motif --}}
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse">
                            <circle cx="2" cy="2" r="1.5" fill="white" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#dots)" />
                </svg>
            </div>

            <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full bg-white/5 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full bg-blue-400/10 blur-3xl"></div>

            <div class="relative z-10 flex flex-col justify-between p-10 w-full">

                {{-- Brand --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm
                            flex items-center justify-center">
                        <i class="bi bi-mortarboard-fill text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-white font-bold text-lg leading-tight">
                            {{ $schoolSettings['school_name'] ?? config('app.name') }}
                        </p>
                        <p class="text-emerald-200 text-xs">Gestion scolaire</p>
                    </div>
                </div>

                {{-- Central --}}
                <div class="text-center">
                    <div class="w-20 h-20 rounded-3xl bg-white/15 backdrop-blur-sm
                            flex items-center justify-center mx-auto mb-6 shadow-xl">
                        <i class="bi bi-person-plus-fill text-white text-4xl"></i>
                    </div>
                    <h2 class="text-3xl font-extrabold text-white mb-3 leading-tight">
                        Rejoignez<br />l'espace scolaire
                    </h2>
                    <p class="text-emerald-200 text-base max-w-xs mx-auto leading-relaxed">
                        Créez votre compte étudiant et accédez à vos notes, bulletins et présences.
                    </p>

                    {{-- Étapes --}}
                    <div class="mt-8 space-y-3 text-left max-w-xs mx-auto">
                        @foreach([
                        ['step' => '1', 'text' => 'Renseignez vos informations'],
                        ['step' => '2', 'text' => 'Votre compte est créé instantanément'],
                        ['step' => '3', 'text' => 'Accédez à votre tableau de bord'],
                        ] as $step)
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center
                                    text-white text-xs font-bold shrink-0">
                                {{ $step['step'] }}
                            </div>
                            <p class="text-emerald-100 text-sm">{{ $step['text'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                <p class="text-emerald-300 text-xs text-center">
                    © {{ date('Y') }} {{ $schoolSettings['school_name'] ?? 'School ERP' }}
                </p>
            </div>
        </div>

        {{-- ══════════════════════════════════════
         PANNEAU DROIT — Formulaire
    ══════════════════════════════════════ --}}
        <div class="flex-1 flex flex-col items-center justify-center
                px-6 py-12 lg:px-12
                bg-white dark:bg-slate-900 overflow-y-auto">

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
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-blue-600
                        flex items-center justify-center shadow-md">
                    <i class="bi bi-mortarboard-fill text-white text-lg"></i>
                </div>
                <p class="font-bold text-slate-800 dark:text-slate-100">
                    {{ $schoolSettings['school_name'] ?? config('app.name') }}
                </p>
            </div>

            <div class="w-full max-w-md">

                {{-- Titre --}}
                <div class="mb-7">
                    <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mb-1">
                        Créer un compte
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Votre compte aura le rôle
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">Étudiant</span>
                        par défaut
                    </p>
                </div>

                {{-- Erreurs globales --}}
                @if($errors->any())
                <div class="mb-5 flex items-start gap-3 px-4 py-3.5 rounded-xl
                        bg-red-50 dark:bg-red-900/20
                        border border-red-200 dark:border-red-800">
                    <i class="bi bi-exclamation-circle-fill text-red-500 shrink-0 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-semibold text-red-700 dark:text-red-400 mb-1">
                            Veuillez corriger les erreurs suivantes :
                        </p>
                        @foreach($errors->all() as $error)
                        <p class="text-xs text-red-600 dark:text-red-400">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('register') }}" id="register-form" novalidate>
                    @csrf

                    <div class="space-y-4">

                        {{-- Nom complet --}}
                        <div>
                            <label for="name" class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200 mb-1.5">
                                <i class="bi bi-person text-slate-400 text-xs"></i>
                                Nom complet
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                    <i class="bi bi-person text-slate-400 text-sm"></i>
                                </span>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                                    autocomplete="name" placeholder="Prénom et Nom"
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border text-sm
                                          text-slate-800 dark:text-slate-100
                                          bg-white dark:bg-slate-800
                                          placeholder:text-slate-400
                                          focus:outline-none focus:ring-2 transition
                                          {{ $errors->has('name')
                                              ? 'border-red-500 focus:ring-red-500/40 focus:border-red-500'
                                              : 'border-slate-300 dark:border-slate-600 focus:ring-blue-600/40 focus:border-blue-600' }}" />
                            </div>
                            @error('name')
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

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
                                    autocomplete="email" placeholder="votre@email.com"
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
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Mot de passe --}}
                        <div>
                            <label for="password" class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200 mb-1.5">
                                <i class="bi bi-lock text-slate-400 text-xs"></i>
                                Mot de passe
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                    <i class="bi bi-lock text-slate-400 text-sm"></i>
                                </span>
                                <input type="password" id="password" name="password" required
                                    autocomplete="new-password" placeholder="Min. 8 caractères"
                                    class="w-full pl-10 pr-12 py-3 rounded-xl border text-sm
                                          text-slate-800 dark:text-slate-100
                                          bg-white dark:bg-slate-800
                                          placeholder:text-slate-400
                                          focus:outline-none focus:ring-2 transition
                                          {{ $errors->has('password')
                                              ? 'border-red-500 focus:ring-red-500/40 focus:border-red-500'
                                              : 'border-slate-300 dark:border-slate-600 focus:ring-blue-600/40 focus:border-blue-600' }}" />
                                <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 flex items-center pr-3.5
                                           text-slate-400 hover:text-slate-600
                                           dark:hover:text-slate-300 transition-colors focus:outline-none">
                                    <i id="pw-eye" class="bi bi-eye text-sm"></i>
                                </button>
                            </div>

                            {{-- Barre de force du mot de passe --}}
                            <div class="mt-2 space-y-1.5" id="pw-strength-wrapper">
                                <div class="flex gap-1">
                                    <div class="h-1 flex-1 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                        <div id="pw-bar-1" class="h-full rounded-full transition-all duration-300 w-0">
                                        </div>
                                    </div>
                                    <div class="h-1 flex-1 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                        <div id="pw-bar-2" class="h-full rounded-full transition-all duration-300 w-0">
                                        </div>
                                    </div>
                                    <div class="h-1 flex-1 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                        <div id="pw-bar-3" class="h-full rounded-full transition-all duration-300 w-0">
                                        </div>
                                    </div>
                                    <div class="h-1 flex-1 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                        <div id="pw-bar-4" class="h-full rounded-full transition-all duration-300 w-0">
                                        </div>
                                    </div>
                                </div>
                                <p id="pw-strength-label" class="text-[11px] text-slate-400 dark:text-slate-500">
                                    Entrez un mot de passe
                                </p>
                            </div>

                            @error('password')
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Confirmation mot de passe --}}
                        <div>
                            <label for="password_confirmation" class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200 mb-1.5">
                                <i class="bi bi-shield-lock text-slate-400 text-xs"></i>
                                Confirmer le mot de passe
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                    <i class="bi bi-shield-lock text-slate-400 text-sm"></i>
                                </span>
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                    autocomplete="new-password" placeholder="Répétez le mot de passe" class="w-full pl-10 pr-10 py-3 rounded-xl border text-sm
                                          text-slate-800 dark:text-slate-100
                                          bg-white dark:bg-slate-800
                                          placeholder:text-slate-400
                                          focus:outline-none focus:ring-2 transition
                                          border-slate-300 dark:border-slate-600
                                          focus:ring-blue-600/40 focus:border-blue-600" />
                                {{-- Icône correspondance --}}
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                                    <i id="confirm-icon"
                                        class="bi bi-circle text-slate-300 dark:text-slate-600 text-sm"></i>
                                </span>
                            </div>
                            @error('password_confirmation')
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Rôle info (readonly, informatif) --}}
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl
                                bg-emerald-50 dark:bg-emerald-900/20
                                border border-emerald-200 dark:border-emerald-800">
                            <i
                                class="bi bi-mortarboard-fill text-emerald-600 dark:text-emerald-400 text-lg shrink-0"></i>
                            <div>
                                <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">
                                    Rôle : Étudiant
                                </p>
                                <p class="text-xs text-emerald-600/80 dark:text-emerald-500 mt-0.5">
                                    Votre compte aura accès à vos bulletins, notes et présences.
                                    Contactez l'administration pour un autre rôle.
                                </p>
                            </div>
                        </div>

                        {{-- Bouton submit --}}
                        <button type="submit" id="register-btn" class="w-full flex items-center justify-center gap-2.5
                                   px-6 py-3.5 rounded-xl text-sm font-semibold text-white
                                   bg-emerald-600 hover:bg-emerald-700
                                   shadow-sm shadow-emerald-600/30
                                   transition-all duration-200 focus:outline-none
                                   focus:ring-2 focus:ring-emerald-600/50
                                   disabled:opacity-60 disabled:cursor-not-allowed">
                            <i class="bi bi-person-check-fill text-base" id="register-icon"></i>
                            <span id="register-label">Créer mon compte</span>
                        </button>
                    </div>
                </form>

                {{-- Lien connexion --}}
                <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700 text-center">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Déjà inscrit ?
                        <a href="{{ route('login') }}"
                            class="text-blue-600 dark:text-blue-400 font-semibold hover:underline ml-1">
                            Se connecter
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // ── Toggle mot de passe visible ────────────────────────────────
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

        // ── Force du mot de passe ──────────────────────────────────────
        const bars = [1, 2, 3, 4].map(i => document.getElementById('pw-bar-' + i));
        const label = document.getElementById('pw-strength-label');

        const strengths = [{
                score: 1,
                color: 'bg-red-500',
                text: 'Très faible',
                textColor: 'text-red-500'
            },
            {
                score: 2,
                color: 'bg-amber-500',
                text: 'Faible',
                textColor: 'text-amber-500'
            },
            {
                score: 3,
                color: 'bg-blue-500',
                text: 'Moyen',
                textColor: 'text-blue-500'
            },
            {
                score: 4,
                color: 'bg-emerald-500',
                text: 'Fort',
                textColor: 'text-emerald-500'
            },
        ];

        function getStrength(pw) {
            let score = 0;
            if (pw.length >= 8) score++;
            if (/[A-Z]/.test(pw)) score++;
            if (/[0-9]/.test(pw)) score++;
            if (/[^A-Za-z0-9]/.test(pw)) score++;
            return score;
        }

        pwInput?.addEventListener('input', function() {
            const pw = this.value;
            const score = pw.length === 0 ? 0 : getStrength(pw);

            bars.forEach(function(bar, i) {
                if (!bar) return;
                if (score > 0 && i < score) {
                    bar.className = 'h-full rounded-full transition-all duration-300 w-full ' +
                        strengths[score - 1].color;
                } else {
                    bar.className = 'h-full rounded-full transition-all duration-300 w-0';
                }
            });

            if (label) {
                if (pw.length === 0) {
                    label.textContent = 'Entrez un mot de passe';
                    label.className = 'text-[11px] text-slate-400 dark:text-slate-500';
                } else {
                    label.textContent = strengths[score - 1]?.text ?? '';
                    label.className = 'text-[11px] ' + (strengths[score - 1]?.textColor ?? '');
                }
            }

            // Re-vérifier la correspondance
            checkConfirm();
        });

        // ── Correspondance mot de passe ────────────────────────────────
        const confirmInput = document.getElementById('password_confirmation');
        const confirmIcon = document.getElementById('confirm-icon');

        function checkConfirm() {
            if (!confirmInput || !confirmInput.value) return;
            const match = pwInput.value === confirmInput.value;
            if (confirmIcon) {
                confirmIcon.className = match ?
                    'bi bi-check-circle-fill text-emerald-500 text-sm' :
                    'bi bi-x-circle-fill text-red-500 text-sm';
            }
        }

        confirmInput?.addEventListener('input', checkConfirm);

        // ── Loading button ─────────────────────────────────────────────
        const form = document.getElementById('register-form');
        const registerBtn = document.getElementById('register-btn');
        const registerIcon = document.getElementById('register-icon');
        const registerLabel = document.getElementById('register-label');

        form?.addEventListener('submit', function() {
            if (registerBtn) {
                registerBtn.disabled = true;
                registerIcon.className = 'bi bi-arrow-repeat animate-spin text-base';
                registerLabel.textContent = 'Création du compte…';
            }
        });

        // ── Dark mode ─────────────────────────────────────────────────
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

        applyTheme(
            localStorage.getItem('theme') === 'dark' ||
            (!localStorage.getItem('theme') &&
                window.matchMedia('(prefers-color-scheme: dark)').matches)
        );

        themeToggle?.addEventListener('click', function() {
            applyTheme(!html.classList.contains('dark'));
        });
    });
    </script>

</body>

</html>