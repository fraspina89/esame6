<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Risorsa API: ContattoResource
 *
 * Trasforma un `Contatto` in un array JSON controllando la visibilità dei
 * campi in base ai ruoli del chiamante (es. `Amministratore`, `Utente`).
 */
class ContattoResource extends JsonResource
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
            // campi minimi visibili a tutti (ospite)
            'idContatto' => $this->idContatto,
            'nome' => $this->nome,
            'cognome' => $this->cognome,
            'nomeCompleto' => $this->nome . ' ' . $this->cognome,

            // campi visibili a utente e admin
            'sesso' => $this->when($isUser || $isAdmin, $this->sesso),
            'dataNascita' => $this->when($isUser || $isAdmin, $this->dataNascita),
            'cittaNascita' => $this->when($isUser || $isAdmin, $this->cittaNascita),

            // campi sensibili solo admin
            'codiceFiscale' => $this->when($isAdmin, $this->codiceFiscale),
            'partitaIva' => $this->when($isAdmin, $this->partitaIva),
            'created_by' => $this->when($isAdmin, $this->created_by),
            'updated_by' => $this->when($isAdmin, $this->updated_by),
            'created_at' => $this->when($isAdmin, $this->created_at),
            'updated_at' => $this->when($isAdmin, $this->updated_at),
            'deleted_at' => $this->when($isAdmin, $this->deleted_at),

            // Relazioni: recapiti/indirizzi/crediti visibili a utente/admin se caricate
            'recapiti' => $this->when($isUser || $isAdmin, $this->whenLoaded('recapiti')),
            'indirizzi' => $this->when($isUser || $isAdmin, $this->whenLoaded('indirizzi')),
            'crediti' => $this->when($isUser || $isAdmin, $this->whenLoaded('crediti')),

            // ruoli visibili solo ad admin
            'ruoli' => $this->when($isAdmin, $this->whenLoaded('ruoli')),
        ];
    }
}
