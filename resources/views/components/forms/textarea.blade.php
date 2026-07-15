@php
$inputId = $attributes->get('id', $name);
$hasError = $errors->has($name);
$resolvedValue = old($name, $value);
@endphp

<div class="w-full {{ $wrapperClass }}">
    <x-forms.label :name="$inputId" :label="$label" :required="$required" :icon="$icon" />

    <textarea name="{{ $name }}" id="{{ $inputId }}" rows="{{ $rows }}" @if($placeholder)
        placeholder="{{ $placeholder }}" @endif @if($required) required @endif @if($disabled) disabled @endif {{ $attributes->except(['id', 'class'])->merge([
            'class' => 'w-full rounded-lg border text-sm text-slate-800 dark:text-slate-100
                bg-white dark:bg-slate-800 px-3.5 py-2.5 placeholder:text-slate-400
                focus:outline-none focus:ring-2 transition resize-y
                disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed dark:disabled:bg-slate-900 '
                . ($hasError
                    ? 'border-red-500 focus:ring-red-500/40 focus:border-red-500'
                    : 'border-slate-300 dark:border-slate-600 focus:ring-blue-600/40 focus:border-blue-600')
        ]) }}>{{ $resolvedValue }}</textarea>

    <x-forms.error :name="$name" />
    <x-forms.help :help="$help" :name="$name" />
</div>