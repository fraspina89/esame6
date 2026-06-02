<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ContattoAbilitaStoreRequest;
use App\Http\Requests\Api\V1\ContattoAbilitaUpdateRequest;
use App\Http\Resources\Api\V1\ContattoAbilitaResource;
use App\Http\Resources\Api\V1\ContattoAbilitaCollection;
use App\Models\ContattoAbilita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Controller per le abilità associate ai contatti
 *
 * Gestisce l'elenco e i dettagli delle abilità disponibili per i contatti.
 */
class ContattoAbilitaController extends Controller
{
    /**
     * Restituisce l'elenco delle abilità.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Gate::allows('leggere')) {
            abort(403);
        }

        $abilita = ContattoAbilita::all();
        
        return new ContattoAbilitaCollection($abilita);
    }

    /**
     * Crea una nuova abilità.
     *
     * @param  ContattoAbilitaStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContattoAbilitaStoreRequest $request)
    {
        if (!Gate::allows('creare')) {
            abort(403);
        }

        $abilita = ContattoAbilita::create($request->validated());
        
        return new ContattoAbilitaResource($abilita);
    }

    /**
     * Restituisce un'abilità specifica.
     *
     * @param  ContattoAbilita  $contattoAbilita
     * @return \Illuminate\Http\Response
     */
    public function show(ContattoAbilita $contattoAbilita)
    {
        if (!Gate::allows('leggere')) {
            abort(403);
        }

        return new ContattoAbilitaResource($contattoAbilita);
    }

    /**
     * Aggiorna un'abilità esistente.
     *
     * @param  ContattoAbilitaUpdateRequest  $request
     * @param  ContattoAbilita  $contattoAbilita
     * @return \Illuminate\Http\Response
     */
    public function update(ContattoAbilitaUpdateRequest $request, ContattoAbilita $contattoAbilita)
    {
        if (!Gate::allows('aggiornare')) {
            abort(403);
        }

        $contattoAbilita->update($request->validated());
        
        return new ContattoAbilitaResource($contattoAbilita);
    }

    /**
     * Elimina un'abilità.
     *
     * @param  ContattoAbilita  $contattoAbilita
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContattoAbilita $contattoAbilita)
    {
        if (!Gate::allows('eliminare')) {
            abort(403);
        }

        $contattoAbilita->delete();
        
        return response()->noContent();
    }
}
