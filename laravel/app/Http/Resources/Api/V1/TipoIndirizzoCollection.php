<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TipoIndirizzoCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = TipoIndirizzoResource::class;

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
                'attivi' => $this->collection->where('attivo', true)->count(),
                'residenziali' => $this->collection->filter(function($item) {
                    return $item->isResidenza();
                })->count(),
                'aziendali' => $this->collection->filter(function($item) {
                    return $item->isAziendale();
                })->count(),
            ]
        ];
    }
}