@error($name)
<p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
    <i class="bi bi-exclamation-circle-fill"></i>
    {{ $message }}
</p>
@enderror