<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TipoRecapitoCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = TipoRecapitoResource::class;

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
                'telefoni' => $this->collection->filter(function($item) {
                    return $item->isTelefono();
                })->count(),
                'email' => $this->collection->filter(function($item) {
                    return $item->isEmail();
                })->count(),
                'cellulari' => $this->collection->filter(function($item) {
                    return $item->isCellulare();
                })->count(),
                'fax' => $this->collection->filter(function($item) {
                    return $item->isFax();
                })->count(),
            ]
        ];
    }
}