@php
$inputId = $attributes->get('id', $name);
$hasError = $errors->has($name);
$resolvedValue = old($name, $value);
@endphp

<div class="w-full {{ $class }}">
    <x-forms.label :name="$inputId" :label="$label" :required="$required" :icon="$icon" />

    <div class="relative">
        @if($icon)
        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
            <i class="bi {{ $icon }} text-slate-400"></i>
        </span>
        @endif

        <input type="{{ $type }}" name="{{ $name }}" id="{{ $inputId }}" value="{{ $resolvedValue }}" @if($placeholder)
            placeholder="{{ $placeholder }}" @endif @if($required) required @endif @if($disabled) disabled @endif
            @if($readonly) readonly @endif {{ $attributes->except(['id', 'class'])->merge([
                'class' => 'w-full rounded-lg border text-sm text-slate-800 dark:text-slate-100
                    bg-white dark:bg-slate-800 placeholder:text-slate-400
                    focus:outline-none focus:ring-2 transition
                    disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed dark:disabled:bg-slate-900
                    py-2.5 ' . ($icon ? 'pl-10 pr-3.5' : 'px-3.5') . ' '
                    . ($hasError
                        ? 'border-red-500 focus:ring-red-500/40 focus:border-red-500'
                        : 'border-slate-300 dark:border-slate-600 focus:ring-blue-600/40 focus:border-blue-600')
            ]) }}>
    </div>

    <x-forms.error :name="$name" />
    <x-forms.help :help="$help" :name="$name" />
</div>