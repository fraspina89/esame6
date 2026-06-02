<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Episodio;
use App\Models\SerieTv;
use App\Http\Resources\Api\V1\EpisodioResource;
use App\Http\Resources\Api\V1\EpisodioCollection;
use App\Http\Requests\Api\V1\StoreEpisodioRequest;
use App\Http\Requests\Api\V1\UpdateEpisodioRequest;

/**
 * Controller per gli episodi delle serie TV
 *
 * Fornisce azioni per elencare, mostrare, creare, aggiornare e rimuovere
 * episodi associati alle serie TV.
 */
class EpisodioController extends Controller
{
    /**
     * Restituisce l'elenco degli episodi di una serie.
     *
     * @param  int  $idSerieTV
     * @return \App\Http\Resources\Api\V1\EpisodioCollection
     */
    public function index($idSerieTV)
    {
        // Verifica che la serie esista
        $serieTv = SerieTv::findOrFail($idSerieTV);
        
        $episodi = Episodio::with('serieTv')
            ->perSerie($idSerieTV)
            ->ordinatoPerEpisodio()
            ->get();

        return new EpisodioCollection($episodi);
    }

    /**
     * Crea un nuovo episodio.
     *
     * @param  \App\Http\Requests\Api\V1\StoreEpisodioRequest  $request
     * @param  int  $idSerieTV
     * @return \App\Http\Resources\Api\V1\EpisodioResource
     */
    public function store(StoreEpisodioRequest $request, $idSerieTV)
    {
        // Verifica che la serie esista
        $serieTv = SerieTv::findOrFail($idSerieTV);
        
        $episodio = Episodio::create([
            'idSerie' => $idSerieTV,
            ...$request->validated()
        ]);
        $episodio->load('serieTv');

        return new EpisodioResource($episodio);
    }

    /**
     * Restituisce un episodio specifico.
     *
     * @param  int  $idSerieTV
     * @param  int  $idEpisodio
     * @return \App\Http\Resources\Api\V1\EpisodioResource
     */
    public function show($idSerieTV, $idEpisodio)
    {
        // Verifica che la serie esista
        $serieTv = SerieTv::findOrFail($idSerieTV);
        
        $episodio = Episodio::with('serieTv')
            ->where('idSerie', $idSerieTV)
            ->findOrFail($idEpisodio);

        return new EpisodioResource($episodio);
    }

    /**
     * Aggiorna un episodio esistente.
     *
     * @param  \App\Http\Requests\Api\V1\UpdateEpisodioRequest  $request
     * @param  int  $idSerieTV
     * @param  int  $idEpisodio
     * @return \App\Http\Resources\Api\V1\EpisodioResource
     */
    public function update(UpdateEpisodioRequest $request, $idSerieTV, $idEpisodio)
    {
        // Verifica che la serie esista
        $serieTv = SerieTv::findOrFail($idSerieTV);
        
        $episodio = Episodio::where('idSerie', $idSerieTV)
            ->findOrFail($idEpisodio);
            
        $episodio->update($request->validated());
        $episodio->load('serieTv');

        return new EpisodioResource($episodio);
    }

    /**
     * Elimina un episodio.
     *
     * @param  int  $idSerieTV
     * @param  int  $idEpisodio
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($idSerieTV, $idEpisodio)
    {
        // Verifica che la serie esista
        $serieTv = SerieTv::findOrFail($idSerieTV);
        
        $episodio = Episodio::where('idSerie', $idSerieTV)
            ->findOrFail($idEpisodio);
            
        $episodio->delete();

        return response()->json(['message' => 'Episodio eliminato con successo'], 200);
    }
}