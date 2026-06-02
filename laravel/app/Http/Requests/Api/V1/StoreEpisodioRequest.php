<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreEpisodioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'titolo' => 'required|string|max:255',
            'descrizione' => 'nullable|string|max:45',
            'numeroStagione' => 'nullable|integer|min:1|max:127',
            'numeroEpisodio' => 'nullable|integer|min:1|max:127',
            'durata' => 'nullable|integer|min:1|max:127', // minuti
            'anno' => 'nullable|integer|min:1900|max:' . (date('Y') + 5),
            'idImmagine' => 'nullable|integer|min:1',
            'idFilmato' => 'nullable|integer|min:1',
        ];
    }
}
