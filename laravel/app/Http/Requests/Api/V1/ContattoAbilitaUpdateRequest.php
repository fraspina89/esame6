<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContattoAbilitaUpdateRequest extends FormRequest
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
            'nome' => 'sometimes|required|string|max:255',
            'sku' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('contattiabilita', 'sku')->ignore($this->route('contattoAbilita')->idContattoAbilita, 'idContattoAbilita'),
            ],
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
            'nome.required' => 'Il nome è obbligatorio.',
            'nome.string' => 'Il nome deve essere una stringa.',
            'nome.max' => 'Il nome non può superare i 255 caratteri.',
            'sku.required' => 'Il codice SKU è obbligatorio.',
            'sku.string' => 'Il codice SKU deve essere una stringa.',
            'sku.max' => 'Il codice SKU non può superare i 255 caratteri.',
            'sku.unique' => 'Il codice SKU deve essere univoco.',
        ];
    }
}
