<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class FilmResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $roles = $request->attributes->get('contattiRuoli', []);
        $isAdmin = in_array('Amministratore', $roles);
        $isUser = in_array('Utente', $roles);

        return [
            // campi visibili a tutti (ospite compreso)
            'idFilm'      => $this->idFilm,
            'idCategoria' => $this->idCategoria,
            'titolo'      => $this->titolo,
            'anno'        => $this->anno,
            'locandina'   => $this->urlMedia($this->locandina, 'files/locandina'),
            'carousel'    => $this->urlMedia($this->carousel, 'files/carousel'),
            'video'       => $this->urlMedia($this->video, 'files/video'),
            'descrizione' => $this->descrizione,

            // campi visibili ad utente e admin
            'durata'           => $this->when($isUser || $isAdmin, $this->durata),
            'durata_formattata'=> $this->when($isUser || $isAdmin, $this->durata_formattata),
            'regista'          => $this->when($isUser || $isAdmin, $this->regista),
            'attori'           => $this->when($isUser || $isAdmin, $this->attori),

            // categoria sempre inclusa (nome per la UI)
            'categoria' => $this->whenLoaded('categoria', function () {
                return [
                    'idCategoria' => $this->categoria->idCategoria,
                    'nome'        => $this->categoria->nome,
                ];
            }),

            // timestamps solo per admin
            'created_at' => $this->when($isAdmin, $this->created_at?->format('Y-m-d H:i:s')),
            'updated_at' => $this->when($isAdmin, $this->updated_at?->format('Y-m-d H:i:s')),
        ];
    }

    /**
     * Costruisce l'URL completo per un file media.
     * Usa basename() per estrarre solo il nome file, gestendo sia i dati
     * seeder ('assets/img/john-wick.jpg') sia i file caricati ('42.jpg').
     */
    private function urlMedia(?string $valore, string $cartella): ?string
    {
        if (empty($valore)) return null;
        return url($cartella . '/' . basename($valore));
    }
}
