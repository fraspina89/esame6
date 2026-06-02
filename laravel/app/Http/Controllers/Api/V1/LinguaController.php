<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Lingua;
use App\Http\Resources\Api\V1\LinguaResource;
use App\Http\Resources\Api\V1\LinguaCollection;

/**
 * Controller per le lingue
 *
 * Gestisce l'elenco e i dettagli delle lingue disponibili nell'app.
 */
class LinguaController extends Controller
{
    /**
     * Restituisce l'elenco delle lingue.
     *
     * @return \App\Http\Resources\Api\V1\LinguaCollection
     */
    public function index()
    {
        $lingue = Lingua::attivo()
            ->ordinato()
            ->get();

        return new LinguaCollection($lingue);
    }

    /**
     * Restituisce una lingua specifica.
     *
     * @param  \App\Models\Lingua  $lingua
     * @return \App\Http\Resources\Api\V1\LinguaResource
     */
    public function show(Lingua $lingua)
    {
        return new LinguaResource($lingua);
    }
}