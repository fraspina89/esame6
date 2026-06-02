<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\RoleAwareResource;

class VistaTraduzioneResource extends RoleAwareResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $isAdmin = $this->isAdmin($request);
        $isUser = $this->isUser($request);

        return [
            'lingua_id' => $this->lingua_id,
            'lingua_codice' => $this->lingua_codice,
            'chiave' => $this->chiave,
            'valore' => $this->when($isUser || $isAdmin, $this->valore),
            'gruppo' => $this->gruppo,
        ];
    }
}