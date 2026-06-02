<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSerieTvRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Per ora autorizza sempre
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'idCategoria' => 'sometimes|required|integer|exists:categorie,idCategoria',
            'nome' => 'sometimes|required|string|max:255',
            'descrizione' => 'sometimes|nullable|string|max:255',
            'totaleStagioni' => 'sometimes|nullable|integer|min:1|max:255',
            'numeroEpisodio' => 'sometimes|nullable|integer|min:1|max:255',
            'regista' => 'sometimes|nullable|string|max:45',
            'attori' => 'sometimes|nullable|string|max:45',
            'annoInizio' => 'sometimes|nullable|integer|min:1900|max:' . (date('Y') + 5),
            'annoFine' => 'sometimes|nullable|integer|min:1900|max:' . (date('Y') + 5) . '|gte:annoInizio',
            'locandina' => 'sometimes|nullable|image|max:5120',
            'carousel'  => 'sometimes|nullable|image|max:5120',
            'video'     => 'sometimes|nullable|mimes:mp4,webm,avi,mov|max:204800',
        ];
    }
}
