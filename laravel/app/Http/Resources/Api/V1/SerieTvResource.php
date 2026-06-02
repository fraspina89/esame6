<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\EpisodioResource;

class SerieTvResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $roles = $request->attributes->get('contattiRuoli', []);
        $isAdmin = in_array('Amministratore', $roles);
        $isUser = in_array('Utente', $roles);

        return [
            'idSerie' => $this->idSerie,
            'nome' => $this->nome,
            'annoInizio' => $this->annoInizio,
            'annoFine' => $this->annoFine,

            // campi visibili a tutti (come FilmResource)
            'locandina'     => $this->urlMedia($this->locandina, 'files/locandina'),
            'carousel'      => $this->urlMedia($this->carousel, 'files/carousel'),
            'video'         => $this->urlMedia($this->video, 'files/video'),
            'descrizione'   => $this->descrizione,
            'totaleStagioni'=> $this->totaleStagioni,

            // campi visibili ad utente e admin
            'regista' => $this->when($isUser || $isAdmin, $this->regista),
            'attori' => $this->when($isUser || $isAdmin, $this->attori),

            'is_in_corso' => $this->when($isUser || $isAdmin, $this->isInCorso()),

            'categoria' => $this->whenLoaded('categoria', function () use ($isUser, $isAdmin) {
                $base = ['id' => $this->categoria->idCategoria, 'nome' => $this->categoria->nome];
                if ($isUser || $isAdmin) {
                    $base['descrizione'] = $this->categoria->descrizione;
                }
                return $base;
            }),

            'episodi_count' => $this->whenLoaded('episodi', function () use ($isUser, $isAdmin) {
                return ($isUser || $isAdmin) ? $this->episodi->count() : null;
            }),

            'episodi' => $this->whenLoaded('episodi', function () use ($isUser, $isAdmin) {
                if (!$isUser && !$isAdmin) return null;
                return EpisodioResource::collection($this->episodi);
            }),

            'created_at' => $this->when($isAdmin, $this->created_at?->toISOString()),
            'updated_at' => $this->when($isAdmin, $this->updated_at?->toISOString()),
        ];
    }

    /**
     * Costruisce l'URL completo per un file media.
     * Usa basename() per estrarre solo il nome file, gestendo sia i dati
     * seeder ('assets/img/...') sia i file caricati ('42.jpg').
     */
    private function urlMedia(?string $valore, string $cartella): ?string
    {
        if (empty($valore)) return null;
        return url($cartella . '/' . basename($valore));
    }
}