<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoIndirizzoResource extends JsonResource
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
            'id' => $this->idTipoIndirizzo,
            'nome' => $this->nome,
            'descrizione' => $this->when($isUser || $isAdmin, $this->descrizione),
            'attivo' => $this->when($isUser || $isAdmin, $this->attivo),
            'ordinamento' => $this->when($isUser || $isAdmin, $this->ordinamento),
            'created_at' => $this->when($isAdmin, $this->created_at?->toISOString()),
            'updated_at' => $this->when($isAdmin, $this->updated_at?->toISOString()),
            'deleted_at' => $this->when($isAdmin, $this->deleted_at?->toISOString()),
        ];
    }
}