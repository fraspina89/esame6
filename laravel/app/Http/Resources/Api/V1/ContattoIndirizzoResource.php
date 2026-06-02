<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\RoleAwareResource;

class ContattoIndirizzoResource extends RoleAwareResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $isAdmin = $this->isAdmin($request);
        $isUser = $this->isUser($request);

        return [
            'idIndirizzo' => $this->idIndirizzo,
            'idTipologiaIndirizzo' => $this->idTipologiaIndirizzo,
            'comune' => $this->comune,
            'indirizzo' => $this->when($isUser || $isAdmin, $this->indirizzo),
            'civico' => $this->when($isUser || $isAdmin, $this->civico),
            'cap' => $this->when($isUser || $isAdmin, $this->cap),
            'localita' => $this->when($isUser || $isAdmin, $this->localita),
            'indirizzoCompleto' => $this->when($isUser || $isAdmin, $this->indirizzo_completo),
            'contatto' => $this->when($isUser || $isAdmin, $this->whenLoaded('contatto')),
            'nazione' => $this->when($isUser || $isAdmin, $this->whenLoaded('nazione')),
            'tipologiaIndirizzo' => $this->when($isUser || $isAdmin, $this->whenLoaded('tipologiaIndirizzo')),
            'created_at' => $this->when($isAdmin, $this->created_at),
            'updated_at' => $this->when($isAdmin, $this->updated_at),
        ];
    }
}
