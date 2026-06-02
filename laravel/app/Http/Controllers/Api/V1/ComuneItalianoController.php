<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ComuneItaliano;
use App\Http\Resources\Api\V1\ComuneItalianoResource;
use App\Http\Resources\Api\V1\ComuneItalianoCollection;

/**
 * Controller per la gestione dei comuni italiani
 *
 * Fornisce endpoint per elencare e mostrare i dettagli dei comuni.
 */
class ComuneItalianoController extends Controller
{
    /**
     * Restituisce l'elenco dei comuni italiani.
     *
     * @return \App\Http\Resources\Api\V1\ComuneItaliaCollection
     */
    public function index()
    {
        $comuni = ComuneItaliano::orderBy('regione')
            ->orderBy('provincia')
            ->orderBy('comune')
            ->get();

        return new ComuneItalianoCollection($comuni);
    }

    /**
     * Restituisce un comune specifico.
     *
     * @param  \App\Models\ComuneItaliano  $comuneItaliano
     * @return \App\Http\Resources\Api\V1\ComuneItalianoResource
     */
    public function show(ComuneItaliano $comuneItaliano)
    {
        return new ComuneItalianoResource($comuneItaliano);
    }
}