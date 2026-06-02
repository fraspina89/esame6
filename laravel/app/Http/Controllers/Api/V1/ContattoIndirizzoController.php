<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ContattoIndirizzoStoreRequest;
use App\Http\Requests\Api\V1\ContattoIndirizzoUpdateRequest;
use App\Http\Resources\Api\V1\ContattoIndirizzoResource;
use App\Http\Resources\Api\V1\ContattoIndirizzoCollection;
use App\Models\Contatto;
use App\Models\ContattoIndirizzo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Controller per gli indirizzi dei contatti
 *
 * Gestisce gli indirizzi associati ai contatti (CRUD e liste).
 */
class ContattoIndirizzoController extends Controller
{
    /**
     * Restituisce l'elenco degli indirizzi di un contatto.
     *
     * @param Contatto $contatto
     * @return \Illuminate\Http\Response
     */
    public function index(Contatto $contatto)
    {
        if (!Gate::allows('leggere')) {
            abort(403);
        }

        $indirizzi = $contatto->indirizzi()
                             ->with(['nazione'])
                             ->orderBy('created_at', 'DESC')
                             ->get();
        
        return new ContattoIndirizzoCollection($indirizzi);
    }

    /**
     * Crea un nuovo indirizzo per un contatto.
     *
     * @param ContattoIndirizzoStoreRequest $request
     * @param Contatto $contatto
     * @return \Illuminate\Http\Response
     */
    public function store(ContattoIndirizzoStoreRequest $request, Contatto $contatto)
    {
        if (!Gate::allows('creare')) {
            abort(403);
        }

        $data = $request->validated();
        $data['idContatto'] = $contatto->idContatto;
        
        $indirizzo = ContattoIndirizzo::create($data);
        $indirizzo->load(['contatto', 'nazione']);
        
        return new ContattoIndirizzoResource($indirizzo);
    }

    /**
     * Restituisce un indirizzo specifico di un contatto.
     *
     * @param Contatto $contatto
     * @param ContattoIndirizzo $indirizzo
     * @return \Illuminate\Http\Response
     */
    public function show(Contatto $contatto, ContattoIndirizzo $indirizzo)
    {
        if (!Gate::allows('leggere')) {
            abort(403);
        }

        // Verifica che l'indirizzo appartenga al contatto
        if ($indirizzo->idContatto !== $contatto->idContatto) {
            abort(404);
        }

        $indirizzo->load(['contatto', 'nazione']);
        
        return new ContattoIndirizzoResource($indirizzo);
    }

    /**
     * Aggiorna un indirizzo esistente di un contatto.
     *
     * @param ContattoIndirizzoUpdateRequest $request
     * @param Contatto $contatto
     * @param ContattoIndirizzo $indirizzo
     * @return \Illuminate\Http\Response
     */
    public function update(ContattoIndirizzoUpdateRequest $request, Contatto $contatto, ContattoIndirizzo $indirizzo)
    {
        if (!Gate::allows('aggiornare')) {
            abort(403);
        }

        // Verifica che l'indirizzo appartenga al contatto
        if ($indirizzo->idContatto !== $contatto->idContatto) {
            abort(404);
        }

        $indirizzo->update($request->validated());
        $indirizzo->load(['contatto', 'nazione']);
        
        return new ContattoIndirizzoResource($indirizzo);
    }

    /**
     * Elimina un indirizzo di un contatto.
     *
     * @param Contatto $contatto
     * @param ContattoIndirizzo $indirizzo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contatto $contatto, ContattoIndirizzo $indirizzo)
    {
        if (!Gate::allows('eliminare')) {
            abort(403);
        }

        // Verifica che l'indirizzo appartenga al contatto
        if ($indirizzo->idContatto !== $contatto->idContatto) {
            abort(404);
        }

        $indirizzo->delete();
        
        return response()->noContent();
    }
}
