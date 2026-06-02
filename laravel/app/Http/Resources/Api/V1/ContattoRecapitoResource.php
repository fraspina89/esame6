<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\RoleAwareResource;

class ContattoRecapitoResource extends RoleAwareResource
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
            'idRecapito' => $this->idRecapito,
            'idTipoRecapito' => $this->idTipoRecapito,
            'descrizione' => $this->when($isUser || $isAdmin, $this->descrizione),
            'valore' => $this->when($isUser || $isAdmin, $this->valore),
            'valoreFormattato' => $this->when($isUser || $isAdmin, $this->valore_formattato),
            'preferito' => $this->when($isUser || $isAdmin, $this->preferito),
            'isEmail' => $this->when($isUser || $isAdmin, $this->isEmail()),
            'isTelefono' => $this->when($isUser || $isAdmin, $this->isTelefono()),
            'isCellulare' => $this->when($isUser || $isAdmin, $this->isCellulare()),
            'created_at' => $this->when($isAdmin, $this->created_at?->toISOString()),
            'updated_at' => $this->when($isAdmin, $this->updated_at?->toISOString()),
            'deleted_at' => $this->when($isAdmin, $this->deleted_at?->toISOString()),

            // Relazioni
            'contatto' => $this->whenLoaded('contatto', function() use ($isUser, $isAdmin) {
                return [
                    'idContatto' => $this->contatto->idContatto,
                    'nome' => $this->contatto->nome,
                    'cognome' => $this->contatto->cognome,
                    'nomeCompleto' => $this->contatto->nome . ' ' . $this->contatto->cognome,
                ];
            }),

            'tipoRecapito' => $this->whenLoaded('tipoRecapito', function() {
                return [
                    'idTipoRecapito' => $this->tipoRecapito->idTipoRecapito ?? null,
                    'nome' => $this->tipoRecapito->nome ?? 'Tipo non specificato',
                    'descrizione' => $this->tipoRecapito->descrizione ?? null,
                ];
            }),
        ];
    }
}