<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CategoriaStoreRequest;
use App\Http\Requests\V1\CategoriaUpdateRequest;
use App\Http\Resources\Api\V1\CategoriaResource;
use App\Http\Resources\Api\V1\CategoriaCollection;
use Illuminate\Support\Facades\Gate;
use App\Models\Categoria;

/**
 * Controller per la gestione delle categorie
 *
 * Espone le azioni CRUD per le categorie utilizzate dai film e
 * dalle interfacce amministrative.
 */
class CategoriaController extends Controller
{
    /**
     * Restituisce l'elenco delle categorie.
     *
     * @return JsonResource
     */
    public function index() 
    {
        // Endpoint pubblico: restituisce solo le categorie con visualizzato=1
        $categorie = Categoria::where('visualizzato', 1)->orderBy('nome')->get();
        return new CategoriaCollection($categorie);
    }

    /**
     * Crea una nuova categoria.
     *
     * @param  \App\Http\Requests\V1\CategoriaStoreRequest  $request
     * @return JsonResource
     */
    public function store(CategoriaStoreRequest $request)
    {
        if (Gate::allows('creare')) {
            $data = $request->validated();
            $categoria = Categoria::create($data);
            return new CategoriaResource($categoria);
        } else {

        abort(403, 'PE_0006');
    }
    }

    /**
     * Restituisce una categoria specifica.
     * 
     * @param  \App\Models\Categoria  $categoria
     * @return JsonResource
     */
    public function show(Categoria $categoria)
    {
        if (Gate::allows('leggere')) {
            if (Gate::allows('Amministratore') || $categoria->visualizzato) {
                return new CategoriaResource($categoria);
            } else {
                abort(403, 'PE_0003');
            }
        } else {
            abort(403, 'PE_0002');
        }
    }

    /**
     * Aggiorna una categoria esistente.
     * 
     * @param  \App\Http\Requests\V1\CategoriaUpdateRequest  $request
     * @param  \App\Models\Categoria  $categoria
     * @return JsonResource
     */
    public function update(CategoriaUpdateRequest $request, Categoria $categoria)
    {
        if (Gate::allows('aggiornare')) {
            $data = $request->validated();
            $categoria->fill($data);
            $categoria->save();
            return new CategoriaResource($categoria);
        } else {
            abort(403, 'PE_0004');
        }
    }

    /**
     * Elimina una categoria.
     * 
     * @param  \App\Models\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function destroy(Categoria $categoria)
    {
        if (Gate::allows('eliminare')) {
            $categoria->deleteOrFail();
            return response()->noContent();
        } else {
            abort(403, 'PE_0005');
        }
    }
}
