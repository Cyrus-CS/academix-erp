<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description" content="Academix ERP | Système de gestion scolaire" />

    <title>
        @yield('title', config('app.name', 'Academix ERP'))
        |
        @yield('page_title', 'Tableau de bord')
    </title>

    {{-- ── Favicon ── --}}
    <link rel="icon" type="image/png+xml" href="{{ asset('favicon.png') }}" />

    {{-- ── Fonts : Inter ── --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />

    {{-- ── Bootstrap Icons ── --}}
    <link rel="stylesheet" href="{{ asset('asset/css/bootstrap-icons.min.css') }}" />

    {{-- ── Chart.js ── --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>

    {{-- ── SortableJS ── --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>

    {{-- ── Flatpickr ── --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    {{-- ── Vite : Tailwind CSS + App JS ── --}}
    {{-- !! DOIT ÊTRE EN PREMIER DANS LE HEAD, AVANT TOUT CSS !! --}}
    <script>
    (function() {
        var stored = localStorage.getItem('theme');
        var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        var isDark = stored === 'dark' || (!stored && prefersDark);
        if (isDark) {
            document.documentElement.classList.add('dark');
        }
    })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{---------- Styles additionnels par page -------}}
    @stack('styles')
</head>