<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\RoleAwareResource;

class ContattoCreditoResource extends RoleAwareResource
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
            'idCredito' => $this->idCredito,
            'idContatto' => $this->idContatto,
            'saldo' => $this->when($isUser || $isAdmin, $this->saldo),
            'saldoFormattato' => $this->when($isUser || $isAdmin, $this->saldo_formattato),
            'limite' => $this->when($isUser || $isAdmin, $this->limite),
            'limiteFormattato' => $this->when($isUser || $isAdmin, $this->limite_formattato),
            'disponibilitaTotale' => $this->when($isUser || $isAdmin, $this->disponibilita_totale),
            'percentualeUtilizzoLimite' => $this->when($isUser || $isAdmin, $this->percentuale_utilizzo_limite),
            'attivo' => $this->when($isUser || $isAdmin, $this->attivo),
            'isInDebito' => $this->when($isUser || $isAdmin, $this->isInDebito()),
            'hasSaldoPositivo' => $this->when($isUser || $isAdmin, $this->hasSaldoPositivo()),
            'puoSpendere' => $this->when($isUser || $isAdmin, function($importo = 0) {
                return $this->puoSpendere($importo);
            }),
            'created_at' => $this->when($isAdmin, $this->created_at?->toISOString()),
            'updated_at' => $this->when($isAdmin, $this->updated_at?->toISOString()),
            'deleted_at' => $this->when($isAdmin, $this->deleted_at?->toISOString()),

            // Relazioni
            'contatto' => $this->when($isUser || $isAdmin && $this->relationLoaded('contatto'), $this->whenLoaded('contatto', function() {
                return [
                    'idContatto' => $this->contatto->idContatto,
                    'nome' => $this->contatto->nome,
                    'cognome' => $this->contatto->cognome,
                    'nomeCompleto' => $this->contatto->nome . ' ' . $this->contatto->cognome,
                ];
            })),
        ];
    }
}