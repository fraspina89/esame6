<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


/**
 * Middleware: ContattoRuolo
 *
 * Verifica che il contatto autenticato possieda almeno uno dei ruoli
 * richiesti per l'accesso alla rotta. I ruoli vengono letti da
 * `$request->attributes->get('contattiRuoli')` (impostato da Autenticazione)
 * o dalla input request `contattiRuoli`.
 */
class ContattoRuolo
{
    /**
     * Gestisce la richiesta in entrata.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$requiredRuoli)
    {
        // Legge i ruoli dagli attributi della richiesta (impostati da Autenticazione) o dall'input
        $contattiRuoli = $request->attributes->get('contattiRuoli', $request->input('contattiRuoli', []));
        if (!is_array($contattiRuoli)) {
            if (is_string($contattiRuoli)) {
                $contattiRuoli = array_filter(array_map('trim', explode(',', $contattiRuoli)));
            } else {
                $contattiRuoli = [];
            }
        }

        abort_if(0 === count(array_intersect($requiredRuoli, $contattiRuoli)), 403, 'MD_0001');
        return $next($request);
    }
}