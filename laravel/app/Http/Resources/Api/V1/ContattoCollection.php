<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ContattoCollection extends ResourceCollection
{
    /**
     * Collezione di risorse Contatto
     *
     *  restituisce l'array `data` con la
     * collection dei contatti. Quando la collection è paginata,
     * Laravel aggiunge automaticamente i metadati di paginazione
     * (links, meta) nella risposta JSON, quindi non li ricostruiamo
     * manualmente qui per evitare duplicazioni.
     */
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Ritorna solo la collection: Laravel aggiunge automaticamente
        // i metadati di paginazione quando la risorsa è paginata.
        return [
            'data' => $this->collection,
        ];
    }
}
