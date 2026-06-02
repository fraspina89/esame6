<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreSerieTvRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Per ora autorizza sempre, poi implementeremo Gate
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'idCategoria' => 'required|integer|exists:categorie,idCategoria',
            'nome' => 'required|string|max:255',
            'descrizione' => 'nullable|string|max:255',
            'totaleStagioni' => 'nullable|integer|min:1|max:255',
            'numeroEpisodio' => 'nullable|integer|min:1|max:255',
            'regista' => 'nullable|string|max:45',
            'attori' => 'nullable|string|max:45',
            'annoInizio' => 'nullable|integer|min:1900|max:' . (date('Y') + 5),
            'annoFine' => 'nullable|integer|min:1900|max:' . (date('Y') + 5) . '|gte:annoInizio',
            'locandina' => 'nullable|image|max:5120',
            'carousel'  => 'nullable|image|max:5120',
            'video'     => 'nullable|mimes:mp4,webm,avi,mov|max:204800',
        ];
    }
}
