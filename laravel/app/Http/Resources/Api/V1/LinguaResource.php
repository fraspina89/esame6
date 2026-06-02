<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\RoleAwareResource;

class LinguaResource extends RoleAwareResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $isAdmin = $this->isAdmin($request);
        $isUser = $this->isUser($request);

        return [
            'id' => $this->id,
            'codice' => $this->codice,
            'nome' => $this->nome,
            'nome_nativo' => $this->nome_nativo,
            'bandiera' => $this->bandiera,
            'predefinita' => $this->predefinita,
            'attivo' => $this->when($isUser || $isAdmin, $this->attivo),
            'ordinamento' => $this->when($isUser || $isAdmin, $this->ordinamento),
            'nome_completo' => $this->when($isUser || $isAdmin, $this->nome_completo),
            'codice_upper' => $this->when($isUser || $isAdmin, $this->codice_upper),
            'created_at' => $this->when($isAdmin, $this->created_at?->toISOString()),
            'updated_at' => $this->when($isAdmin, $this->updated_at?->toISOString()),
        ];
    }
}