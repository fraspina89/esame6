<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SerieTv;
use App\Http\Resources\Api\V1\SerieTvResource;
use App\Http\Resources\Api\V1\SerieTvCollection;
use App\Http\Requests\Api\V1\StoreSerieTvRequest;
use App\Http\Requests\Api\V1\UpdateSerieTvRequest;
use Illuminate\Http\Request;

/**
 * Controller per le Serie TV
 *
 * Gestisce le risorse relative alle serie televisive (lista, dettagli, ricerca).
 */
class SerieTvController extends Controller
{
    /**
     * Restituisce l'elenco delle serie TV.
     *
     * @return \App\Http\Resources\Api\V1\SerieTvCollection
     */
    public function index(Request $request)
    {
        $query = SerieTv::with('categoria');

        if ($request->has('categoria')) {
            $query->where('idCategoria', $request->input('categoria'));
        }

        $serieTV = $query->orderBy('annoInizio', 'desc')->orderBy('nome')->get();

        return new SerieTvCollection($serieTV);
    }

    /**
     * Crea una nuova serie TV.
     *
     * @param  \App\Http\Requests\Api\V1\StoreSerieTvRequest  $request
     * @return \App\Http\Resources\Api\V1\SerieTvResource
     */
    public function store(StoreSerieTvRequest $request)
    {
        $dati = $request->validated();

        // Rimuovi i campi file: li gestiamo dopo la creazione (ci serve l'ID)
        foreach (['locandina', 'carousel', 'video'] as $campo) {
            unset($dati[$campo]);
        }

        // Fallback URL se non è stato selezionato un file reale
        foreach (['locandina', 'carousel', 'video'] as $campo) {
            if (!$request->hasFile($campo) && $request->filled($campo . '_url')) {
                $dati[$campo] = $request->input($campo . '_url');
            }
        }

        $serieTv = SerieTv::create($dati);

        // Salva i file usando l'ID appena creato
        $aggiornamenti = $this->gestisciUploadFile($request, $serieTv->idSerie);
        if (!empty($aggiornamenti)) {
            $serieTv->update($aggiornamenti);
        }

        $serieTv->load('categoria');

        return new SerieTvResource($serieTv->fresh()->load('categoria'));
    }

    /**
     * Restituisce una serie TV specifica con categorie ed episodi.
     *
     * @param  int  $idSerieTV
     * @return \App\Http\Resources\Api\V1\SerieTvResource
     */
    public function show($idSerieTV)
    {
        $serieTv = SerieTv::with(['categoria', 'episodi'])
            ->findOrFail($idSerieTV);

        return new SerieTvResource($serieTv);
    }

    /**
     * Aggiorna una serie TV esistente.
     *
     * @param  \App\Http\Requests\Api\V1\UpdateSerieTvRequest  $request
     * @param  int  $idSerieTV
     * @return \App\Http\Resources\Api\V1\SerieTvResource
     */
    public function update(UpdateSerieTvRequest $request, $idSerieTV)
    {
        $serieTv = SerieTv::findOrFail($idSerieTV);
        $dati = $request->validated();
        foreach (['locandina', 'carousel', 'video'] as $campo) {
            unset($dati[$campo]);
        }

        // Fallback URL se non è stato selezionato un file reale
        foreach (['locandina', 'carousel', 'video'] as $campo) {
            if (!$request->hasFile($campo) && $request->filled($campo . '_url')) {
                $dati[$campo] = $request->input($campo . '_url');
            }
        }

        $aggiornamenti = $this->gestisciUploadFile($request, $serieTv->idSerie);
        $dati = array_merge($dati, $aggiornamenti);

        $serieTv->update($dati);

        return new SerieTvResource($serieTv->fresh()->load('categoria'));
    }

    /**
     * Elimina una serie TV.
     *
     * @param  int  $idSerieTV
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($idSerieTV)
    {
        $serieTv = SerieTv::findOrFail($idSerieTV);
        $serieTv->delete();

        return response()->json(['message' => 'Serie TV eliminata con successo'], 200);
    }

    /**
     * Gestisce l'upload di file (locandina, carousel, video).
     * Salva i file in public/files/{tipo}/{idSerie}.{ext}
     * Restituisce un array con i campi aggiornati da salvare nel DB.
     */
    private function gestisciUploadFile(\Illuminate\Http\Request $request, int $idSerie): array
    {
        // Salva in laravel_core/public/files/{tipo}/ come indicato dal professore
        $configurazione = [
            'locandina' => 'locandina',
            'carousel'  => 'carousel',
            'video'     => 'video',
        ];

        $aggiornamenti = [];

        foreach ($configurazione as $campo => $sottoCartella) {
            if ($request->hasFile($campo)) {
                $file     = $request->file($campo);
                $nomeFile = $file->getClientOriginalName();
                $percorso = public_path('files/' . $sottoCartella);
                if (!is_dir($percorso)) {
                    mkdir($percorso, 0755, true);
                }
                $file->move($percorso, $nomeFile);
                $aggiornamenti[$campo] = $nomeFile;
            }
        }

        return $aggiornamenti;
    }
}