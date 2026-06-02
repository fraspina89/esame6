<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\NazioneStoreRequest;
use App\Http\Requests\Api\V1\NazioneUpdateRequest;
use App\Http\Resources\Api\V1\NazioneResource;
use App\Http\Resources\Api\V1\NazioneCollection;
use App\Models\Nazione;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Controller per le nazioni
 *
 * Fornisce operazioni CRUD leggere per le nazioni usate nell'app.
 */
class NazioneController extends Controller
{
    /**
     * Restituisce l'elenco delle nazioni.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // elenco pubblico delle nazioni (accesso in sola lettura aperto)
        $query = Nazione::query();
        
        // Filtro per continente
        if ($request->has('continente')) {
            $query->byContinente($request->input('continente'));
        }
        
        // Filtro per ISO
        if ($request->has('iso')) {
            $query->byIso($request->input('iso'));
        }
        
        // Ordinamento
        $query->orderBy('nome', 'asc');
        
        $nazioni = $query->get();
        
        return new NazioneCollection($nazioni);
    }

    /**
     * Crea una nuova nazione.
     *
     * @param  NazioneStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NazioneStoreRequest $request)
    {
        if (!Gate::allows('creare')) {
            abort(403);
        }

        $nazione = Nazione::create($request->validated());
        
        return new NazioneResource($nazione);
    }

    /**
     * Restituisce una nazione specifica.
     *
     * @param  Nazione  $nazione
     * @return \Illuminate\Http\Response
     */
    public function show(Nazione $nazione)
    {
        // restituisce la risorsa pubblica di una nazione
        return new NazioneResource($nazione);
    }

    /**
     * Aggiorna una nazione esistente.
     *
     * @param  NazioneUpdateRequest  $request
     * @param  Nazione  $nazione
     * @return \Illuminate\Http\Response
     */
    public function update(NazioneUpdateRequest $request, Nazione $nazione)
    {
        if (!Gate::allows('aggiornare')) {
            abort(403);
        }

        $nazione->update($request->validated());
        
        return new NazioneResource($nazione);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Nazione  $nazione
     * @return \Illuminate\Http\Response
     */
    public function destroy(Nazione $nazione)
    {
        if (!Gate::allows('eliminare')) {
            abort(403);
        }

        $nazione->delete();
        
        return response()->noContent();
    }
}
