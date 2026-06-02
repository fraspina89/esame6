<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class VistaTraduzioneCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = VistaTraduzioneResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $raggruppate = $this->collection->groupBy('gruppo');
        
        return [
            'data' => $this->collection,
            'meta' => [
                'count' => $this->collection->count(),
                'lingua_id' => $this->collection->first()->lingua_id ?? null,
                'lingua_codice' => $this->collection->first()->lingua_codice ?? null,
                'gruppi' => $raggruppate->keys(),
                'per_gruppo' => $raggruppate->map(function ($gruppo) {
                    return $gruppo->count();
                })
            ],
            'translations' => $raggruppate->map(function ($gruppo) {
                return $gruppo->mapWithKeys(function ($traduzione) {
                    return [$traduzione->chiave => $traduzione->valore];
                });
            })
        ];
    }
}