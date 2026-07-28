<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StatelessApi
{
    /**
     * Désactive l'écriture de session pour les requêtes AJAX légères.
     * Évite le lock de session qui bloque les autres requêtes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Passer en session read-only
        config(['session.driver' => 'array']);

        return $next($request);
    }
}