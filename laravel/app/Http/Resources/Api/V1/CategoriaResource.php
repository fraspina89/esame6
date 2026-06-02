<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\RoleAwareResource;

class CategoriaResource extends RoleAwareResource
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
            'idCategoria' => $this->idCategoria,
            'nome' => $this->nome,
            'descrizione' => $this->when($isUser || $isAdmin, $this->descrizione ?? null),
            'created_at' => $this->when($isAdmin, $this->created_at),
            'updated_at' => $this->when($isAdmin, $this->updated_at),
        ];
    }
}