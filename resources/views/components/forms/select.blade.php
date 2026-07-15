@php
$inputId = $attributes->get('id', $name);
$hasError = $errors->has($name);
$fieldName = $multiple ? $name . '[]' : $name;
$rawValue = old($name, $value);
$resolvedValue = $multiple ? (array) $rawValue : $rawValue;
@endphp

<div class="w-full {{ $wrapperClass }}">
    <x-forms.label :name="$inputId" :label="$label" :required="$required" :icon="$icon" />

    <div class="relative">
        @if($icon)
        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none z-10">
            <i class="bi {{ $icon }} text-slate-400"></i>
        </span>
        @endif

        <select name="{{ $fieldName }}" id="{{ $inputId }}" @if($required) required @endif @if($disabled) disabled
            @endif @if($multiple) multiple @endif {{ $attributes->except(['id', 'class'])->merge([
                'class' => 'w-full rounded-lg border text-sm text-slate-800 dark:text-slate-100
                    bg-white dark:bg-slate-800 py-2.5 appearance-none
                    focus:outline-none focus:ring-2 transition
                    disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed dark:disabled:bg-slate-900
                    ' . ($icon ? 'pl-10 pr-9' : 'pl-3.5 pr-9') . ' '
                    . ($hasError
                        ? 'border-red-500 focus:ring-red-500/40 focus:border-red-500'
                        : 'border-slate-300 dark:border-slate-600 focus:ring-blue-600/40 focus:border-blue-600')
            ]) }}>
            @if(!$multiple && $placeholder)
            <option value="" @selected($rawValue===null || $rawValue==='' )>{{ $placeholder }}</option>
            @endif

            @foreach($options as $key => $option)
            @php
            // Supporte : tableau associatif [valeur => libellé] OU collection d'objets/models Eloquent
            if (is_object($option) || is_array($option)) {
            $optVal = data_get($option, $optionValue);
            $optLabel = data_get($option, $optionLabel);
            } else {
            $optVal = $key;
            $optLabel = $option;
            }

            $isSelected = $multiple
            ? in_array((string) $optVal, array_map('strval', $resolvedValue), true)
            : (string) $optVal === (string) $resolvedValue;
            @endphp
            <option value="{{ $optVal }}" @selected($isSelected)>{{ $optLabel }}</option>
            @endforeach
        </select>

        @unless($multiple)
        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
            <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
        </span>
        @endunless
    </div>

    <x-forms.error :name="$name" />
    <x-forms.help :help="$help" :name="$name" />
</div>