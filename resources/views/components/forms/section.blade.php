@props([
'title',
'description' => null,
'icon',
'color' => 'blue'
])

<div
    {{ $attributes->merge(['class' => "bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden"]) }}>

    <div class="flex items-center gap-3 px-6 py-4
        border-b border-slate-100 dark:border-slate-700
        bg-slate-50/50 dark:bg-slate-700/20">

        <div class="w-8 h-8 rounded-lg
            bg-{{ $color }}-100
            dark:bg-{{ $color }}-900/40
            flex items-center justify-center">

            <i class="bi {{ $icon }}
                text-{{ $color }}-600
                dark:text-{{ $color }}-400
                text-sm">
            </i>

        </div>

        <div>

            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">

                {{ $title }}

            </h2>

            @if($description)

            <p class="text-xs text-slate-400">

                {{ $description }}

            </p>

            @endif

        </div>

    </div>

    <div class="p-6">

        {{ $slot }}

    </div>

</div>