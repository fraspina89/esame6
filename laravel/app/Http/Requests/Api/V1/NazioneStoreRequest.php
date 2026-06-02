<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class NazioneStoreRequest extends FormRequest
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
            'nome' => 'required|string|max:45',
            'continente' => 'required|string|max:45|in:Europa,Asia,Nord America,Sud America,Africa,Oceania,Antartide',
            'iso' => 'required|string|size:2|unique:nazioni,iso',
            'iso3' => 'required|string|size:3|unique:nazioni,iso3',
            'prefissoTelefonico' => 'required|string|max:45',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'nome.required' => 'Il nome della nazione è obbligatorio.',
            'nome.max' => 'Il nome non può superare i 45 caratteri.',
            'continente.required' => 'Il continente è obbligatorio.',
            'continente.in' => 'Il continente deve essere uno tra: Europa, Asia, Nord America, Sud America, Africa, Oceania, Antartide.',
            'iso.required' => 'Il codice ISO a 2 lettere è obbligatorio.',
            'iso.size' => 'Il codice ISO deve essere esattamente di 2 caratteri.',
            'iso.unique' => 'Il codice ISO deve essere univoco.',
            'iso3.required' => 'Il codice ISO3 a 3 lettere è obbligatorio.',
            'iso3.size' => 'Il codice ISO3 deve essere esattamente di 3 caratteri.',
            'iso3.unique' => 'Il codice ISO3 deve essere univoco.',
            'prefissoTelefonico.required' => 'Il prefisso telefonico è obbligatorio.',
            'prefissoTelefonico.max' => 'Il prefisso telefonico non può superare i 45 caratteri.',
        ];
    }
}
