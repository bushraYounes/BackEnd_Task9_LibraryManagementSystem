<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookStoreRequest extends FormRequest
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
            'title' => 'required|string|max:150',
            'isbn' => 'required|string|max:150',
            'edition' => 'nullable|integer|min:0',
            'year' => 'nullable|digits:4|integer|min:1900',
            'price' => 'required|numeric|min:0'
        ];
    }
}
