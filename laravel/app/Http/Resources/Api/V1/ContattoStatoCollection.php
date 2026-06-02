<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ContattoStatoCollection extends ResourceCollection
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
            'data' => ContattoStatoResource::collection($this->collection),
            'meta' => [
                'total' => $this->collection->count(),
                'stati_disponibili' => $this->collection->count()
            ]
        ];
    }
}
