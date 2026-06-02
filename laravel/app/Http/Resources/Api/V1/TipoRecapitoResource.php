<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\RoleAwareResource;

class TipoRecapitoResource extends RoleAwareResource
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
            'descrizione' => $this->descrizione,
            'ordinamento' => $this->when($isUser || $isAdmin, $this->ordinamento),
            'attivo' => $this->when($isUser || $isAdmin, $this->attivo),
            'is_telefono' => $this->when($isUser || $isAdmin, $this->isTelefono()),
            'is_email' => $this->when($isUser || $isAdmin, $this->isEmail()),
            'is_fax' => $this->when($isUser || $isAdmin, $this->isFax()),
            'is_cellulare' => $this->when($isUser || $isAdmin, $this->isCellulare()),
            'created_at' => $this->when($isAdmin, $this->created_at?->toISOString()),
            'updated_at' => $this->when($isAdmin, $this->updated_at?->toISOString()),
        ];
    }
}