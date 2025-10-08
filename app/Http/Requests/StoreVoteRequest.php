<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Die eigentliche Autorisierung erfolgt im Controller mit $this->authorize()
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Nur noch score validieren, rating wird nicht mehr unterstützt
        return [
            'score' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
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
            'score.required' => 'Bitte gib eine Bewertung ab.',
            'score.integer' => 'Die Bewertung muss eine ganze Zahl sein.',
            'score.min' => 'Die Bewertung muss mindestens 1 sein.',
            'score.max' => 'Die Bewertung darf maximal 5 sein.',
        ];
    }
}
