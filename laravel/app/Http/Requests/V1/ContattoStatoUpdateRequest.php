<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class ContattoStatoUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Autorizzazione gestita nel Controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nome' => 'sometimes|required|string|max:255|unique:contattistati,nome,' . $this->route('contattoStato')->idContattoStato . ',idContattoStato'
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
            'nome.required' => 'Il nome dello stato è obbligatorio',
            'nome.string' => 'Il nome deve essere una stringa',
            'nome.max' => 'Il nome non può superare 255 caratteri',
            'nome.unique' => 'Esiste già uno stato con questo nome'
        ];
    }
}
