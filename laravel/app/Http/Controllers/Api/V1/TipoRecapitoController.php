<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TipoRecapito;
use App\Http\Resources\Api\V1\TipoRecapitoResource;
use App\Http\Resources\Api\V1\TipoRecapitoCollection;

class TipoRecapitoController extends Controller
{
    /**
     * Restituisce l'elenco dei tipi di recapito.
     *
     * @return \App\Http\Resources\Api\V1\TipoRecapitoCollection
     */
    public function index()
    {
        $tipiRecapito = TipoRecapito::where('attivo', true)
            ->orderBy('nome')
            ->orderBy('descrizione')
            ->get();

        return new TipoRecapitoCollection($tipiRecapito);
    }

    /**
     * Restituisce un tipo di recapito specifico.
     *
     * @param  \App\Models\TipoRecapito  $tipoRecapito
     * @return \App\Http\Resources\Api\V1\TipoRecapitoResource
     */
    public function show(TipoRecapito $tipoRecapito)
    {
        return new TipoRecapitoResource($tipoRecapito);
    }
}