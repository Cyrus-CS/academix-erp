<!DOCTYPE html>
<html class="h-full">
@include('layouts.partials.head')

<body class="h-full bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 antialiased">

    {{---------------- Page Loader Bar -------------- --}}
    <div id="page-loader"></div>

    {{-- ------------------- Layout Wrapper ------------------- --}}
    <div class="flex h-full min-h-screen">

        {{-- ------------------- Sidebar ------------------- --}}
        @include('layouts.partials.sidebar')

        {{-- ------------------- Main Content Area ------------------- --}}
        <div class="flex flex-col flex-1 min-w-0 transition-all duration-300 ease-in-out"
            :class="isCollapsed ? 'ml-18' : 'ml-65'">
            {{-- ------------------- Top Navbar ------------------- --}}
            @include('layouts.partials.navbar')

            {{-- ------------------- Flash Messages ------------------- --}}
            @include('layouts.partials.flash-messages')

            {{-- ------------------- Validation Errors ------------------- --}}
            @if($errors->any())
            <div class="mx-4 mt-4 sm:mx-6 flex items-start gap-3 px-4 py-3.5 rounded-xl
                                bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800
                                text-red-700 dark:text-red-400">
                <i class="bi bi-exclamation-circle-fill text-lg shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-sm font-semibold mb-1">
                        Veuillez corriger les erreurs suivantes :
                    </p>
                    <ul class="text-xs space-y-0.5 list-disc list-inside">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- ------------------- Page Header (optionnel) ------------------- --}}
            @hasSection('page_header')
            <div class="px-4 sm:px-6 pt-6 pb-2">
                @yield('page_header')
            </div>
            @endif

            {{-- ------------------- Main Content ------------------- --}}
            <main
                class="flex flex-col flex-1 px-4 sm:px-6 py-6 overflow-auto min-w-0 transition-all duration-300 ease-in-out ml-65"
                id="main-content">
                @yield('content')
            </main>

            {{-- ------------------- Footer ------------------- --}}
            @include('layouts.partials.footer')

        </div>{{-- end main content area --}}

    </div>{{-- end layout wrapper --}}

    {{-- ------------------- Modal Global Search (Ctrl+K) ------------------- --}}
    @include('layouts.partials.modals.global-search')

    {{-- ------------------- Mobile Sidebar Overlay ------------------- --}}
    <div x-data @click="$dispatch('close-sidebar')" x-show="$store.sidebar?.mobileOpen"
        x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 lg:hidden"></div>

    {{-- ------------------- Scripts ------------------- --}}
    {{-- @include('layouts.partials.scripts') --}}
    @stack('scripts')
</body>

</html>