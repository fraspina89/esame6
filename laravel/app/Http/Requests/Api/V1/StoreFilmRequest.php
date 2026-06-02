<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreFilmRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'idCategoria' => 'required|integer|exists:categorie,idCategoria',
            'titolo' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'durata' => 'nullable|integer|min:1|max:999',
            'regista' => 'nullable|string|max:45',
            'attori' => 'nullable|string|max:45',
            'anno' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'idImmagine'  => 'nullable|integer',
            'idFilmato'   => 'nullable|integer',
            'locandina'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'carousel'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'video'       => 'nullable|mimes:mp4,mov,avi,webm|max:204800',
            'locandina_url' => 'nullable|string|max:255',
            'carousel_url'  => 'nullable|string|max:255',
            'video_url'     => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'idCategoria.required' => 'La categoria è obbligatoria',
            'idCategoria.exists' => 'La categoria selezionata non esiste',
            'titolo.required' => 'Il titolo è obbligatorio',
            'titolo.max' => 'Il titolo non può superare i 255 caratteri',
            'durata.integer' => 'La durata deve essere un numero intero',
            'durata.min' => 'La durata deve essere almeno 1 minuto',
            'durata.max' => 'La durata non può superare i 999 minuti',
            'regista.max' => 'Il nome del regista non può superare i 45 caratteri',
            'attori.max' => 'La lista degli attori non può superare i 45 caratteri',
            'anno.integer' => 'L\'anno deve essere un numero intero',
            'anno.min' => 'L\'anno non può essere inferiore al 1900',
            'anno.max'       => 'L\'anno non può essere superiore a ' . (date('Y') + 10),
            'locandina.image' => 'La locandina deve essere un\'immagine valida',
            'locandina.max'   => 'La locandina non può superare i 5 MB',
            'carousel.image'  => 'L\'immagine carousel deve essere un\'immagine valida',
            'carousel.max'    => 'L\'immagine carousel non può superare i 5 MB',
            'video.mimes'     => 'Il video deve essere in formato mp4, mov, avi o webm',
            'video.max'       => 'Il video non può superare i 200 MB',
        ];
    }
}
