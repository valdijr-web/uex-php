<?php

namespace App\Http\Requests;

use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateContactRequest extends FormRequest
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
            'name'         => ['required', 'string', 'max:255'],
            'cpf'          => [
                'required',
                'string',
                'size:14',
                new Cpf(),
                Rule::unique('contacts')->ignore($this->route('contact'))->where('user_id', Auth::id())
            ],
            'phone'        => ['required', 'string', 'max:20'],
            'zip_code'     => ['required', 'string', 'max:9','regex:/^\d{5}-?\d{3}$/'],
            'address'      => ['required', 'string', 'max:255'],
            'number'       => ['required', 'string', 'max:10'],
            'neighborhood' => ['required', 'string', 'max:255'],
            'city'         => ['required', 'string', 'max:255'],
            'state'        => ['required', 'string', 'size:2'],
            'complement'   => ['nullable', 'string', 'max:255'],
        ];
    }
}
