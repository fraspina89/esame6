<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class RoleAwareResource extends JsonResource
{
    protected function roles($request): array
    {
        return $request->attributes->get('contattiRuoli', []) ?: [];
    }

    protected function isAdmin($request): bool
    {
        return in_array('Amministratore', $this->roles($request));
    }

    protected function isUser($request): bool
    {
        return in_array('Utente', $this->roles($request));
    }

    protected function isGuest($request): bool
    {
        return !$this->isUser($request) && !$this->isAdmin($request);
    }
}
