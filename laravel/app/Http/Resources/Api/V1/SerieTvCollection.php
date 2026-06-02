<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SerieTvCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = SerieTvResource::class;

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
                'in_corso' => $this->collection->filter(function($item) {
                    return $item->isInCorso();
                })->count(),
                'completate' => $this->collection->filter(function($item) {
                    return $item->isCompletata();
                })->count(),
                'anni_disponibili' => $this->collection->pluck('annoInizio')->unique()->sort()->values(),
                'categorie' => $this->collection->pluck('idCategoria')->unique()->values(),
                'registi' => $this->collection->pluck('regista')->filter()->unique()->values(),
            ]
        ];
    }
}