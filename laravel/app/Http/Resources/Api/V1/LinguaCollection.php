<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LinguaCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = LinguaResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'count' => $this->collection->count(),
                'attive' => $this->collection->where('attivo', true)->count(),
                'predefinita' => $this->collection->where('predefinita', true)->first(),
                'codici_disponibili' => $this->collection->pluck('codice')->values(),
            ]
        ];
    }
}