<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Configurazione;
use App\Http\Requests\V1\ConfigurazioneStoreRequest;
use App\Http\Requests\V1\ConfigurazioneUpdateRequest;
use App\Http\Resources\Api\V1\ConfigurazioneResource;
use App\Http\Resources\Api\V1\ConfigurazioneCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Controller per la gestione delle configurazioni applicative
 *
 * Fornisce accesso alle impostazioni memorizzate e alle operazioni
 * amministrative correlate.
 */
class ConfigurazioneController extends Controller
{
    /**
     * Restituisce l'elenco delle configurazioni.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) 
    {
        $query = Configurazione::query();

        // Filtri opzionali
        if ($request->has('chiave')) {
            $query->where('chiave', 'LIKE', '%' . $request->input('chiave') . '%');
        }

        if ($request->has('valore')) {
            $query->where('valore', 'LIKE', '%' . $request->input('valore') . '%');
        }

        $configurazioni = $query->get();
        return new ConfigurazioneCollection($configurazioni);
    }

    /**
     * Crea una nuova configurazione.
     *
     * @param  \App\Http\Requests\V1\ConfigurazioneStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ConfigurazioneStoreRequest $request)
    {
        if (Gate::allows('creare')) {
            $validatedData = $request->validated();
            $configurazione = Configurazione::create($validatedData);
            
            return response()->json([
                'message' => 'Configurazione creata con successo',
                'data' => new ConfigurazioneResource($configurazione)
            ], 201);
        }

        abort(403, 'PE_0006');
    }

    /**
     * Restituisce una configurazione specifica.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $configurazione = Configurazione::findOrFail($id);
        return new ConfigurazioneResource($configurazione);
    }

    /**
     * Aggiorna una configurazione esistente.
     *
     * @param  \App\Http\Requests\V1\ConfigurazioneUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ConfigurazioneUpdateRequest $request, $id)
    {
        if (Gate::allows('aggiornare')) {
            $configurazione = Configurazione::findOrFail($id);
            $validatedData = $request->validated();
            
            $configurazione->update($validatedData);
            
            return response()->json([
                'message' => 'Configurazione aggiornata con successo',
                'data' => new ConfigurazioneResource($configurazione->fresh())
            ]);
        } else {
            abort(403, 'PE_0004');
        }
    }

    /**
     * Elimina una configurazione.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::allows('eliminare')) {
            $configurazione = Configurazione::findOrFail($id);
            $configurazione->delete(); // Utilizzerà SoftDeletes se configurato nel model
            
            return response()->json([
                'message' => 'Configurazione eliminata con successo'
            ], 200);
        } else {
            abort(403, 'PE_0005');
        }
    }

    /**
     * Metodo utile per recuperare una configurazione per chiave
     * 
     * @param string $chiave
     * @return \Illuminate\Http\Response
     */
    public function getByKey($chiave)
    {
        if (Gate::allows('leggere')) {
            $configurazione = Configurazione::where('chiave', $chiave)->first();
            
            if (!$configurazione) {
                return response()->json(['message' => 'Configurazione non trovata'], 404);
            }
            
            return new ConfigurazioneResource($configurazione);
        } else {
            abort(403, 'PE_0002');
        }
    }
}
