<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\AccediController;
use App\Models\Contatto;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware: Autenticazione
 *
 * Verifica il token Bearer JWT inviato nella richiesta, valida il payload
 * e, in caso di successo, effettua il login del `Contatto` e imposta i
 * ruoli nel `Request` tramite l'attributo `contattiRuoli`.
 * In caso di token non valido o contatto non attivo aborta con codice TK_xxx.
 */
class Autenticazione
{
    /**
     * Gestisce la richiesta in entrata.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // leggere il token dal Bearer Authorization header (Laravel helper)
        $token = $request->bearerToken();

        $payload = AccediController::verificaToken($token);
        if ($payload != null) {
            $contatto = Contatto::where('idContatto', $payload->data->idContatto)->firstOrFail();
            if ($contatto->idContattoStato == 1) {
            Auth::login($contatto);
            $ruoli = $contatto->ruoli->pluck('nome')->toArray();
            $request->attributes->set('contattiRuoli', $ruoli);
            return $next($request);
        } else {
            abort(403, 'TK_0002');
        }    
    } else {
        abort(403, 'TK_0001');
    }
}
}
