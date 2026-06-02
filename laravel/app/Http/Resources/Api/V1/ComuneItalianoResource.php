<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\RoleAwareResource;

class ComuneItalianoResource extends RoleAwareResource
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
            'idComune' => $this->idComune,
            'comune' => $this->comune,
            'provincia' => $this->provincia,
            'regione' => $this->when($isUser || $isAdmin, $this->regione),
            'zona' => $this->when($isUser || $isAdmin, $this->zona),
            'sigla_provincia' => $this->sigla_provincia,
            'cap' => $this->cap,
            'codice_catastale' => $this->codice_istat,
            'abitanti' => $this->when($isUser || $isAdmin, $this->abitanti),
            'superficie' => $this->when($isUser || $isAdmin, $this->superficie),
            'cap_iniziale' => $this->cap_iniziale,
            'cap_finale' => $this->cap_finale,
            'codice_istat' => $this->when($isAdmin, $this->codice_istat),
            'created_at' => $this->when($isAdmin, $this->created_at?->toISOString()),
            'updated_at' => $this->when($isAdmin, $this->updated_at?->toISOString()),
        ];
    }
}