@props([
'name' => null,
'label' => null,
'required' => false,
'icon' => null,
])

@if($label)
<label for="{{ $name }}"
    class="flex items-center gap-1.5 text-sm font-medium text-slate-700 dark:text-slate-200 mb-1.5">
    @if($icon)
    <i class="bi {{ $icon }} text-slate-400"></i>
    @endif
    {{ $label }}
    @if($required)
    <span class="text-red-500">*</span>
    @endif
</label>
@endif