<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEpisodioRequest extends FormRequest
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
            'titolo' => 'sometimes|required|string|max:255',
            'descrizione' => 'sometimes|nullable|string|max:45',
            'numeroStagione' => 'sometimes|nullable|integer|min:1|max:127',
            'numeroEpisodio' => 'sometimes|nullable|integer|min:1|max:127',
            'durata' => 'sometimes|nullable|integer|min:1|max:127',
            'anno' => 'sometimes|nullable|integer|min:1900|max:' . (date('Y') + 5),
            'idImmagine' => 'sometimes|nullable|integer|min:1',
            'idFilmato' => 'sometimes|nullable|integer|min:1',
        ];
    }
}
