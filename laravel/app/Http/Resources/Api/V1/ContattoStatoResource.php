<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\RoleAwareResource;

class ContattoStatoResource extends RoleAwareResource
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
            'idContattoStato' => $this->idContattoStato,
            'nome' => $this->nome,
            'created_at' => $this->when($isAdmin, $this->created_at),
            'updated_at' => $this->when($isAdmin, $this->updated_at),

            // Informazioni aggiuntive quando richieste (solo admin)
            $this->mergeWhen($request->include_stats && $isAdmin, [
                'contatti_count' => $this->whenLoaded('contatti', function() {
                    return $this->contatti->count();
                })
            ])
        ];
    }
}
