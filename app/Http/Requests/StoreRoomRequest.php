<?php

namespace App\Http\Requests;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRoomRequest extends FormRequest
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
            // 'code' => ['required', 'unique:rooms,code', 'string'],
            'name' => ['required', 'unique:rooms,name', 'string'],
            'description' => ['required',  'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'max_players' => ['required',  'integer'],
            // 'status' => ['required',  'in:active,deactivated,closed'],
            'winners_prize' => ['numeric',  'required'],
            'location_id' => ['required', 'exists:locations,id'],
            'category_id' => ['required', 'exists:categories,id'],
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
