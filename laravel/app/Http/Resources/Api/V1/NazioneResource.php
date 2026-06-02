<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\RoleAwareResource;

class NazioneResource extends RoleAwareResource
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
            'idNazione' => $this->idNazione,
            'nome' => $this->nome,
            'continente' => $this->when($isUser || $isAdmin, $this->continente),
            'iso' => $this->when($isUser || $isAdmin, $this->iso),
            'iso3' => $this->when($isAdmin, $this->iso3),
            'prefissoTelefonico' => $this->when($isUser || $isAdmin, $this->prefissoTelefonico),
            'formatoCompleto' => $this->when($isUser || $isAdmin, $this->formato_completo),
            'created_at' => $this->when($isAdmin, $this->created_at),
            'updated_at' => $this->when($isAdmin, $this->updated_at),
        ];
    }
}
