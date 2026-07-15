@if($help && !$errors->has($name))
<p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
    {{ $help }}
</p>
@endif