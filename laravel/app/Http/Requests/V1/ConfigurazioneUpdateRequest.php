<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfigurazioneUpdateRequest extends FormRequest
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
            'chiave' => [
                'required',
                'string',
                'max:100',
                Rule::unique('configurazioni', 'chiave')->ignore($this->route('id'), 'idConfigurazione')
            ],
            'valore' => 'required|string|max:500'
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
            'chiave.required' => 'La chiave della configurazione è obbligatoria',
            'chiave.string' => 'La chiave deve essere una stringa',
            'chiave.max' => 'La chiave può avere massimo 100 caratteri',
            'chiave.unique' => 'Esiste già una configurazione con questa chiave',
            'valore.required' => 'Il valore della configurazione è obbligatorio',
            'valore.string' => 'Il valore deve essere una stringa',
            'valore.max' => 'Il valore può avere massimo 500 caratteri'
        ];
    }
}
