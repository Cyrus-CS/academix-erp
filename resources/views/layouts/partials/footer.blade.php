<footer class="px-4 sm:px-6 py-4 border-t border-slate-200 dark:border-slate-700
               bg-white/60 dark:bg-slate-800/60 backdrop-blur-sm">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2
                text-xs text-slate-400 dark:text-slate-500">
        <p>
            &copy; {{ date('Y') }}
            <span class="font-semibold text-blue-600 dark:text-blue-400">School ERP</span>
            | Tous droits réservés
        </p>
        <p>
            Développé avec
            <i class="bi bi-heart-fill text-red-500 text-xs mx-0.5"></i>
            par
            <span class="font-semibold text-slate-600 dark:text-slate-300">Eben-Ezer Sissou | <a
                    href="https://web.facebook.com/profile.php?id=61578476155133">suivez sa page
                    Facebook </a> </span>
            &nbsp;·&nbsp; Laravel {{ app()->version() }}
        </p>
    </div>
</footer>