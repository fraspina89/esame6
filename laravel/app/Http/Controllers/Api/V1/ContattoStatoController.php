<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContattoStato;
use App\Http\Resources\Api\V1\ContattoStatoResource;
use App\Http\Resources\Api\V1\ContattoStatoCollection;
use App\Http\Requests\V1\ContattoStatoStoreRequest;
use App\Http\Requests\V1\ContattoStatoUpdateRequest;
use Illuminate\Http\Request;

/**
 * Controller per gli stati dei contatti
 *
 * Gestisce gli stati (es. attivo, sospeso) assegnabili ai contatti.
 */
class ContattoStatoController extends Controller
{
    /**
     * Restituisce l'elenco degli stati dei contatti.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Filtri opzionali
        $query = ContattoStato::query();
        
        // Filtro per nome (ricerca)
        if ($request->has('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }
        
        // Ordinamento (default per nome)
        $query->orderBy('nome', 'asc');
        
        $stati = $query->get();
        
        return new ContattoStatoCollection($stati);
    }

    /**
     * Crea un nuovo stato.
     *
     * @param  \App\Http\Requests\V1\ContattoStatoStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContattoStatoStoreRequest $request)
    {
        $stato = ContattoStato::create($request->validated());
        
        return new ContattoStatoResource($stato);
    }

    /**
     * Restituisce uno stato specifico.
     *
     * @param  \App\Models\ContattoStato  $contattoStato
     * @return \Illuminate\Http\Response
     */
    public function show(ContattoStato $contattoStato)
    {
        return new ContattoStatoResource($contattoStato);
    }

    /**
     * Aggiorna uno stato esistente.
     *
     * @param  \App\Http\Requests\V1\ContattoStatoUpdateRequest  $request
     * @param  \App\Models\ContattoStato  $contattoStato
     * @return \Illuminate\Http\Response
     */
    public function update(ContattoStatoUpdateRequest $request, ContattoStato $contattoStato)
    {
        $contattoStato->update($request->validated());
        
        return new ContattoStatoResource($contattoStato);
    }

    /**
     * Elimina uno stato.
     *
     * @param  \App\Models\ContattoStato  $contattoStato
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContattoStato $contattoStato)
    {
        // Controllo se ci sono contatti che usano questo stato
        if ($contattoStato->contatti()->count() > 0) {
            return response()->json([
                'message' => 'Impossibile eliminare: ci sono contatti associati a questo stato',
                'contatti_count' => $contattoStato->contatti()->count()
            ], 422);
        }

        $contattoStato->delete();
        
        return response()->json([
            'message' => 'Stato eliminato con successo'
        ]);
    }
}
