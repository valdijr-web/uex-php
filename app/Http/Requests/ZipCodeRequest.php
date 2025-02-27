<?php

namespace App\Http\Requests;

use App\Rules\ZipCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ZipCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'zip_code' => ['required', 'string', 'max:9','regex:/^\d{5}-?\d{3}$/']
        ];
    }

    public function messages(): array
    {
        return [
            'zip_code.required' => 'O campo CEP é obrigatório.',
            'zip_code.string'   => 'O CEP deve ser uma string.',
            'zip_code.regex'    => 'O CEP deve estar no formato 00000-000 ou 00000000.',
        ];
    }
}
