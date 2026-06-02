<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class ContattoUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Per ora permettiamo l'accesso, gestiremo l'autorizzazione nel controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Otteniamo l'ID del contatto dalla route
        $contattoId = $this->route('idContatto');
        
        return [
            'idContattoStato' => 'sometimes|nullable|integer',
            'nome' => 'sometimes|nullable|string|max:45',
            'cognome' => 'sometimes|required|string|max:45',
            'sesso' => 'sometimes|nullable|integer|between:0,255',
            'codiceFiscale' => 'sometimes|nullable|string|max:20',
            'partitaIva' => 'sometimes|nullable|string|max:20',
            'cittadinanza' => 'sometimes|nullable|string|max:45',
            'idNazioneNascita' => 'sometimes|nullable|integer',
            'cittaNascita' => 'sometimes|nullable|string|max:45',
            'provinciaNascita' => 'sometimes|nullable|string|max:45',
            'dataNascita' => 'sometimes|nullable|date',
            'archiviato' => 'sometimes|nullable|integer|between:0,255',
            'updated_by' => 'sometimes|required|integer',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages()
    {
        return [
            'nome.required' => 'Il nome è obbligatorio',
            'cognome.required' => 'Il cognome è obbligatorio',
            'codice_fiscale.size' => 'Il codice fiscale deve essere di 16 caratteri',
            'codice_fiscale.unique' => 'Questo codice fiscale è già registrato',
            'email.unique' => 'Questa email è già registrata',
            'password.min' => 'La password deve essere di almeno 8 caratteri',
            'password.confirmed' => 'La conferma password non corrisponde',
        ];
    }
}
