<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\RoleAwareResource;

class VistaProvinciaResource extends RoleAwareResource
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
            'nome' => $this->nome,
            'regione' => $this->when($isUser || $isAdmin, $this->regione),
            'sigla' => $this->sigla,
            'count_comuni' => $this->when($isUser || $isAdmin, $this->count_comuni),
            'area_geografica' => [
                'is_nord' => $this->isNord(),
                'is_centro' => $this->isCentro(),
                'is_sud' => $this->isSud(),
                'is_isole' => $this->isIsole(),
            ]
        ];
    }
}