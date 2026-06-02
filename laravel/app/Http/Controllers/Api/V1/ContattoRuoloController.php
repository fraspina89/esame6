<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContattoRuolo;
use App\Http\Requests\V1\ContattoRuoloStoreRequest;
use App\Http\Requests\V1\ContattoRuoloUpdateRequest;
use App\Http\Resources\Api\V1\ContattoRuoloResource;
use App\Http\Resources\Api\V1\ContattoRuoloCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Controller per i ruoli dei contatti
 *
 * Gestisce l'elenco dei ruoli disponibili e le operazioni amministrative
 * correlate alla gestione dei ruoli.
 */
class ContattoRuoloController extends Controller
{
    /**
     * Restituisce l'elenco dei ruoli.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        if (Gate::allows('leggere')) {
            $contattoRuolo = ContattoRuolo::all();
            return new ContattoRuoloCollection($contattoRuolo);
        } else {
            abort(403, 'PE_0001');
        }
    }

    /**
     * Non utilizzato (endpoint API).
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Non necessario per API
    }

    /**
     * Crea un nuovo ruolo.
     *
     * @param  \App\Http\Requests\V1\ContattoRuoloStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContattoRuoloStoreRequest $request)
    {
        if (Gate::allows('creare')) {
            $validatedData = $request->validated();
            $contattoRuolo = ContattoRuolo::create($validatedData);
            
            return response()->json([
                'message' => 'Ruolo creato con successo',
                'data' => new ContattoRuoloResource($contattoRuolo)
            ], 201);
        }

        abort(403, 'PE_0006');
    }

    /**
     * Restituisce un ruolo specifico.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::allows('leggere')) {
            $contattoRuolo = ContattoRuolo::findOrFail($id);
            return new ContattoRuoloResource($contattoRuolo);
        } else {
            abort(403, 'PE_0002');
        }
    }

    /**
     * Non utilizzato (endpoint API).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Non necessario per API
    }

    /**
     * update the specified resource in storage.
     * 
     * @param  \App\Http\Requests\V1\ContattoRuoloUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ContattoRuoloUpdateRequest $request, $id)
    {
        if (Gate::allows('aggiornare')) {
            $contattoRuolo = ContattoRuolo::findOrFail($id);
            $validatedData = $request->validated();
            
            $contattoRuolo->update($validatedData);
            
            return response()->json([
                'message' => 'Ruolo aggiornato con successo',
                'data' => new ContattoRuoloResource($contattoRuolo->fresh())
            ]);
        } else {
            abort(403, 'PE_0004');
        }
    }

    /**
     * Elimina un ruolo.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::allows('eliminare')) {
            $contattoRuolo = ContattoRuolo::findOrFail($id);
            $contattoRuolo->delete(); // SoftDelete
            
            return response()->json([
                'message' => 'Ruolo eliminato con successo'
            ], 200);
        } else {
            abort(403, 'PE_0005');
        }
    }
}
