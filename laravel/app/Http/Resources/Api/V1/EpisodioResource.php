<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class EpisodioResource extends JsonResource
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
            'idEpisodio' => $this->idEpisodio,
            'titolo' => $this->titolo,

            // campi utente/admin
            'descrizione' => $this->when($isUser || $isAdmin, $this->descrizione),
            'numeroStagione' => $this->when($isUser || $isAdmin, $this->numeroStagione),
            'numeroEpisodio' => $this->when($isUser || $isAdmin, $this->numeroEpisodio),
            'durata' => $this->when($isUser || $isAdmin, $this->durata),
            'anno' => $this->when($isUser || $isAdmin, $this->anno),

            // admin only
            'idImmagine' => $this->when($isAdmin, $this->idImmagine),
            'idFilmato' => $this->when($isAdmin, $this->idFilmato),

            'codice_episodio' => $this->when($isUser || $isAdmin, $this->codice_episodio),
            'durata_formattata' => $this->when($isUser || $isAdmin, $this->durata_formattata),
            'titolo_completo' => $this->when($isUser || $isAdmin, $this->titolo_completo),
            'is_pilot' => $this->when($isUser || $isAdmin, $this->isPilot()),
            'is_finale' => $this->when($isUser || $isAdmin, $this->isFinale()),

            'serie_tv' => $this->whenLoaded('serieTv', function () use ($isUser, $isAdmin) {
                $base = ['idSerie' => $this->serieTv->idSerie, 'nome' => $this->serieTv->nome];
                if ($isUser || $isAdmin) {
                    $base['descrizione'] = $this->serieTv->descrizione;
                }
                return $base;
            }),

            'created_at' => $this->when($isAdmin, $this->created_at?->toISOString()),
            'updated_at' => $this->when($isAdmin, $this->updated_at?->toISOString()),
        ];
    }
}