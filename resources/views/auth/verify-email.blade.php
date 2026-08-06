<x-guest-layout>
    <div class="flex flex-col items-center text-center">

        {{-- Icône --}}
        <div class="w-16 h-16 rounded-2xl bg-linear-to-br from-blue-600 to-emerald-500
                    flex items-center justify-center shadow-lg mb-5">
            <i class="bi bi-envelope-paper-fill text-white text-2xl"></i>
        </div>

        {{-- Titre --}}
        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">
            Vérifiez votre adresse e-mail
        </h2>

        {{-- Texte explicatif --}}
        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-sm">
            Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre
            adresse e-mail en cliquant sur le lien que nous venons de vous envoyer ?
            Si vous ne l'avez pas reçu, nous pouvons vous en renvoyer un autre.
        </p>

        {{-- Message de confirmation d'envoi --}}
        @if (session('status') == 'verification-link-sent')
        <div class="mt-5 w-full flex items-start gap-3 px-4 py-3.5 rounded-xl
                        bg-emerald-50 dark:bg-emerald-900/20
                        border border-emerald-200 dark:border-emerald-800
                        text-emerald-700 dark:text-emerald-400">
            <i class="bi bi-check-circle-fill text-lg shrink-0 mt-0.5"></i>
            <p class="text-sm font-medium text-left">
                Un nouveau lien de vérification a été envoyé à l'adresse e-mail
                fournie lors de votre inscription.
            </p>
        </div>
        @endif

        {{-- Actions --}}
        <div class="w-full mt-7 flex flex-col sm:flex-row items-center justify-center gap-3">

            <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                               bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                               shadow-sm hover:shadow-md transition-all duration-200
                               focus:outline-none focus:ring-2 focus:ring-blue-600/40">
                    <i class="bi bi-arrow-repeat"></i>
                    Renvoyer l'e-mail de vérification
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                               text-slate-500 dark:text-slate-400 text-sm font-medium
                               border border-slate-200 dark:border-slate-700
                               hover:bg-slate-50 dark:hover:bg-slate-800
                               hover:text-slate-700 dark:hover:text-slate-200
                               transition-all duration-200
                               focus:outline-none focus:ring-2 focus:ring-slate-300/40">
                    <i class="bi bi-box-arrow-right"></i>
                    Déconnexion
                </button>
            </form>

        </div>
    </div>
</x-guest-layout>