<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\VistaProvincia;
use App\Http\Resources\Api\V1\VistaProvinciaResource;
use App\Http\Resources\Api\V1\VistaProvinciaCollection;

class VistaProvinciaController extends Controller
{
    /**
     * Restituisce l'elenco delle province.
     *
     * @return \App\Http\Resources\Api\V1\VistaProvinciaCollection
     */
    public function index()
    {
        $province = VistaProvincia::getAll();

        return new VistaProvinciaCollection($province);
    }

    /**
     * Restituisce una provincia specifica tramite sigla automobilistica.
     *
     * @param  string  $siglaAutomobilistica
     * @return \App\Http\Resources\Api\V1\VistaProvinciaResource
     */
    public function show($siglaAutomobilistica)
    {
        $provincia = VistaProvincia::findBySigla(strtoupper($siglaAutomobilistica));
        
        if (!$provincia) {
            return response()->json([
                'message' => 'Provincia non trovata con sigla: ' . $siglaAutomobilistica
            ], 404);
        }

        return new VistaProvinciaResource($provincia);
    }
}