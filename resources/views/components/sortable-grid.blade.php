<div id="{{ $gridId }}" data-sortable-url="{{ $sortableUrl }}" @if($handle) data-sortable-handle="{{ $handle }}" @endif
    {{ $attributes->merge(['class' => $class]) }}>
    {{ $slot }}
</div>