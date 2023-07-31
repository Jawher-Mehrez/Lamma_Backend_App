<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRoomPlayerRequest extends FormRequest
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
            'score' => ['required', 'integer'],
            'rank' => ['required', 'integer'],
            'room_id' => ['required', 'exists:rooms,id'],
            'user_id' => ['required', 'exists:users,id'],
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
