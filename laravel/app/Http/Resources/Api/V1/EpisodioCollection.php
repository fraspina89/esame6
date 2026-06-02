<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EpisodioCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = EpisodioResource::class;

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
                'stagioni' => $this->collection->pluck('numeroStagione')->unique()->filter()->sort()->values(),
                'durata_totale' => $this->collection->sum('durata'),
                'anni_disponibili' => $this->collection->pluck('anno')->unique()->filter()->sort()->values(),
                'episodi_pilota' => $this->collection->filter(function($item) {
                    return $item->isPilot();
                })->count(),
                'durata_media' => $this->collection->avg('durata'),
            ]
        ];
    }
}