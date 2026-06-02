<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ContattoRecapitoCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = ContattoRecapitoResource::class;

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
                'preferiti' => $this->collection->where('preferito', true)->count(),
                'email' => $this->collection->filter(function($item) {
                    return $item->isEmail();
                })->count(),
                'telefoni' => $this->collection->filter(function($item) {
                    return $item->isTelefono();
                })->count(),
            ]
        ];
    }
}