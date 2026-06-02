<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoriaUpdateRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'nome' => [
                'required',
                'string',
                'max:45',
                Rule::unique('categorie', 'nome')->ignore($this->categoria)
            ]
        ];
    }

    /**
     * Custom error messages
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'nome.required' => 'Il nome della categoria è obbligatorio',
            'nome.string' => 'Il nome deve essere una stringa',
            'nome.max' => 'Il nome può avere massimo 45 caratteri',
            'nome.unique' => 'Esiste già una categoria con questo nome'
        ];
    }
}