<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class VistaProvinciaCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = VistaProvinciaResource::class;

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
                'regioni' => $this->collection->pluck('regione')->unique()->values(),
                'totale_comuni' => $this->collection->sum('count_comuni'),
                'statistiche_geografiche' => [
                    'nord' => $this->collection->filter(function($item) {
                        return $item->isNord();
                    })->count(),
                    'centro' => $this->collection->filter(function($item) {
                        return $item->isCentro();
                    })->count(),
                    'sud' => $this->collection->filter(function($item) {
                        return $item->isSud();
                    })->count(),
                    'isole' => $this->collection->filter(function($item) {
                        return $item->isIsole();
                    })->count(),
                ]
            ]
        ];
    }
}