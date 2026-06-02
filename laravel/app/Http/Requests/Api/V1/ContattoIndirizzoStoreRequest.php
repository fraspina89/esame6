<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ContattoIndirizzoStoreRequest extends FormRequest
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
            'idTipologiaIndirizzo' => 'required|integer|exists:tipiindirizzi,idTipologiaIndirizzo',
            'idNazione' => 'required|integer|exists:nazioni,idNazione',
            'indirizzo' => 'required|string|max:255',
            'civico' => 'nullable|string|max:15',
            'cap' => 'nullable|string|max:15',
            'comune' => 'required|string|max:255',
            'localita' => 'nullable|string|max:255',
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
            'idTipologiaIndirizzo.required' => 'La tipologia di indirizzo è obbligatoria.',
            'idTipologiaIndirizzo.exists' => 'La tipologia di indirizzo selezionata non esiste.',
            'idNazione.required' => 'La nazione è obbligatoria.',
            'idNazione.exists' => 'La nazione selezionata non esiste.',
            'indirizzo.required' => 'L\'indirizzo è obbligatorio.',
            'indirizzo.max' => 'L\'indirizzo non può superare i 255 caratteri.',
            'civico.max' => 'Il numero civico non può superare i 15 caratteri.',
            'cap.max' => 'Il CAP non può superare i 15 caratteri.',
            'comune.required' => 'Il comune è obbligatorio.',
            'comune.max' => 'Il comune non può superare i 255 caratteri.',
            'localita.max' => 'La località non può superare i 255 caratteri.',
        ];
    }
}
