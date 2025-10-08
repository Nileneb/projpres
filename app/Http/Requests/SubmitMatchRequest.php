<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitMatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Autorisierung erfolgt über Policy und weitere Checks im Controller
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
            'submission_url' => ['required', 'url'],
            'submission_notes' => ['nullable', 'string', 'max:1000'],
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
            'submission_url.required' => 'Eine Einreichungs-URL ist erforderlich.',
            'submission_url.url' => 'Bitte gib eine gültige URL ein.',
            'submission_notes.string' => 'Die Anmerkungen müssen ein Text sein.',
            'submission_notes.max' => 'Die Anmerkungen dürfen maximal 1000 Zeichen lang sein.',
        ];
    }
}
