<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreFilmRequest;
use App\Http\Requests\Api\V1\UpdateFilmRequest;
use App\Http\Resources\Api\V1\FilmResource;
use App\Models\Film;
use Illuminate\Http\Request;

/**
 * Controller per la gestione dei film
 *
 * Espone gli endpoint per elencare, mostrare, creare, aggiornare e
 * cancellare film; applica inoltre filtri e ordinamenti per le liste.
 */
class FilmController extends Controller
{
    /**
     * Restituisce l'elenco dei film.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Film::with('categoria')->where('visualizzato', 1);
        
        // Filtri opzionali
        if ($request->has('categoria')) {
            $query->where('idCategoria', $request->input('categoria'));
        }
        
        if ($request->has('anno')) {
            $query->where('anno', $request->input('anno'));
        }
        
        if ($request->has('regista')) {
            $query->where('regista', 'like', '%' . $request->input('regista') . '%');
        }
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('titolo', 'like', '%' . $search . '%')
                  ->orWhere('descrizione', 'like', '%' . $search . '%')
                  ->orWhere('attori', 'like', '%' . $search . '%');
            });
        }
        
        // Ordinamento
        $sortBy = $request->get('sort', 'titolo');
        $sortOrder = $request->get('order', 'asc');
        
        if (in_array($sortBy, ['titolo', 'anno', 'durata', 'regista'])) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $films = $query->get();

        return FilmResource::collection($films);
    }

    /**
     * Crea un nuovo film.
     *
     * @param  \App\Http\Requests\Api\V1\StoreFilmRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFilmRequest $request)
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

        $film = Film::create($dati);

        // Salva i file usando l'ID appena creato
        $aggiornamenti = $this->gestisciUploadFile($request, $film->idFilm);
        if (!empty($aggiornamenti)) {
            $film->update($aggiornamenti);
        }

        return (new FilmResource($film->fresh()->load('categoria')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Restituisce un film specifico.
     *
     * @param  \App\Models\Film  $film
     * @return \Illuminate\Http\Response
     */
    public function show(Film $film)
    {
        return new FilmResource($film->load('categoria'));
    }

    /**
     * Aggiorna un film esistente.
     *
     * @param  \App\Http\Requests\Api\V1\UpdateFilmRequest  $request
     * @param  \App\Models\Film  $film
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFilmRequest $request, Film $film)
    {
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

        $aggiornamenti = $this->gestisciUploadFile($request, $film->idFilm);
        $dati = array_merge($dati, $aggiornamenti);

        $film->update($dati);

        return new FilmResource($film->fresh()->load('categoria'));
    }

    /**
     * Elimina un film.
     *
     * @param  \App\Models\Film  $film
     * @return \Illuminate\Http\Response
     */
    public function destroy(Film $film)
    {
        $film->delete();

        return response()->json([
            'message' => 'Film eliminato con successo'
        ], 200);
    }

    /**
     * Gestisce l'upload di file (locandina, carousel, video).
     * Salva i file in public/files/{tipo}/{idFilm}.{ext}
     * Restituisce un array con i campi aggiornati da salvare nel DB.
     */
    private function gestisciUploadFile(\Illuminate\Http\Request $request, int $idFilm): array
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
