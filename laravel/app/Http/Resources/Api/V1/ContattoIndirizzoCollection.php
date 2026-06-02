<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ContattoIndirizzoCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
                'nazioni' => $this->collection->pluck('nazione.nome')->filter()->unique()->values(),
                'comuni' => $this->collection->pluck('comune')->unique()->values(),
                'generated_at' => now()->toISOString(),
            ]
        ];
    }
}
