<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\VistaTraduzioni;
use App\Http\Resources\Api\V1\VistaTraduzioneResource;
use App\Http\Resources\Api\V1\VistaTraduzioneCollection;

class VistaTraduzioneController extends Controller
{
    /**
     * Restituisce l'elenco delle traduzioni, opzionalmente filtrate per lingua.
     *
     * @param  int|null  $idLingua
     * @return \App\Http\Resources\Api\V1\VistaTraduzioneCollection
     */
    public function index(?int $idLingua = null)
    {
        $traduzioni = $idLingua !== null
            ? VistaTraduzioni::perLingua($idLingua)
            : collect();

        return new VistaTraduzioneCollection($traduzioni);
    }

    /**
     * Restituisce una traduzione specifica.
     *
     * @param  int  $idLingua
     * @param  string  $chiave
     * @return \App\Http\Resources\Api\V1\VistaTraduzioneResource|\Illuminate\Http\JsonResponse
     */
    public function show($idLingua, $chiave)
    {
        $traduzione = VistaTraduzioni::perLinguaEChiave($idLingua, $chiave);
        
        if (!$traduzione) {
            return response()->json([
                'message' => 'Traduzione non trovata per lingua ' . $idLingua . ' e chiave: ' . $chiave
            ], 404);
        }

        return new VistaTraduzioneResource($traduzione);
    }
}