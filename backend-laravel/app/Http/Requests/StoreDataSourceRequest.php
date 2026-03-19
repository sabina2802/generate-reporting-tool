<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDataSourceRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('data_sources', 'name')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            ],
            'connection_string' => [
                'required',
                'string'
            ],
            'type' => [
                'required',
                'string',
                'max:100',
                Rule::in(['mysql', 'postgresql', 'sqlite', 'sqlserver', 'oracle', 'mongodb', 'api', 'csv', 'json'])
            ],
            'user_id' => [
                'required',
                'integer',
                'exists:users,id'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The data source name is required.',
            'name.unique' => 'You already have a data source with this name.',
            'connection_string.required' => 'The connection string is required.',
            'type.required' => 'The data source type is required.',
            'type.in' => 'The selected data source type is invalid.',
            'user_id.required' => 'The user ID is required.',
            'user_id.exists' => 'The specified user does not exist.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (!$this->has('user_id')) {
            $this->merge([
                'user_id' => auth()->id()
            ]);
        }
    }
}