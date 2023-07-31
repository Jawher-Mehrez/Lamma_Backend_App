<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'unique:users,username', 'string'],
            'email' => ['required', Rule::unique('users', 'email')->ignore($this->segment(3)), 'email'],
            'phone_number' => ['required', 'unique:users,phone_number', 'string'],
            'password' => ['required', 'string'],
            // 'role' => ['required',  'in:admin,player'],


        ];
    }

    /**
     * @throws HttpResponseException
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'check the data entered',
            'data' => $validator->errors()
        ], 400));
    }

    public function messages()
    {
        return [
            // 'name.required' => "",
        ];
    }
}
