<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ContactListRequest extends FormRequest
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
            'name'  => ['nullable', 'string', 'max:255'],
            'cpf'   => ['nullable', 'string', 'max:14', 'min:11'],
            'page'  => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'between:1,50']
        ];
    }
}
