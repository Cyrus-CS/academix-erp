<form action="{{ $action }}" method="POST" @if($enctype) enctype="{{ $enctype }}" @endif
    autocomplete="{{ $autocomplete }}" {{ $attributes }}>

    @csrf

    @if($isEdit)

    @method('PUT')

    @endif

    {{ $slot }}

</form>