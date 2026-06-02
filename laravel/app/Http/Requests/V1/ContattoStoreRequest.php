<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class ContattoStoreRequest extends FormRequest
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
            'idContattoStato' => 'nullable|integer',
            'nome' => 'nullable|string|max:45',
            'cognome' => 'required|string|max:45',
            'sesso' => 'nullable|integer|between:0,255',
            'codiceFiscale' => 'nullable|string|max:20',
            'partitaIva' => 'nullable|string|max:20',
            'cittadinanza' => 'nullable|string|max:45',
            'idNazioneNascita' => 'nullable|integer',
            'cittaNascita' => 'nullable|string|max:45',
            'provinciaNascita' => 'nullable|string|max:45',
            'dataNascita' => 'nullable|date',
            'archiviato' => 'nullable|integer|between:0,255',
            'created_by' => 'nullable|integer',
            'updated_by' => 'nullable|integer',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages()
    {
        return [
            'cognome.required' => 'Il cognome è obbligatorio',
            'cognome.max' => 'Il cognome non può superare 45 caratteri',
            'nome.max' => 'Il nome non può superare 45 caratteri',
            'codiceFiscale.max' => 'Il codice fiscale non può superare 20 caratteri',
            'created_by.integer' => 'Il campo created_by deve essere un intero',
            'updated_by.integer' => 'Il campo updated_by deve essere un intero',
        ];
    }
}
