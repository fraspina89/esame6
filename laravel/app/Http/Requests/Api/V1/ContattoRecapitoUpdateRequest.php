<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ContattoRecapitoUpdateRequest extends FormRequest
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
            'idTipoRecapito' => 'sometimes|integer|exists:tipiRecapito,idTipoRecapito',
            'valore' => 'sometimes|string|max:255|min:3',
            'descrizione' => 'nullable|string|max:255',
            'preferito' => 'boolean'
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
            'idTipoRecapito.integer' => 'Il tipo di recapito deve essere un numero.',
            'idTipoRecapito.exists' => 'Il tipo di recapito selezionato non esiste.',
            'valore.string' => 'Il valore deve essere una stringa.',
            'valore.max' => 'Il valore non può superare i 255 caratteri.',
            'valore.min' => 'Il valore deve avere almeno 3 caratteri.',
            'descrizione.string' => 'La descrizione deve essere una stringa.',
            'descrizione.max' => 'La descrizione non può superare i 255 caratteri.',
            'preferito.boolean' => 'Il campo preferito deve essere vero o falso.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('valore') && $this->has('idTipoRecapito')) {
                $valore = $this->valore;
                $idTipoRecapito = $this->idTipoRecapito;

                // Verifica che sia un'email valida se il tipo è email
                if ($this->isEmailType($idTipoRecapito) && !filter_var($valore, FILTER_VALIDATE_EMAIL)) {
                    $validator->errors()->add('valore', 'Il valore deve essere un indirizzo email valido.');
                }

                // Verifica che sia un numero di telefono se il tipo è telefono
                if ($this->isTelefonoType($idTipoRecapito) && !$this->isValidTelefono($valore)) {
                    $validator->errors()->add('valore', 'Il valore deve essere un numero di telefono valido.');
                }

                // Verifica che sia un fax valido se il tipo è fax
                if ($this->isFaxType($idTipoRecapito) && !$this->isValidTelefono($valore)) {
                    $validator->errors()->add('valore', 'Il valore deve essere un numero di fax valido.');
                }
            }
        });
    }

    /**
     * Controlla se il tipo è email
     */
    private function isEmailType($idTipoRecapito)
    {
        // TODO: Implementare logica per controllare il tipo dal database
        // Per ora assumiamo che idTipoRecapito 1 = Email
        return $idTipoRecapito == 1;
    }

    /**
     * Controlla se il tipo è telefono
     */
    private function isTelefonoType($idTipoRecapito)
    {
        // TODO: Implementare logica per controllare il tipo dal database
        // Per ora assumiamo che idTipoRecapito 2 = Telefono, 3 = Cellulare
        return in_array($idTipoRecapito, [2, 3]);
    }

    /**
     * Controlla se il tipo è fax
     */
    private function isFaxType($idTipoRecapito)
    {
        // TODO: Implementare logica per controllare il tipo dal database
        // Per ora assumiamo che idTipoRecapito 4 = Fax
        return $idTipoRecapito == 4;
    }

    /**
     * Valida un numero di telefono
     */
    private function isValidTelefono($numero)
    {
        // Rimuovi spazi e caratteri non numerici eccetto + per internazionali
        $numeroPulito = preg_replace('/[^\d\+]/', '', $numero);
        
        // Accetta numeri dai 7 ai 15 cifre (standard internazionale)
        return preg_match('/^(\+39)?[0-9]{7,15}$/', $numeroPulito);
    }
}